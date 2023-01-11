<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property integer $id
 * @property integer $address_id
 * @property integer $service_provider_id
 * @property integer $user_id
 * @property float $status
 * @property boolean $booking_type
 * @property string $country_code
 * @property string $contact_number
 * @property int $total_quanity
 * @property float $total_amount
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Address $address
 * @property User $user
 * @property User $user
 * @property BookingService[] $bookingServices
 * @property Transaction[] $transactions
 */
class Booking extends Model
{
    use SoftDeletes;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     *
     * @var array
     */
    protected $fillable = [
        'address_id',
        'service_provider_id',
        'user_id',
        'status',
        'booking_type',
        'country_code',
        'contact_number',
        'total_quanity',
        'total_amount',
        'created_at',
        'updated_at',
        'deleted_at',
        'booking_date_time',
        'cancel_type',
        'cancel_reason',
        'business_id',
        'is_live_booking',
        'reject_reason',
        'payment_method',
        'payment_status',
        'user_booking_date_time'
    ];

    public function delete()
    {
        BookingService::where('booking_id', $this->id)->get()->each(function ($booking) {
            $booking->delete();
        });
        Notification::where([
            'model_id' => $this->id,
            'model_type' => get_class($this)
        ])->get()->each(function ($notification) {
            $notification->delete();
        });

        return parent::delete();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo('App\Models\Address');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getBusinessAddress()
    {
        $service_provider_id = ! empty($this->business_id) ? $this->business_id : $this->service_provider_id;
        $address = Address::where([
            'user_id' => $service_provider_id
        ])->first();
        if (! empty($address)) {
            return $address->jsonResponse();
        }
        return (object) [];
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo('App\Models\User', 'service_provider_id')->withTrashed();
    }

    public function businessProvider()
    {
        return $this->belongsTo('App\Models\User', 'business_id')->withTrashed();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookingServices()
    {
        return $this->hasMany('App\Models\BookingService');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'booking_id')->where('created_by', Auth::id());
    }

    public function getAllBookings()
    {
        return self::latest()->get();
    }

    public function getBookingType()
    {
        $list = [
            BOOKING_TYPE_VISIT_HOMES => 'Visit Homes ',
            BOOKING_TYPE_ONLY_AT_WORK_PLACE => 'Only at work place'
        ];
        return ! empty($list[$this->booking_type]) ? $list[$this->booking_type] : null;
    }

    public function getBookingStatus()
    {
        $list = [
            BOOKING_PENDING => 'Pending ',
            BOOKING_ACCEPT => 'Accepted',
            BOOKING_IN_PROGRESS => 'In-progress ',
            BOOKING_COMPLETE => 'Completed ',
            BOOKING_CANCEL => 'Cancelled ',
            BOOKING_REJECT => 'Rejected ',
            BOOKING_ARRIVED => 'Arrived ',
            CANCEL_BY_CUSTOMER => 'Cancelled by customer '
        ];
        return ! empty($list[$this->status]) ? $list[$this->status] : null;
    }

    public function getPaymentMethod()
    {
        $list = [
            COD_PAYMENT_METHOD => 'Cash',
            ONLINE_PAYMENT_METHOD => 'Online'
        ];
        return ! empty($list[$this->payment_method]) ? $list[$this->payment_method] : 'Online';
    }

    public function jsonResponse()
    {
        $json = [];
        $json['id'] = $this->id;
        $json['address_id'] = $this->address_id;
        $json['service_provider_id'] = $this->service_provider_id;
        $json['user_id'] = $this->user_id;
        $json['booking_type'] = $this->booking_type;
        $json['total_quanity'] = $this->total_quanity;
        $json['total_amount'] = custom_number_format($this->total_amount);
        $json['booking_date_time'] = $this->booking_date_time;
        $json['business_id'] = $this->business_id;
        $json['is_rated_by_provider'] = $this->checkServiceIsRateByProvider();
        $json['is_rated_by_customer'] = $this->checkServiceIsRateByCustomer();
        $json['rating'] = $this->getRateByProvider();
        $json['is_live_booking'] = $this->is_live_booking;
        $json['cancel_type'] = $this->cancel_type;
        $json['cancel_reason'] = $this->cancel_reason;
        $json['reject_reason'] = $this->reject_reason;
        $json['cancel_by'] = $this->getCancelByName('full_name');
        $json['cancel_by_id'] = $this->getCancelByName('id');
        $json['payment_method'] = $this->payment_method;
        $json['payment_status'] = $this->payment_status;

        return $json;
    }

    public function getCancelByName($column = 'full_name')
    {
        if (! empty($this->cancel_reason) && $this->status == BOOKING_CANCEL) {
            $created_by = BookingStatusHistory::where([
                'booking_id' => $this->id,
                'service_provider_id' => $this->service_provider_id,
                'status' => BOOKING_CANCEL
            ])->first();
            if (! empty($created_by->createdBy)) {

                return @$created_by->createdBy->$column;
            }
            return null;
        }
    }

    /**
     * this can check customer give rating to all booked services or not
     *
     * @return number
     */
    public function checkServiceIsRateByProvider()
    {
        return (int) Rating::where([
            'booking_id' => $this->id,
            'created_by' => Auth::id()
        ])->exists();
    }

    /**
     * this can check provider give rating customer or not
     *
     * @return number
     */
    public function checkServiceIsRateByCustomer()
    {
        $user = Auth::user();
        if ($user->role_id == NORMAL_USER_TYPE) {
            return (int) Rating::where([
                'booking_id' => $this->id,
                'created_by' => $this->service_provider_id
            ])->whereNull('booking_service_id')->exists();
        } else {
            return (int) Rating::where([
                'booking_id' => $this->id,
                'created_by' => Auth::id()
            ])->whereNull('booking_service_id')->exists();
        }
    }

    public static function getCustomerBooking($request)
    {
        $user = Auth::user();
        $customJson = [];
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 10;
        self::where([
            'user_id' => $user->id,
            'status' => BOOKING_PENDING
        ])->whereDate('booking_date_time', '<', date("Y-m-d"))->update([
            'status' => BOOKING_CANCEL
        ]);
        BookingService::where([
            'user_id' => $user->id,
            'status' => BOOKING_PENDING
        ])->whereDate('booking_date_time', '<', date("Y-m-d"))->update([
            'status' => BOOKING_CANCEL
        ]);

        $query = self::where([
            'user_id' => $user->id
        ]);
        if (! empty($request->query('order_tab')) && $request->query('order_tab') == PAST_ORDER_TAB_FILTER) {
            $query = $query->whereIn('status', [
                BOOKING_COMPLETE,
                BOOKING_CANCEL,
                BOOKING_REJECT
            ])->whereNotIn('status', [
                BOOKING_ACCEPT,
                BOOKING_IN_PROGRESS,
                BOOKING_PENDING,
                BOOKING_ARRIVED
            ]);
        }

        if (! empty($request->query('order_tab')) && $request->query('order_tab') == TODAY_ORDER_TAB_FILTER) {
            $query = $query->whereDate('booking_date_time', '=', date("Y-m-d"))->whereNotIn('status', [
                BOOKING_CANCEL,
                BOOKING_REJECT
            ]);
        }

        if (! empty($request->query('order_tab')) && $request->query('order_tab') == ONGOING_ORDER_TAB_FILTER) {

            $query = $query->whereIn('status', [
                BOOKING_ACCEPT,
                BOOKING_IN_PROGRESS,
                BOOKING_PENDING,
                BOOKING_ARRIVED
            ]);
        }
        if (! empty($request->query('order_tab')) && $request->query('order_tab') == PAST_ORDER_TAB_FILTER) {
            $query->orderBy('booking_date_time', 'desc');
        } else {
            $query->orderBy('booking_date_time', 'asc');
        }
        $query = $query->paginate($page_limit);
        $items = $query->items();
        $finalJson = [];
        foreach ($items as $key => $item) {
            $serviceProvider = $item->serviceProvider;
            $customJson['id'] = $item->id;
            $customJson['order_id'] = $item->order_id;
            $customJson['status'] = $item->status;
            $customJson['service_provider_id'] = $item->service_provider_id;
            $customJson['user_id'] = $item->user_id;
            $customJson['booking_type'] = $item->booking_type;
            $customJson['total_quanity'] = $item->total_quanity;
            $customJson['total_amount'] = custom_number_format($item->total_amount);
            $customJson['booking_date_time'] = $item->booking_date_time;
            $customJson['service_provider_name'] = $serviceProvider->getFullName();
            $customJson['service_provider_profile_file'] = $serviceProvider->profile_file;
            $customJson['booking_date_time'] = $item->booking_date_time;
            $customJson['firebase_chat_token'] = $serviceProvider->firebase_chat_token;
            $customJson['fcm_token'] = $serviceProvider->fcm_token;
            $customJson['business_id'] = $item->business_id;
            $customJson['is_follow'] = $serviceProvider->isFollow();
            $customJson['is_rated_by_customer'] = $item->customerIsRatingServices();
            $customJson['tip_amount'] = $item->isTipByCustomer();
            $customJson['rating'] = $item->getRateByProvider();
            $customJson['address'] = ! empty($item->address) ? $item->address->jsonResponse() : (object) [];
            $customJson['is_live_booking'] = $item->is_live_booking;
            $customJson['cancel_type'] = $item->cancel_type;
            $customJson['cancel_reason'] = $item->cancel_reason;
            $customJson['reject_reason'] = $item->reject_reason;
            $customJson['cancel_by'] = $item->getCancelByName();
            $customJson['cancel_by_id'] = $item->getCancelByName('id');
            $customJson['payment_method'] = $item->payment_method;
            $customJson['payment_status'] = $item->payment_status;
            $booked_services = [];
            if (! empty($item->bookingServices)) {
                foreach ($item->bookingServices as $singleService) {
                    $booked_services[] = $singleService->jsonResponse();
                }
            }
            $customJson['booking_services'] = $booked_services;
            $finalJson[] = $customJson;
        }
        $data['today_order_count'] = self::where([
            'user_id' => $user->id
        ])->whereDate('booking_date_time', '=', date("Y-m-d"))
            ->whereNotIn('status', [
            BOOKING_CANCEL,
            BOOKING_REJECT
        ])
            ->count();
        $data['next_page'] = ($query->nextPageUrl()) ? true : false;
        $data['bookings'] = $finalJson;
        $data['current_page'] = $query->currentPage();
        $data['per_page'] = $query->perPage();
        $data['links'] = $query->linkCollection();
        $data['total'] = $query->total();
        $data['total_pages'] = (int) ceil($query->total() / $query->perPage());
        return $data;
    }

    public function customerIsRatingServices()
    {
        $services = BookingService::where([
            'booking_id' => $this->id,
            'service_provider_id' => $this->service_provider_id,
            'user_id' => Auth::id()
        ])->count();
        $rating = Rating::where([
            'booking_id' => $this->id,
            'service_provider_id' => $this->service_provider_id,
            'created_by' => Auth::id()
        ])->count();
        return (int) ($services == $rating);
    }

    public function customerIsRatingServicesDetails()
    {
        $services = BookingService::where([
            'booking_id' => $this->id,
            'service_provider_id' => $this->service_provider_id
        ])->count();
        $rating = Rating::where([
            'booking_id' => $this->id,
            'service_provider_id' => $this->service_provider_id,
            'created_by' => $this->user_id
        ])->whereNotNull('booking_service_id')->count();
        return (int) ($services == $rating);
    }

    public function isTipByCustomer()
    {
        $tip = Transaction::where([
            'booking_id' => $this->id,
            'type' => TIP_PAYMENT,
            'user_id' => $this->user_id
        ])->value('total_amount');
        if (! empty($tip)) {
            return $tip;
        }
        return INACTIVE_STATUS;
    }

    public static function getServiceProviderOrders($request)
    {
        $specialist_ids = [];
        $customJson = [];
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 10;
        $provider = Auth::user();
        if ($provider->workProfile->account_type == BUSINESS_PROFILE) {
            $specialist_ids = Specialist::where([
                'user_id' => $provider->id,
                'state_id' => ACTIVE_STATUS
            ])->pluck('user_id')->toArray();
        }
        self::where([
            'user_id' => $request->service_provider_id,
            'status' => BOOKING_PENDING
        ])->whereDate('booking_date_time', '<', date("Y-m-d"))->update([
            'status' => BOOKING_CANCEL
        ]);
        BookingService::where([
            'user_id' => $request->service_provider_id,
            'status' => BOOKING_PENDING
        ])->whereDate('booking_date_time', '<', date("Y-m-d"))->update([
            'status' => BOOKING_CANCEL
        ]);

        $query = self::where(function ($query) use ($specialist_ids, $request) {
            $query->where('service_provider_id', $request->service_provider_id);
            $query->orWhereIn('business_id', $specialist_ids);
        });

        if (! empty($request->query('order_tab')) && $request->query('order_tab') == PAST_ORDER_TAB_FILTER) {
            $query = $query->whereIn('status', [
                BOOKING_COMPLETE,
                BOOKING_CANCEL,
                BOOKING_REJECT
            ])->whereNotIn('status', [
                BOOKING_ACCEPT,
                BOOKING_IN_PROGRESS,
                BOOKING_PENDING,
                BOOKING_ARRIVED
            ]);
        }

        if (! empty($request->query('order_tab')) && $request->query('order_tab') == ONGOING_ORDER_TAB_FILTER) {
            $query = $query->whereIn('status', [
                BOOKING_ACCEPT,
                BOOKING_IN_PROGRESS,
                BOOKING_ARRIVED
            ]);
        }

        if (! empty($request->query('order_tab')) && $request->query('order_tab') == CURRENT_REQUEST_ORDER_TAB_FILTER) {
            $query = $query->whereDate('booking_date_time', '>=', date("Y-m-d"))->whereIn('status', [
                BOOKING_ACCEPT,
                BOOKING_IN_PROGRESS,
                BOOKING_ARRIVED,
                BOOKING_PENDING
            ]);
        }

        if (! empty($request->query('order_tab')) && $request->query('order_tab') == TODAY_ORDER_TAB_FILTER) {
            $query = $query->whereDate('booking_date_time', '=', date("Y-m-d"))->whereNotIn('status', [
                BOOKING_CANCEL,
                BOOKING_REJECT
            ]);
        }

        if (! empty($request->query('order_tab')) && $request->query('order_tab') == PAST_ORDER_TAB_FILTER) {
            $query->orderBy('booking_date_time', 'desc');
        } else {
            $query->orderBy('booking_date_time', 'asc');
        }

        $query = $query->paginate($page_limit);
        $items = $query->items();
        $finalJson = [];
        foreach ($items as $key => $item) {
            if (! empty(count($item->bookingServices))) {
                $serviceProvider = $item->serviceProvider;
                $customJson['id'] = $item->id;
                $customJson['order_id'] = $item->order_id;
                $customJson['status'] = $item->status;
                $customJson['service_provider_id'] = $item->service_provider_id;
                $customJson['user_id'] = $item->user_id;
                $customJson['booking_type'] = $item->booking_type;
                $customJson['total_quanity'] = $item->total_quanity;
                $customJson['total_amount'] = custom_number_format($item->total_amount);
                $customJson['booking_date_time'] = $item->booking_date_time;
                $customJson['business_id'] = $item->business_id;
                $customJson['provider_unique_id'] = $serviceProvider->unique_id;
                $customJson['service_provider_name'] = $serviceProvider->getFullName();
                $customJson['service_provider_profile_file'] = $serviceProvider->profile_file;
                $customJson['firebase_chat_token'] = $serviceProvider->firebase_chat_token;
                $customJson['fcm_token'] = $serviceProvider->fcm_token;
                $customJson['booking_date_time'] = $item->booking_date_time;
                $customJson['is_follow'] = $serviceProvider->isFollow();
                $customJson['is_rated_by_provider'] = (int) Rating::where([
                    'booking_id' => $item->id,
                    'created_by' => Auth::id()
                ])->whereNull('booking_service_id')->exists();
                $customJson['tip_amount'] = $item->isTipByCustomer();
                $customJson['rating'] = $item->getRateByProvider();
                $customJson['customer'] = @! empty($item->user) ? $item->user->minimizeJsonResponse() : (object) [];
                $customJson['address'] = ! empty($item->address) ? $item->address->jsonResponse() : (object) [];
                $customJson['is_live_booking'] = $item->is_live_booking;
                $customJson['cancel_type'] = $item->cancel_type;
                $customJson['cancel_reason'] = $item->cancel_reason;
                $customJson['reject_reason'] = $item->reject_reason;
                $customJson['cancel_by'] = $item->getCancelByName();
                $customJson['cancel_by_id'] = $item->getCancelByName('id');
                $customJson['payment_method'] = $item->payment_method;
                $customJson['payment_status'] = $item->payment_status;
                $booked_services = [];
                if (! empty($item->bookingServices)) {
                    foreach ($item->bookingServices as $singleService) {
                        $booked_services[] = $singleService->jsonResponse();
                    }
                }
                $customJson['booking_services'] = $booked_services;
                $finalJson[] = $customJson;
            }
        }
        $data['today_order_count'] = self::where(function ($query) use ($specialist_ids, $request) {
            $query->where('service_provider_id', $request->service_provider_id);
            $query->orWhereIn('business_id', $specialist_ids);
        })->whereDate('booking_date_time', '=', date("Y-m-d"))
            ->whereNotIn('status', [
            BOOKING_CANCEL,
            BOOKING_REJECT
        ])
            ->count();
        $data['next_page'] = ($query->nextPageUrl()) ? true : false;
        $data['bookings'] = $finalJson;
        $data['current_page'] = $query->currentPage();
        $data['per_page'] = $query->perPage();
        $data['links'] = $query->linkCollection();
        $data['total'] = $query->total();
        $data['total_pages'] = (int) ceil($query->total() / $query->perPage());
        return $data;
    }

    public static function getServiceProviderEarnings($request)
    {
        $data = [];
        $specialist_ids = [];
        $customJson = [];
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 10;
        $provider = Auth::user();
        $providerId = $provider->id;
        if ($provider->workProfile->account_type == BUSINESS_PROFILE) {
            $specialist_ids = Specialist::where([
                'user_id' => $provider->id,
                'state_id' => ACTIVE_STATUS
            ])->pluck('user_id')->toArray();
        }

        $query = self::where(function ($query) use ($specialist_ids, $request, $providerId) {
            $query->where('bookings.service_provider_id', $providerId);
            $query->orWhereIn('bookings.business_id', $specialist_ids);
        })->join('transactions', function ($join) {
            $join->on('bookings.id', '=', 'transactions.booking_id')
                ->where('bookings.status', '=', BOOKING_COMPLETE)
                ->where('transactions.type', '=', BOOKING_PAYMENT);
        })
            ->select('*', DB::raw('bookings.id as booking_id,bookings.status AS booking_status'));

        if (empty($request->query('date'))) {
            $query = $query->whereDate('booking_date_time', '=', date("Y-m-d"));
        } else {
            $query = $query->whereDate('booking_date_time', '=', $request->query('date'));
        }
        $tep = [];
        $items = $query->get();
        $finalJson = [];
        $total_amount = 0;
        foreach ($items as $key => $item) {
            $serviceProvider = $item->serviceProvider;
            $tipAmount = (double) Transaction::where([
                'booking_id' => $item->id,
                'type' => TIP_PAYMENT
            ])->value('total_amount');
            $customJson['id'] = $item->id;
            $customJson['total_amount'] = $item->total_amount;
            $customJson['order_id'] = $item->order_id;
            $customJson['booking_id'] = $item->booking_id;
            $customJson['status'] = $item->booking_status;
            $customJson['amount'] = $item->amount;
            $customJson['service_provider_name'] = @$serviceProvider->getFullName();
            $customJson['service_provider_profile_file'] = @$serviceProvider->profile_file;
            $customJson['tip'] = @$tipAmount;
            $customJson['payment_method'] = $item->payment_method;
            $customJson['payment_method_name'] = $item->getPaymentMethod();
            $total_amount = $total_amount + $item->amount;
            $tep[] = $customJson;
        }
        $finalJson['earnings'] = $tep;
        $finalJson['total_amount'] = $total_amount;
        $finalJson['total_order'] = $items->count();
        $data = $finalJson;
        return $data;
    }

    public static function getorderDetailsById($id)
    {
        $customJson = [];
        $item = self::find($id);
        {
            $serviceProvider = $item->serviceProvider;
            $customJson['id'] = $item->id;
            $customJson['order_id'] = $item->order_id;
            $customJson['status'] = $item->status;
            $customJson['service_provider_id'] = $item->service_provider_id;
            $customJson['user_id'] = $item->user_id;
            $customJson['booking_type'] = $item->booking_type;
            $customJson['total_quanity'] = $item->total_quanity;
            $customJson['total_amount'] = custom_number_format($item->total_amount);
            $customJson['booking_date_time'] = $item->booking_date_time;
            $customJson['service_provider_name'] = $serviceProvider->getFullName();
            $customJson['service_provider_profile_file'] = $serviceProvider->profile_file;
            $customJson['firebase_chat_token'] = $serviceProvider->firebase_chat_token;
            $customJson['fcm_token'] = $serviceProvider->fcm_token;
            $customJson['booking_date_time'] = $item->booking_date_time;
            $customJson['business_id'] = $item->business_id;
            $customJson['business_name'] = $item->getBusinessAttribute('business_name');
            $customJson['business_photo'] = $item->getBusinessAttribute('business_photo');
            $customJson['provider_unique_id'] = $serviceProvider->unique_id;
            $customJson['is_follow'] = $serviceProvider->isFollow();
            $customJson['is_rated_by_provider'] = (int) Rating::where([
                'booking_id' => $item->id,
                'created_by' => Auth::id()
            ])->whereNull('booking_service_id')->exists();
            $customJson['is_rated_by_customer'] = $item->customerIsRatingServices();
            $customJson['rating'] = $item->getRateByProvider();
            $customJson['address'] = ! empty($item->address) ? $item->address->jsonResponse() : (object) [];
            $customJson['customer'] = @! empty($item->user) ? $item->user->minimizeJsonResponse() : (object) [];
            $customJson['business_address'] = $item->getBusinessAddress();
            $customJson['is_live_booking'] = $item->is_live_booking;
            $customJson['cancel_type'] = $item->cancel_type;
            $customJson['cancel_reason'] = $item->cancel_reason;
            $customJson['reject_reason'] = $item->reject_reason;
            $customJson['cancel_by'] = $item->getCancelByName();
            $customJson['cancel_by_id'] = $item->getCancelByName('id');
            $customJson['payment_method'] = $item->payment_method;
            $customJson['payment_status'] = $item->payment_status;
            $booked_services = [];
            if (! empty($item->bookingServices)) {
                foreach ($item->bookingServices as $singleService) {
                    $booked_services[] = $singleService->jsonResponse();
                }
            }
            $customJson['booking_services'] = $booked_services;
            return $customJson;
        }
        return false;
    }

    public function getBusinessAttribute($attribute)
    {
        $businessProvider = $this->businessProvider;
        if (! empty($this->business_id) && ! empty($businessProvider)) {
            $workProfile = $businessProvider->WorkProfile;
            if (! empty($workProfile)) {
                switch ($attribute) {
                    case $attribute == 'business_name':
                        return $workProfile->business_name;
                        break;
                    case $attribute == 'business_photo':
                        return $workProfile->getSingleFile();
                        break;
                    default:
                        return null;
                        break;
                }
            }
        }
        return null;
    }

    public function getRateByProvider()
    {
        $user = Auth::user();
        if ($user->role_id == NORMAL_USER_TYPE) {
            $rating = Rating::where([
                'booking_id' => $this->id,
                'user_id' => Auth::id()
            ])->whereNull('booking_service_id')->first();
        } else {
            $rating = Rating::where([
                'booking_id' => $this->id,
                'created_by' => Auth::id()
            ])->whereNull('booking_service_id')->first();
        }

        if (! empty($rating)) {
            return $rating->jsonResponse();
        }
        return (object) [];
    }

    public function getServicesTotalTime()
    {
        $bookedServices = BookingService::where([
            'booking_id' => $this->id
        ])->pluck('service_id')->toArray();
        $time = Service::whereIn('id', $bookedServices)->sum('time');
        if (! empty($time)) {
            return $time;
        }
        return 30;
    }
}
