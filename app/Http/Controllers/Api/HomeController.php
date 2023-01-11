<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\SubServiceCategory;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Http\Resources\ServiceCategoryResource;

class HomeController extends Controller
{

    public function getServiceCategory(Request $request)
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
          $subIds = SubServiceCategory::distinct()->pluck('service_category_id')->toArray();
        $other = ServiceCategory::where('category_type', CATEGORY_TYPE_OTHER)->distinct()
            ->pluck('id')
            ->toArray();
         $subIds = array_merge($subIds, $other);
        $serviceCategory =ServiceCategoryResource::collection( ServiceCategory::whereIn('service_categories.id', $subIds)->orderBy('category_type', 'asc')
            ->orderBy('service_categories.created_at', 'desc')
            ->get());
            // $serviceCategory = ServiceCategoryResource::collection(ServiceCategory::get());

        //  $response = [];
        // foreach ($serviceCategory as $key => $category) {
        //     $response[] = $category->jsonResponse();
        //  }

         return returnSuccessResponse('Data sent successfully', $serviceCategory,true);
        //  return returnSuccessResponse('Data sent successfully', $response,true);
    }

    public function getSubServiceCategory(Request $request)
    {
        $serviceCategoryId = $request->service_category_id;

        if (empty($serviceCategoryId))
            return returnErrorResponse('Please send service category Id.');

        $subServiceCategory = SubServiceCategory::where([
            'service_category_id' => $serviceCategoryId
        ])->paginate(250)->items();

        return returnSuccessResponse('Data sent successfully', $subServiceCategory);
    }

    public function getPage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'page_type' => 'required|integer|between:1,3'
            ]);
            if ($validator->fails()) {
                $errorMessage = $validator->errors()->first();
                throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
            }
            if ($request->page_type == PAGE_TYPE_ABOUT_US) {
                $response['page_url'] = User::getAboutUs();
            } elseif ($request->page_type == PAGE_TYPE_TERMS_AND_CONDITION) {
                $response['page_url'] = User::getTermCondition();
            } elseif ($request->page_type == PAGE_TYPE_PRIVACY_POLICY) {
                $response['page_url'] = User::getPrivacyPolicy();
            }
            return returnSuccessResponse('Data sent successfully', $response);
        } catch (\Exception $e) {
            return returnErrorResponse($e->getMessage());
        }
    }

    /**
     * Contact us API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function contactUs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|min:3',
            'email' => 'required|email|email:rfc,filter',
            'category_type' => 'required|integer|between:1,2',
            'message' => 'required|max:1000'
        ], [
            'full_name.required' => 'Please enter name.',
            'email.required' => 'Please email address.',
            'full_name.min' => 'The name must be at least 3 characters.',
            'message.required' => 'Please enter query.'
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
        }
        try {
            $postData['name'] = $request->full_name;
            if (! empty($postData['name'])) {
                $postData['full_name'] = $request->full_name;
            } else {
                $postData['full_name'] = $request->email;
            }
            $adminEmail = User::where('role_id', ADMIN_USER_TYPE)->value('email');
            $postData['email'] = ! empty(setting('contact_email')) ? setting('contact_email') : $adminEmail;
            $postData['description'] = $request->message;
            $postData['role'] = (@Auth::user()->role_id == NORMAL_USER_TYPE) ? 'Customer' : 'Service Provider';
            $role = $postData['role'];
            $postData['contact_category'] = ($request->category_type == 2) ? 'Suggestion' : 'Feedback';
            $postData['subject'] = env('APP_NAME', 'Just Say What') . ": $role Feedback";
            $postData['layout'] = 'mail.contact_us';
            $mail = emailSend($postData);
            return returnSuccessResponse('We appreciate that you’ve taken the time to write us. We’ll get back to you very soon. Please come back and see us often.');
            
        } catch (\Exception $e) {
            return returnErrorResponse($e->getMessage());
            
        }
    }
}
