<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\SubServiceCategory;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\Specialist;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SubServiceCategoryResource;

class CustomerController extends Controller
{

    /**
     * Get nearby service provider list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function nearbyProviders(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'latitude' => [
                'required',
                'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'
            ],
            'longitude' => [
                'required',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'
            ]
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        if ($user) {
            $providers = User::getNearByServiceProviders($request);
            return returnSuccessResponse("NearBy service provider list", $providers);
        }

        return returnErrorResponse('Unable to get NearBy Service provider list.');
    }

    /**
     * Sub Service category list for customer module
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerSubServiceCategory(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => [
                'required',
                'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'
            ],
            'longitude' => [
                'required',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'
            ]
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        // $response = [];
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 10;
        $subServiceCategory = SubServiceCategoryResource::collection(SubServiceCategory::where('service_category_id', $id)->paginate($page_limit));
        // $subServiceCategory = SubServiceCategoryResource::collection(SubServiceCategory::paginate($page_limit));
        // $subServiceCategory = SubServiceCategory::find($id);
        foreach ($subServiceCategory as $category) {
            $response[] = $category->jsonResponse();
        }
         
        // return returnSuccessResponse('Data sent successfully', $subServiceCategory,true);

        return returnSuccessResponse('Data sent successfully', $response,true);
    }

    /**
     * Get recommended Providers provider list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function recommendedProviders(Request $request)
    {
        $user = $request->user();
        $page_no = ! empty($request->query('page')) ? $request->query('page') : 1;

        $validator = Validator::make($request->all(), [
            'latitude' => [
                'required',
                'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'
            ],
            'longitude' => [
                'required',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'
            ]
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        if ($user) {
            $providers = User::getRecommendedProviders($request, $page_no);
            return returnSuccessResponse("recommended providers list", $providers);
        }

        return returnErrorResponse('Unable to get recommended providers list.');
    }

    /**
     * Service Provider Business Details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function businessDetails(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $provider = User::where([
            'id' => $request->provider_id,
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->first();
        if (empty($provider)) {
            return returnErrorResponse("Service provider not found.");
        }
        if ($user) {
            return returnSuccessResponse("service provider business details", $provider->jsonResponseSpecialists(true, true));
        }

        return returnErrorResponse('Unable to get service provider business details.');
    }

    /**
     * Service Provider Business Details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function providerDetails(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $provider = User::where([
            'id' => $request->provider_id,
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->first();
        if (empty($provider)) {
            return returnErrorResponse("Service provider not found.");
        }
        if (! empty($provider->workProfile) && $provider->workProfile->account_type != ACTIVE_STATUS) {
            return returnErrorResponse("This is not a Individual service provider account .");
        }
        if ($user) {
            return returnSuccessResponse("service provider business details", $provider->jsonResponseIndividualProvider());
        }

        return returnErrorResponse('Unable to get service provider business details.');
    }

    
}
