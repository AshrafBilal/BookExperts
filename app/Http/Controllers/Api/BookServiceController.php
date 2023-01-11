<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Follow;
use App\Models\User;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\NumberUtility;
use App\Models\ProviderTiming;
use App\Models\Notification;
use App\Models\BookingStatusHistory;
use Stripe\Stripe;
use App\Models\Transaction;

class BookServiceController extends Controller
{

    private $stripe;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    /**
     * Customer book a service
     *
     * @param Request $request
     * @param Follow $follow
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function bookService(Request $request)
    {
        $user = $request->user();
        $rules = [
            'payment_method' => 'required|integer|between:1,2',
            'booking_date_time' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        try {

            DB::beginTransaction();
            $data = $request->data;
            $bookingDate = $request->booking_date_time;
            if (is_array($data)) {
                foreach ($data as $key => $bookService) {
                    $service = Service::find($bookService['id']);
                    if (! empty($service)) {

                        $service_visit = @$bookService['service_visit'];
                        $bookingStatus = ($service_visit == BOOKING_TYPE_ONLY_AT_WORK_PLACE) ? BOOKING_ARRIVED : BOOKING_PENDING;
                        $service_provider_id = ! empty($request->provider_id) ? $request->provider_id : $service->user_id;
                        if (empty($key)) {
                            $booking = new Booking();
                            $booking->total_quanity = @$bookService['quantity'];
                            $booking->address_id = ! empty($request->address_id) ? $request->address_id : null;
                            $booking->user_id = $user->id;
                            $booking->total_amount = $service->getServicePrice($request->service_visit);
                            $booking->status = $bookingStatus;
                            $booking->service_provider_id = $service_provider_id;
                            $booking->booking_date_time = $bookingDate;
                            $booking->booking_type = @$bookService['service_visit'];
                            $booking->business_id = @$request->business_id;
                            $booking->payment_method = @$request->payment_method;
                            $booking->save();
                        }

                        $bookingService = new BookingService();
                        $bookingService->booking_id = $booking->id;
                        $bookingService->status = $bookingStatus;
                        $bookingService->total_quanity = @$bookService['quantity'];
                        $bookingService->total_amount = ($bookService['quantity'] * $service->getServicePrice($request->service_visit));
                        $bookingService->price_per_unit = $service->getServicePrice($request->service_visit);
                        $bookingService->service_provider_id = $service_provider_id;
                        $bookingService->user_id = $user->id;
                        $bookingService->booking_type = @$bookService['service_visit'];
                        $bookingService->booking_date_time = $bookingDate;
                        $bookingService->service_id = $service->id;
                        $bookingService->business_id = @$request->business_id;
                        $bookingService->save();
                    } else {
                        return returnErrorResponse("Invalid service ID.");
                    }
                }
                $totalAmount = BookingService::where('booking_id', $booking->id)->sum('total_amount');
                $totalQuantity = BookingService::where('booking_id', $booking->id)->sum('total_quanity');
                $booking->total_quanity = $totalQuantity;
                $booking->order_id = $this->getUniqueOrderNumber($booking->id);
                $booking->total_amount = $totalAmount;
                $booking->is_live_booking = ! empty($request->is_live_booking) ? ACTIVE_STATUS : INACTIVE_STATUS;
                $booking->save();
                if (! empty($booking->business_id)) {

                    $serviceProvider = User::find($booking->business_id);
                } else {
                    $serviceProvider = User::find($booking->service_provider_id);
                }
                /*
                 * if (empty($serviceProvider->stripe_connect_id)) {
                 * return returnErrorResponse('This service provider is not connected with the stripe.');
                 * }
                 */

                $adminAmount = $adminAmount = $providerAmount = 0;
                $adminCommission = setting('admin_commission');
                if (! empty($adminCommission)) {
                    $adminAmount = ($booking->total_amount * $adminCommission / 100);
                    $providerAmount = $booking->total_amount - $adminAmount;
                } else {
                    $providerAmount = $booking->total_amount;
                }

                if ($request->payment_method == ONLINE_PAYMENT_METHOD) {
                    if (! empty($request->card_id)) {
                        if (! empty($serviceProvider->stripe_connect_id)) {
                            $charge = \Stripe\Charge::create(array(
                                "amount" => $providerAmount * 100,
                                "currency" => 'gbp',
                                "customer" => $user->stripe_id,
                                "source" => $request->card_id,
                                "destination" => $serviceProvider->stripe_connect_id,
                                "capture" => "false",
                                "application_fee_amount" => $adminAmount * 100,
                                "description" => "Booking payment of order ID " . $booking->order_id
                            ));
                        } else {
                            $charge = \Stripe\Charge::create(array(
                                "amount" => $booking->total_amount * 100,
                                "currency" => 'gbp',
                                "customer" => $user->stripe_id,
                                "source" => $request->card_id,
                                "capture" => "false",
                                "description" => "Booking payment of order ID " . $booking->order_id
                            ));
                        }

                        if ($charge) {
                            $Transaction = new Transaction();
                            $Transaction->user_id = Auth::id();
                            $Transaction->booking_id = @$booking->id;
                            $Transaction->transaction_id = @$charge['id'];
                            $Transaction->type = BOOKING_PAYMENT;
                            $Transaction->amount = $providerAmount;
                            $Transaction->total_amount = $booking->total_amount;
                            $Transaction->commission_amount = $adminAmount;
                            $Transaction->card_id = $request->card_id;
                            $Transaction->status = PAYMENT_PENDING;
                            $Transaction->payment_mode = ONLINE_PAYMENT_METHOD;
                            $Transaction->created_at = date('Y-m-d H:i:s');
                            $Transaction->save();
                        }
                    }
                } else {
                    $Transaction = new Transaction();
                    $Transaction->user_id = Auth::id();
                    $Transaction->booking_id = @$booking->id;
                    $Transaction->transaction_id = date("dmY") . "_" . time();
                    $Transaction->type = BOOKING_PAYMENT;
                    $Transaction->amount = $providerAmount;
                    $Transaction->total_amount = $booking->total_amount;
                    $Transaction->commission_amount = $adminAmount;
                    $Transaction->card_id = null;
                    $Transaction->status = PAYMENT_PENDING;
                    $Transaction->created_at = date('Y-m-d H:i:s');
                    $Transaction->payment_mode = COD_PAYMENT_METHOD;
                    $Transaction->save();
                }

                if (! empty($bookingService->business_id)) {
                    Notification::sendNotification([
                        'sender_id' => $user->id,
                        'receiver_id' => $booking->business_id,
                        'model' => $booking,
                        'type' => getOrderNotificationType(BOOKING_PENDING),
                        'message' => "You have received new booking request for order - ". $booking->order_id."."
                    ]);
                }

                Notification::sendNotification([
                    'sender_id' => $user->id,
                    'receiver_id' => $booking->service_provider_id,
                    'model' => $booking,
                    'type' => getOrderNotificationType(BOOKING_PENDING),
                    'message' => "You have received new booking request for order - ". $booking->order_id."."
                ]);

                Notification::sendNotification([
                    'sender_id' => $booking->service_provider_id,
                    'receiver_id' => $user->id,
                    'model' => $booking,
                    'type' => getOrderNotificationType(BOOKING_PENDING),
                    'message' => "You have created a new booking request for order - ". $booking->order_id."."
                ]);
                DB::commit();

                return returnSuccessResponse("Your request Service booked successfully.", [
                    'order_id' => $booking->id
                ]);
            } else {
                return returnErrorResponse("Invalid data format");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return returnErrorResponse($e->getMessage());
        }
    }

    public function getUniqueOrderNumber($id)
    {
        $randomNumber = NumberUtility::getRandomNumber(8);
        $order_id = "#" . (string) $id . $randomNumber;
        $exist = Booking::where('order_id', $order_id)->first();
        if ($exist) {
            $order_id = $this->getUniqueInvoiceNumber($id);
        }
        return $order_id;
    }

    /**
     * Get Customer booking request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function myBookings(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'address1' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'default_address' => 'required',
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }


        if ($user) {
            $booking = Booking::getCustomerBooking($request);
            return returnSuccessResponse("booking request list", $booking);
        }

        return returnErrorResponse('Unable to get booking request.');
    }

    /**
     * check Provider Availability
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function checkProviderAvailability(Request $request)
    {
        $user = $request->user();
        $rules = [
            'provider_id' => 'required',
            'booking_date_time' => 'required',
            'booked_services' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        if ($user) {

            if (! is_array($request->booked_services)) {
                return returnErrorResponse('Invalid Data format.');
            }

            $checkAvailability = ProviderTiming::checkAvailability($request);
            if (! empty($checkAvailability)) {
                return returnSuccessResponse("Service provider available for your selected booking time slot.");
            } else {
                return returnErrorResponse('Service provider not available for your selected booking time slot, please select a different time slot.');
            }
        }

        return returnErrorResponse('Unable to get booking request.');
    }

    /**
     * Get Service provider received orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function myOrders(Request $request)
    {
        // $provider_id = $request->provider_id;
   
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required'
        ]);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        if ($user) {
            $request->request->add([
                'service_provider_id' => $user->id
            ]);
            $order = Booking::getServiceProviderOrders($request);
            return returnSuccessResponse("Order request list", $order);
        }
        return returnErrorResponse('Unable to get Order request.');
    }

    /**
     * Get Customer booking details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function bookingDetails(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'booking_id' => 'required',
            'address1' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'default_address' => 'required',
            'address_id' => 'required',
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $booking = Booking::where([
            'id' => $request->booking_id
        ])->first();
        if (! empty($user) && !empty($booking)) {

            $order = Booking::getorderDetailsById($booking->id);
            if (! empty($order)) {
                return returnSuccessResponse("Booking details", $order);
            } else {
                return returnErrorResponse('No Booking detail found.');
            }
        } else {
            return returnErrorResponse('No Booking detail found.');
        }
        return returnErrorResponse('Unable to get Booking detail.');
    }

    /**
     * Get Service provider order details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function orderDetails(Request $request)
    {
        $rules = [
            'booking_id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $booking = Booking::where([
            'id' => $request->booking_id
        ])->first();
        if (! empty($booking)) {

            $order = Booking::getorderDetailsById($booking->id);
            if (! empty($order)) {
                return returnSuccessResponse("Order details", $order);
            } else {
                return returnErrorResponse('No Order detail found.');
            }
        } else {
            return returnErrorResponse('No Order detail found.');
        }
        return returnErrorResponse('Unable to get Order detail.');
    }

    /**
     * Service provider update order status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function updateOrderStatus(Request $request)
    {
        $serviceProvider = Auth::user();
        if ($request->status == BOOKING_CANCEL) {

            $rules = [
                'booking_service_id' => 'required',
                'cancel_type' => 'required',
                'cancel_reason' => 'required'
            ];
        } else {
            $rules = [
                'booking_service_id' => 'required',
                'status' => 'required|integer|between:1,6'
            ];
        }
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        DB::beginTransaction();
        try {

            $booking = Booking::where([
                'id' => $request->booking_service_id
            ])->whereNotIn('status', [
                BOOKING_REJECT,
                BOOKING_COMPLETE,
                BOOKING_CANCEL
            ])->first();
            if (! empty($booking)) {

                /*
                 * if ($request->status == BOOKING_IN_PROGRESS && time() < strtotime($booking->booking_date_time)) {
                 * return returnErrorResponse("You can't start service before the order start time.");
                 * }
                 */

                if ($request->status == BOOKING_CANCEL) {
                    $booking->cancel_type = $request->cancel_type;
                    $booking->cancel_reason = $request->cancel_reason;
                }
                if ($request->status == BOOKING_REJECT) {
                    $booking->reject_reason = $request->reject_reason;
                }
                $checkStatus = BookingStatusHistory::saveStatusHistory($booking, $request->status);
                $booking->status = $request->status;
                $booking->save();
                if (empty($checkStatus)) {
                    return returnErrorResponse('Order status cannot be reverted or updated again.');
                }
                $transaction = Transaction::where('booking_id', $booking->id)->first();
                if ($request->status == BOOKING_COMPLETE) {
                    if (! empty($transaction->transaction_id) && $transaction->payment_mode == ONLINE_PAYMENT_METHOD) {
                        $this->stripe->charges->capture($transaction->transaction_id);
                        $transaction->status = PAYMENT_SUCCESS;
                        $transaction->save();
                    } else {
                        $transaction->status = PAYMENT_SUCCESS;
                        $transaction->save();
                        $booking->payment_status = ACTIVE_STATUS;
                        $booking->save();
                    }
                }
                $order = Booking::getorderDetailsById($booking->id);
                if ($order) {
                    DB::commit();

                    $message = getOrderStatusMessage($booking->status, @$booking->user->role_id);
                    Notification::sendNotification([
                        'sender_id' => $serviceProvider->id,
                        'receiver_id' => $booking->user_id,
                        'model' => $booking,
                        'type' => getOrderNotificationType($booking->status),
                        'message' => $message
                    ]);
                    $message = getOrderStatusMessage($booking->status, @$serviceProvider->role_id);

                    Notification::sendNotification([
                        'sender_id' => $booking->user_id,
                        'receiver_id' => $serviceProvider->id,
                        'model' => $booking,
                        'type' => getOrderNotificationType($booking->status),
                        'message' => $message
                    ]);
                    if ($request->status == BOOKING_COMPLETE) {
                        $message1 = "How was your order from " . $serviceProvider->getFullName() . " ? Tap to rate and review.";
                        Notification::sendNotification([
                            'sender_id' => $serviceProvider->id,
                            'receiver_id' => $booking->user_id,
                            'model' => $booking,
                            'type' => NOTIFICATION_TYPE_RATE_SERVICE,
                            'message' => $message1
                        ]);
                        $customer = User::find($booking->user_id);
                        $message2 = "How was your experience with customer " . $customer->getFullName() . " ? Tap to rate and review.";
                        Notification::sendNotification([
                            'sender_id' => $customer->id,
                            'receiver_id' => $serviceProvider->id,
                            'model' => $booking,
                            'type' => NOTIFICATION_TYPE_RATE_CUSTOMER,
                            'message' => $message2
                        ]);
                    }
                    return returnSuccessResponse($message, $order);
                } else {
                    return returnErrorResponse('No Order detail found.');
                }
            } else {
                return returnErrorResponse('No Order detail found.');
            }
            return returnErrorResponse('Unable to get Order detail.');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnErrorResponse($e->getMessage());
        }
    }

    /**
     * Customer pay additional tip amount to service provider
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function addTip(Request $request)
    {
        $rules = [
            'booking_id' => 'required',
            'tip_amount' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $booking = Booking::where([
            'id' => $request->booking_id
        ])->first();
        if (! empty($booking)) {
            if ($booking->status == BOOKING_COMPLETE) {
                $user = Auth::user();
                $card_id = $user->getDefaultStripeCardID();
                if (! empty($booking->business_id)) {
                    $serviceProvider = User::find($booking->business_id);
                } else {
                    $serviceProvider = User::find($booking->service_provider_id);
                }
                if (empty($serviceProvider->stripe_connect_id)) {
                    return returnErrorResponse('This service provider is not connected with the stripe.');
                }

                if (! empty($card_id)) {
                    $charge = \Stripe\Charge::create(array(
                        "amount" => $request->tip_amount * 100,
                        "currency" => 'gbp',
                        "customer" => $user->stripe_id,
                        "source" => $card_id,
                        "destination" => $serviceProvider->stripe_connect_id,
                        "capture" => "true",
                        "description" => "Tip payment of order ID " . $booking->order_id
                    ));
                    if ($charge) {
                        $exists = Transaction::where([
                            'booking_id' => $booking->id,
                            'type' => TIP_PAYMENT
                        ])->exists();
                        if (empty($exists)) {
                            $Transaction = new Transaction();
                            $Transaction->user_id = Auth::id();
                            $Transaction->booking_id = @$booking->id;
                            $Transaction->transaction_id = @$charge['id'];
                            $Transaction->type = TIP_PAYMENT;
                            $Transaction->amount = $request->tip_amount;
                            $Transaction->total_amount = $request->tip_amount;
                            $Transaction->commission_amount = null;
                            $Transaction->card_id = $card_id;
                            $Transaction->status = PAYMENT_SUCCESS;
                            $Transaction->created_at = date('Y-m-d H:i:s');
                            $Transaction->save();
                            $order = Booking::getorderDetailsById($booking->id);
                            if (! empty($order)) {
                                return returnSuccessResponse("Add tip for provider successfully.");
                            } else {
                                return returnErrorResponse('No Order detail found.');
                            }
                        } else {
                            return returnErrorResponse('Tip already added for this order.');
                        }
                    }
                } else {
                    return returnErrorResponse('The customer selected payment card is invalid, please update your default payment card and try again.');
                }
            } else {
                return returnErrorResponse('Add Tip allowed only for completed orders.');
            }
        } else {
            return returnErrorResponse('No Order detail found.');
        }
        return returnErrorResponse('Unable to get Order detail.');
    }

    /**
     * Get Service provider earnings list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function myEarnings(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $order = Booking::getServiceProviderEarnings($request);
            return returnSuccessResponse("My earnings list", $order);
        }
        return returnErrorResponse('Unable to get My earnings request.');
    }

    /**
     * Service provider cancel
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function customerCancelBooking(Request $request)
    {
        $customer = Auth::user();
        $rules = [
            'booking_service_id' => 'required',
            'cancel_type' => 'required',
            'cancel_reason' => 'required'
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        DB::beginTransaction();
        try {

            $booking = Booking::where([
                'id' => $request->booking_service_id,
                'status' => BOOKING_PENDING
            ])->first();
            if (! empty($booking)) {
                $request->request->add([
                    'status' => BOOKING_CANCEL
                ]);
                $booking->cancel_type = $request->cancel_type;
                $booking->cancel_reason = $request->cancel_reason;
                $booking->status = BOOKING_CANCEL;
                $checkStatus = BookingStatusHistory::saveStatusHistory($booking, $request->status);
                $booking->status = $request->status;
                $booking->save();
                if (empty($checkStatus)) {
                    return returnErrorResponse('Order status cannot be reverted or updated again.');
                }
                $order = Booking::getorderDetailsById($booking->id);
                if ($order) {
                    Notification::sendNotification([
                        'sender_id' => $customer->id,
                        'receiver_id' => $booking->service_provider_id,
                        'model' => $booking,
                        'type' => getOrderNotificationType($booking->status),
                        'message' => "Booking request cancelled by customer."
                    ]);

                    Notification::sendNotification([
                        'sender_id' => $booking->service_provider_id,
                        'receiver_id' => $customer->id,
                        'model' => $booking,
                        'type' => getOrderNotificationType($booking->status),
                        'message' => "Your booking request  canceled successfully."
                    ]);
                    DB::commit();
                    return returnSuccessResponse("Booking request canceled  successfully", $order);
                } else {
                    return returnErrorResponse('No Order detail found.');
                }
            } else {
                return returnErrorResponse('No Order detail found.');
            }
            return returnErrorResponse('Unable to get Order detail.');
        } catch (\Exception $e) {
            DB::rollBack();
            return returnErrorResponse($e->getMessage());
        }
    }
    
    
}
