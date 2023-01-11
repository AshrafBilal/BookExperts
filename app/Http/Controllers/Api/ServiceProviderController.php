<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\Service;
use App\Models\SubServiceCategory;
use App\Models\User;
use App\Models\Specialist;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\BookingService;

class ServiceProviderController extends Controller
{

    /**
     * Get Service Provider Profile verification step details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function providerDetailSteps(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $workProfile = @$user->workProfile;
            $name = "Other";
            if (! empty($workProfile->serviceCategory->name)) {
                $name = ucwords($workProfile->serviceCategory->name);
            }
            return returnSuccessResponse("You entered as a $name Service Provider", $user->getProviderDetailStep());
        }

        return returnErrorResponse('Unable to get Provider  details step list.');
    }

    /**
     * Service PRovider add menu
     *
     * @param Request $request
     * @param Service $service
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function addMenu(Request $request, Service $service)
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $request->request->add([
                'user_id' => $user->id
            ]);
            $rules = [
                'sub_service_category_id.*' => 'required',
                'price.*' => 'required',
                'time.*' => 'required'
            ];
            $inputArr = $request->all();
            $validator = Validator::make($inputArr, $rules);
            if ($validator->fails()) {
                $errorMessages = $validator->errors()->all();
                throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
            }
            // return returnErrorResponse('Invalid sub service category Id.',$request->all());
            $subCategories = $request->sub_service_category_id;
            $prices = $request->price;
            $times = $request->time;
            $oldMenus = $request->old_menus;
            $serviceProviders = $request->service_provider_id;
            $service_visits = $request->service_visit;
            $service_image = $request->service_image;

            if (is_array($subCategories) && is_array($prices) && is_array($times)) {
                foreach ($subCategories as $key => $serviceName) {

                    if (! empty($serviceProviders[$key])) {
                        $provider = User::where([
                            'id' => $serviceProviders[$key],
                            'role_id' => SERVICE_PROVIDER_USER_TYPE
                        ])->first();
                        /*
                         * if (empty($provider)) {
                         * return returnErrorResponse('Invalid service provider Id.');
                         * }
                         */
                    }
                    if (! empty($oldMenus[$key])) {
                        $menuId = $oldMenus[$key];
                        $exist = Service::find($menuId);
                        if (! empty($exist)) {
                            $exist->unlinkFile();
                        }
                    }
                    $service = ! empty($exist) ? $exist : new Service();
                    $service->name = $serviceName;
                    $service->user_id = $user->id;
                    $service->time = @$times[$key];
                    $service->price = @$prices[$key];
                    if (! empty($service_image[$key])) {
                        $service->service_image = saveUploadedFile($service_image[$key], 'images');
                    }
                    $service->service_provider_id = @$provider->id;
                    $service->service_visit = @$service_visits[$key];
                    $service->save();
                }
                DB::commit();
                return returnSuccessResponse('Service menu added successfully');
            } else {
                return returnErrorResponse('Post data in not a valid format.');
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            return returnErrorResponse($ex->getMessage());
        }
    }

    /**
     * Service Provider business profile get added provider list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceProvider(Request $request)
    {
        $specialist_id = Specialist::where([
            'user_id' => Auth::id(),
            'state_id' => ACTIVE_STATUS
        ])->pluck('specialist_id')->toArray();
        $serviceProviders = User::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->whereIn('id', $specialist_id)->get();
        $response = [];
        if (! $serviceProviders->isEmpty()) {

            foreach ($serviceProviders as $key => $serviceProvider) {
                $response[] = $serviceProvider->jsonResponse();
            }
        } else {
            return returnSuccessResponse('Data sent successfully', $response, true);
        }

        return returnSuccessResponse('Data sent successfully', $response);
    }

    /**
     * Get added menu details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenu(Request $request)
    {
        // Log::error('error', $request->all());
        $providerId = ! empty($request->query('provider_id')) ? $request->query('provider_id') : null;
        $businessId = ! empty($request->query('business_id')) ? $request->query('business_id') : null;
        $services = Service::orderBy('id', 'asc');

        if (! empty($providerId) && ! empty($businessId)) {
            $services = $services->where([
                'service_provider_id' => $providerId
            ]);
        }
        if (! empty($providerId) && empty($businessId)) {
            $services = $services->where([
                'user_id' => $providerId
            ]);
        }

        if (! empty($businessId)) {
            $services = $services->where([
                'user_id' => $businessId
            ]);
        }

        if (empty($businessId) && empty($providerId)) {
            $services = $services->where([
                'user_id' => Auth::id()
            ]);
        }

        if (! empty($providerId) && ! empty($businessId)) {

            if (! empty($request->query('is_individual'))) {
                $services = Service::where(function ($query) use ($providerId, $businessId) {
                    $query->orWhere('user_id', $providerId);
                    $query->orWhere('service_provider_id', $providerId);
                });
            } else {
                $services = Service::where(function ($query) use ($providerId, $businessId) {
                    $query->orWhere('service_provider_id', $providerId);
                    $query->orWhere('user_id', $businessId);
                    $query->orWhere('service_provider_id', $businessId);
                    $query->orWhere('user_id', $providerId);
                });
            }
        }
        $visit_type = $request->query('visit_type');
        $services = $services->get();
        $response = [];
        if (! empty($services->count())) {
            foreach ($services as $key => $service) {
                $response[] = $service->jsonResponse($visit_type);
            }
        } else {
            return returnSuccessResponse('Data sent successfully', $response, true);
        }
        return returnSuccessResponse('Data sent successfully', $response);
    }

    /**
     * Business service provider add new provider
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function addServiceProvider(Request $request)
    {
        $user = $request->user();
        if (empty($user->workProfile)) {
            return returnErrorResponse('Your work profile not completed.');
        }
        $workProfile = $user->workProfile;
        $rules = [
            'unique_id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $serviceProvider = User::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'unique_id' => $request->unique_id
        ])->first();
        if (empty($serviceProvider)) {
            return returnErrorResponse('Invalid service provider unique id.');
        }

        $existSpecialist = Specialist::where([
            'specialist_id' => $serviceProvider->id,
            'state_id' => ACTIVE_STATUS
        ])->exists();
        $specialistName = $serviceProvider->getFullName();
        if (! empty($existSpecialist)) {
            return returnErrorResponse('This Specialist ' . ucwords($specialistName) . ' already linked with other service provider business profile.');
        }
        $specialist = Specialist::where([
            'specialist_id' => $serviceProvider->id,
            'user_id' => $user->id
        ])->first();
        $specialist = ! empty($specialist) ? $specialist : new Specialist();
        $specialist->specialist_id = $serviceProvider->id;
        $specialist->work_profile_id = $workProfile->id;
        $specialist->state_id = INACTIVE_STATUS;
        $specialist->user_id = $user->id;
        if (! $specialist->save()) {
            return returnErrorResponse('Unable to save');
        }
        Notification::sendNotification([
            'sender_id' => $user->id,
            'receiver_id' => $serviceProvider->id,
            'model' => $specialist,
            'type' => NOTIFICATION_TYPE_SPECIALIST_ADD_BY_BUSINESS,
            'message' => getNotificationMessage(NOTIFICATION_TYPE_SPECIALIST_ADD_BY_BUSINESS)
        ]);
        $specialist_id = Specialist::select('specialist_id')->where([
            'user_id' => Auth::id(),
            'state_id' => ACTIVE_STATUS
        ])
            ->get()
            ->toArray();
        $serviceProviders = User::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->whereIn('id', $specialist_id)->get();
        $response = [];
        if (! empty($serviceProviders->count())) {
            foreach ($serviceProviders as $key => $serviceProvider) {
                $response[] = $serviceProvider->jsonResponse();
            }
        } else {
            return returnSuccessResponse('The request has been sent to this service provider.', $response, true);
        }

        return returnSuccessResponse('The request has been sent to this service provider.', $response);
    }

    /**
     * Delete added menu
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function deleteMenu(Request $request)
    {
        $user = $request->user();
        $rules = [
            'menu_id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $service = Service::where([
            'id' => $request->menu_id,
            'user_id' => $user->id
        ])->first();
        if (! empty($service)) {
            /*
             * $bookServices = BookingService::where([
             * 'service_id' => $service->id
             * ])->exists();
             * if (! empty($bookServices)) {
             * return returnErrorResponse('This service cannot be deleted ,because already booked by some customers.');
             * }
             */
            if ($service->delete()) {
                $services = Service::where([
                    'user_id' => Auth::id()
                ])->get();
                $response = [];
                if (! $services->isEmpty()) {
                    foreach ($services as $key => $service) {
                        $response[] = $service->jsonResponse();
                    }
                } else {
                    $response[] = (object) [];
                }
                return returnSuccessResponse('Menu deleted successfully', $response);
            }
            return returnErrorResponse('Unable to delete menu.');
        }
        return returnErrorResponse('Menu not found.');
    }

    /**
     * Remove service provider form business profile
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function removeServiceProvider(Request $request)
    {
        $user = $request->user();
        if (empty($user->workProfile)) {
            return returnErrorResponse('Your work profile not completed.');
        }
        $rules = [
            'unique_id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $serviceProvider = User::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'unique_id' => $request->unique_id
        ])->first();
        if (empty($serviceProvider)) {
            return returnErrorResponse('Invalid service provider unique id.');
        }
        $specialist = Specialist::where([
            'specialist_id' => $serviceProvider->id,
            'user_id' => $user->id
        ])->first();
        if (! empty($specialist)) {
            if (! $specialist->delete()) {
                return returnErrorResponse('Unable to save');
            }
        } else {
            return returnErrorResponse('Invalid service provider unique id.');
        }
        $specialist_id = Specialist::select('specialist_id')->where([
            'user_id' => Auth::id(),
            'state_id' => ACTIVE_STATUS
        ])
            ->get()
            ->toArray();
        $serviceProviders = User::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->whereIn('id', $specialist_id)->get();
        $response = [];

        if (! empty($serviceProviders->count())) {
            foreach ($serviceProviders as $key => $serviceProvider) {
                $response[] = $serviceProvider->jsonResponse();
            }
        } else {
            return returnSuccessResponse('Service provider deleted successfully.', $response, true);
        }

        return returnSuccessResponse('Service provider deleted successfully.', $response, true);
    }

    /**
     * Remove service provider form business profile
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function deleteWorkProfileImage(Request $request)
    {
        $user = $request->user();
        if (empty($user->workProfile)) {
            return returnErrorResponse('Your work profile not completed.');
        }
        $workProfile = $user->workProfile;
        $rules = [
            'image_id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $image = File::where([
            'id' => $request->image_id,
            'model_id' => $workProfile->id,
            'model_type' => get_class($workProfile),
            'user_id' => $user->id
        ])->first();
        if (! empty($image->file)) {
            $image->delete();
        } else {
            return returnErrorResponse('Image not found.');
        }
        $response = $user->jsonProfileStatusDetails(2);
        return returnSuccessResponse('Image deleted successfully', $response);
    }

    public function UpdateProfile(Request $request)
    {
        $model = $request->user();
        if (! $model) {
            return returnErrorResponse('Unable to Update Your Profile. Please try again later');
        }
        $fullName = $request->first_name . " " . $request->last_name;
        $request->request->add([
            'full_name' => $fullName
        ]);
        $model->fill($request->all());
        if ($model->save()) {
            $response = $model->jsonResponse();
            return returnSuccessResponse('Profile Updated successfully', $response);
        }

        return returnErrorResponse('Unable to Update Your Profile. Please try again later');
    }

    /**
     * Individual accept reject business request
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function acceptRejectBusinessRequest(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'request_id' => 'required',
            'status' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $specialist = Specialist::where([
            'id' => $request->request_id
        ])->first();
        if (empty($specialist)) {
            return returnErrorResponse('Invalid business request id.');
        }
        $specialist->state_id = $request->status;
        if (! $specialist->save()) {
            return returnErrorResponse('Unable to update business request.');
        }
        $message = ! empty($request->status) ? 'Business request accepted.' : 'Business request rejected.';
        if (! empty($request->status)) {
            $user->available_for_home_booking = INACTIVE_STATUS;
            $user->save();
            $message = "Business request accepted.";
            Notification::sendNotification([
                'sender_id' => $user->id,
                'receiver_id' => $specialist->user_id,
                'model' => $specialist,
                'type' => NOTIFICATION_TYPE_SPECIALIST_ACCEPT_BUSINESS_REQUEST,
                'message' => getNotificationMessage(NOTIFICATION_TYPE_SPECIALIST_ACCEPT_BUSINESS_REQUEST)
            ]);
            Specialist::where([
                'specialist_id' => $specialist->specialist_id,
                'state_id' => INACTIVE_STATUS
            ])->delete();
        } else {
            $message = "Business request rejected.";
            Notification::sendNotification([
                'sender_id' => $user->id,
                'receiver_id' => $specialist->user_id,
                'model' => $specialist,
                'type' => NOTIFICATION_TYPE_SPECIALIST_REJECTED_BUSINESS_REQUEST,
                'message' => getNotificationMessage(NOTIFICATION_TYPE_SPECIALIST_REJECTED_BUSINESS_REQUEST)
            ]);
            Specialist::where([
                'specialist_id' => $specialist->specialist_id
            ])->delete();
        }
        return returnSuccessResponse($message);
    }

    public function removeServiceProviderFormBusiness(Request $request)
    {
        $user = $request->user();

        if (empty($user->workProfile)) {
            return returnErrorResponse('Your work profile not completed.');
        }
        $rules = [
            'id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $serviceProvider = User::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'id' => $request->id
        ])->first();
        if (empty($serviceProvider)) {
            return returnErrorResponse('Invalid service provider id.');
        }
        $specialist = Specialist::where([
            'specialist_id' => $serviceProvider->id,
            'user_id' => $user->id
        ])->first();
        if (! empty($specialist)) {
            Notification::sendNotification([
                'sender_id' => $user->id,
                'receiver_id' => $request->id,
                'model' => $specialist,
                'type' => NOTIFICATION_TYPE_BUSINESS_REMOVE_SPECIALIST,
                'message' => getNotificationMessage(NOTIFICATION_TYPE_BUSINESS_REMOVE_SPECIALIST)
            ]);
            if (! $specialist->delete()) {
                return returnErrorResponse('Unable to save');
            }
        } else {
            return returnErrorResponse('Invalid service provider id.');
        }
        $specialist_id = Specialist::select('specialist_id')->where([
            'user_id' => Auth::id(),
            'state_id' => ACTIVE_STATUS
        ])
            ->get()
            ->toArray();
        $serviceProviders = User::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->whereIn('id', $specialist_id)->get();
        $response = [];

        foreach ($serviceProviders as $key => $serviceProvider) {
            $response[] = $serviceProvider->jsonResponse();
        }

        return returnSuccessResponse('The service provider removed you from own business profile successfully.', $response, true);
    }

    public function serviceProviderLeftBusiness(Request $request)
    {
        $user = $request->user();
        if (empty($user->workProfile)) {
            return returnErrorResponse('Your work profile not completed.');
        }
        $rules = [
            'id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $serviceProvider = User::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'id' => $request->id
        ])->first();
        if (empty($serviceProvider)) {
            return returnErrorResponse('Invalid service provider id.');
        }
        $specialist = Specialist::where([
            'user_id' => $serviceProvider->id,
            'specialist_id' => $user->id
        ])->first();
        if (! empty($specialist)) {
            Notification::sendNotification([
                'sender_id' => $user->id,
                'receiver_id' => $request->id,
                'model' => $specialist,
                'type' => NOTIFICATION_TYPE_SPECIALIST_LEAVE_BUSINESS,
                'message' => getNotificationMessage(NOTIFICATION_TYPE_SPECIALIST_LEAVE_BUSINESS)
            ]);
            if (! $specialist->delete()) {
                return returnErrorResponse('Unable to save');
            }
            Service::where([
                'service_provider_id' => $user->id,
                'user_id' => $serviceProvider->id
            ])->update([
                'service_provider_id' => null
            ]);
            return returnSuccessResponse('You have successfully left  from  business.');
        } else {
            return returnErrorResponse('Invalid service provider id.');
        }
    }

    /**
     * Update service provider is available for live booking status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function updateLiveBookingStatus(Request $request)
    {
        $rules = [
            'live_booking_status' => 'required|required|integer|between:0,1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $model = $request->user();
        $model->available_for_live_booking = $request->live_booking_status;
        if ($model->save()) {
            $response = $model->jsonResponse();
            $message = ! empty($request->live_booking_status) ? 'You are now available for live booking.' : 'You have successfully disabled the live booking request.';
            return returnSuccessResponse($message, $response);
        }
        return returnErrorResponse('Unable to Update Your live booking status. Please try again later');
    }

    /**
     * Update service provider is available for live booking status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function updateHomeBookingStatus(Request $request)
    {
        $rules = [
            'home_booking_status' => 'required|required|integer|between:0,1'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
        }
        $model = $request->user();
        $model->available_for_home_booking = (int) $request->home_booking_status;
        if ($model->save()) {
            $response = $model->jsonResponse();
            $message = ! empty($request->live_booking_status) ? 'You are now available for Home booking.' : 'You have successfully disabled the Home booking request.';
            return returnSuccessResponse($message, $response);
        }
        return returnErrorResponse('Unable to Update Your Home booking status. Please try again later');
    }

    /**
     * Service provider update menu price
     *
     * @param Request $request
     * @param Service $service
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function updateMenu(Request $request, Service $service)
    {
        DB::beginTransaction();
        $user = $request->user();
        $request->request->add([
            'user_id' => $user->id
        ]);
        $rules = [
            'sub_service_category_id' => 'required',
            'price' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
        }
        try {
            $subCategories = $request->sub_service_category_id;
            $prices = $request->price;
            if (is_array($subCategories) && is_array($prices)) {
                foreach ($subCategories as $key => $serviceID) {
                    $service = Service::find($serviceID);
                    if (! empty($service)) {
                        if ($user->available_for_home_booking == ACTIVE_STATUS) {
                            $service->home_service_price = @$prices[$key];
                        } else {
                            $service->price = @$prices[$key];
                        }
                        $service->save();
                    } else {
                        return returnErrorResponse('Invalid service menu ID.');
                    }
                }
                DB::commit();
                return returnSuccessResponse('Service menu price updated successfully for home services');
            } else {
                return returnErrorResponse('Post data in not a valid format.');
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            return returnErrorResponse($ex->getMessage());
        }
    }

    public function customerDetails(Request $request)
    {
        $rules = [
            'customer_id' => 'required|exists:users,id,deleted_at,NULL'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
        }
        $model = User::where(['id'=>$request->customer_id,'role_id'=>NORMAL_USER_TYPE])->first();
        if (! $model) {
            return $this->notAuthorizedResponse('User is not authorized');
        }
        $returnArr = $model->customerReviewJsonResponse();
        return returnSuccessResponse('Data sent successfully', $returnArr);
    }
}
