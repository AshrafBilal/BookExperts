<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\WorkProfileRequest;
use App\Http\Resources\ProviderProfileResource;
use App\Models\WorkProfile;
use App\Models\Specialist;
use App\Models\SubServiceCategory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\ProviderTiming;
use App\Models\ProviderOtherCategory;

class AccountController extends Controller
{

    public function changePassword(Request $request)
    {
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_new_password' => 'required|same:new_password'
        ];

        $messages = [
            'old_password.required' => 'Please enter old password',
            'new_password.required' => 'Please enter the password',
            'confirm_new_password.same' => "Password and confirm password doesn't match",
            'confirm_new_password.required' => 'Please enter confirm password'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules, $messages);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $userObj = $request->user();
        if (! $userObj) {
            return notAuthorizedResponse('User is not authorized');
        }

        if (! Hash::check($request->old_password, $userObj->password)) {
            throw new HttpResponseException(returnValidationErrorResponse('Invalid old password'));
        }

        $userObj->password = $request->get('new_password');
        $hasUpdated = $userObj->save();
        if (! $hasUpdated) {
            return returnErrorResponse('Unable to change password');
        }

        $returnArr = $userObj->jsonResponse();
        $returnArr['auth_token'] = $request->bearerToken();
        return returnSuccessResponse('Password updated successfully', $returnArr);
    }

    public function getProfile(Request $request)
    {
        $model = $request->user();
        if (! $model) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $returnArr = $model->jsonResponse();
        $returnArr['auth_token'] = $request->bearerToken();
        return returnSuccessResponse('Data sent successfully', $returnArr);
    }

    /**
     * Get service provider profile
     *
     * @param Request $request
     * @return unknown|\Illuminate\Http\JsonResponse
     */
    public function getProviderProfile(Request $request)
    {
        $model = $request->user();
        if (! $model) {
            return $this->notAuthorizedResponse('User is not authorized');
        }
        $returnArr = $model->jsonResponse(true);
        $returnArr['auth_token'] = $request->bearerToken();
        return returnSuccessResponse('Data sent successfully', $returnArr);
    }

    public function UpdateProfile(Request $request)
    {
        $model = $request->user();

        $oldEmail = $model->email;

        if (! $model) {
            return returnErrorResponse('Unable to Update Your Profile. Please try again later');
        }

        $model = $model->fill($request->all());

        if ($request->hasFile('file')) {
            $model->unlinkProfileFile();
            $model->profile_file = saveUploadedFile($request->file);
        }

        $model->email = $oldEmail;

        if ($model->save()) {
            $model = $model->jsonResponse();
            return returnSuccessResponse('User Profile Updated successfully', $model);
        }

        return returnErrorResponse('Unable to Update Your Profile. Please try again later');
    }

    public function personalProfile(Request $request)
    {
        $user = User::find($request->user_id);
        if (empty($user)) {
            return returnErrorResponse('User not found');
        }
        if ($user->profile_verified == ACTIVE_STATUS || $user->profile_verified == PROFILE_VERIFICATION_REJECT) {
            $rules = [
                'user_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required'
            ];
        } else {
            $rules = [
                'user_id' => 'required',
                'account_type' => 'required',
                'address' => 'required',
                'city' => 'required',
                'email' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'home_services' => 'required',
                'iso_code' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                // 'profile_file' => 'required',
                'phone_number' => 'required',
                'phone_code' => 'required',
                'about_me' => 'required',
                'zip_code' => 'required',
            ];
            if($request->has('home_services') && $request->home_services === true){
                $rules['profile_file'] = 'required';
                $rules['profile_identity_file'] = 'required';
            }
        }

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $user = $user->fill($request->all());

        if (empty($user->step_completed)) {
            $user->step_completed = json_encode([]);
        }

        $completedSteps = json_decode($user->step_completed, true);
        if (! in_array(2, $completedSteps)) {
            array_push($completedSteps, 2);
            $user->step_completed = json_encode($completedSteps);
        }
        if ($request->hasFile('profile_file')) {
            $user->unlinkProfileFile();
            $user->profile_file = saveUploadedFile($request->profile_file);
        }
        if ($request->hasFile('profile_identity_file')) {
            $user->unlinkProfileIdentityFile();
            $user->profile_identity_file = saveUploadedFile($request->profile_identity_file);
            $user->profile_identity_file_status = INACTIVE_STATUS;
        }
        if ($request->hasFile('profile_identity_video')) {
            $user->unlinkProfileVideo();
            $user->profile_identity_video = saveUploadedFile($request->profile_identity_video, 'videos');
            $user->profile_identity_video_status = INACTIVE_STATUS;
        }

        $user->profile_verified = ($user->profile_verified == ACTIVE_STATUS) ? $user->profile_verified == ACTIVE_STATUS : INACTIVE_STATUS;

        $subServiceCategory = $request->sub_service_category_id;

        DB::beginTransaction();

        try {
            if ($request->category_type == CATEGORY_TYPE_OTHER) {

                $subCategoryName = $request->sub_category_name;

                if (empty($subCategoryName)) {
                    return returnValidationErrorResponse('please send sub category name');
                }
                $categories = explode(',', $subCategoryName);

                foreach ($categories as $categoryName) {
                    $exist = SubServiceCategory::where([
                        'name' => $categoryName,
                        'service_category_id' => $request->service_category_id
                    ])->first();
                    if (empty($exist)) {
                        $subCategory = new SubServiceCategory();
                        $subCategory->service_category_id = $request->service_category_id;
                        $subCategory->status = SubServiceCategory::STATUS_PENDING;
                        $subCategory->name = $categoryName;
                        $subCategory->created_by = $user->id;
                        if ($subCategory->save()) {
                            $subServiceCategory[] = $subCategory->id;
                        }
                    } else {
                        $subServiceCategory[] = $exist->id;
                    }
                }
            }

            // if (empty($subServiceCategory) || (! is_array($subServiceCategory))) {
            //     return returnValidationErrorResponse('Please select sub category');
            // }

            $workProfile = WorkProfile::where([
                'user_id' => $request->user_id
            ])->first();
            
            if (empty($workProfile))
                $workProfile = new WorkProfile();

            $workProfile = $workProfile->fill($request->all());
            $workProfile->user_id = $user->id;
            $workProfile->account_type = $request->account_type;
            $workProfile->sub_service_category_id = json_encode($subServiceCategory);
            $workProfile->status = ($workProfile->status == ACTIVE_STATUS) ? $workProfile->status : INACTIVE_STATUS;
            if (! $workProfile->save()) {
                return returnErrorResponse('Unable to save workProfile');
            }
            $subcategories = json_decode($workProfile->sub_service_category_id,true);
    if(!empty($subcategories) )
            foreach ($subcategories as $tempcat) {
                $exist = ProviderOtherCategory::where([
                    'user_id' => $user->id,
                    'sub_service_category_id' => $tempcat
                ])->first();
                
                $model = ! empty($exist) ? $exist : new ProviderOtherCategory();
                $model->user_id = $user->id;
                $model->sub_service_category_id = $tempcat;
                $model->save();
            }
            if ($request->hasFile('file')) {
                saveMultipleFiles($request, $workProfile);
            }
   
            if (empty($user->step_completed)) {
                $user->step_completed = json_encode([]);
            }

            $completedSteps = json_decode($user->step_completed, true);
            if (! in_array(2, $completedSteps)) {

                array_push($completedSteps, 2);

                $user->step_completed = json_encode($completedSteps);

                $user->save();
            }
 

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return returnErrorResponse($e->getMessage());
        }

        if (! $user->save())
            return returnErrorResponse('Unable to save');

        return returnSuccessResponse('Personal profile detail added successfully', new ProviderProfileResource($user));
    }

    public function workProfile(WorkProfileRequest $request)
    {
        $user = User::find($request->user_id);
        if (empty($user))
            return returnErrorResponse('User not found');

        $subServiceCategory = $request->sub_service_category_id;

        DB::beginTransaction();

        try {
            if ($request->category_type == CATEGORY_TYPE_OTHER) {

                $subCategoryName = $request->sub_category_name;

                if (empty($subCategoryName)) {
                    return returnValidationErrorResponse('please send sub category name');
                }
                $categories = explode(',', $subCategoryName);

                foreach ($categories as $categoryName) {
                    $exist = SubServiceCategory::where([
                        'name' => $categoryName,
                        'service_category_id' => $request->service_category_id
                    ])->first();
                    if (empty($exist)) {
                        $subCategory = new SubServiceCategory();
                        $subCategory->service_category_id = $request->service_category_id;
                        $subCategory->status = SubServiceCategory::STATUS_PENDING;
                        $subCategory->name = $categoryName;
                        $subCategory->created_by = $user->id;
                        if ($subCategory->save()) {
                            $subServiceCategory[] = $subCategory->id;
                        }
                    } else {
                        $subServiceCategory[] = $exist->id;
                    }
                }
            }

            if (empty($subServiceCategory) || (! is_array($subServiceCategory))) {
                return returnValidationErrorResponse('Please select sub category');
            }

            $workProfile = WorkProfile::where([
                'user_id' => $request->user_id
            ])->first();

            if (empty($workProfile))
                $workProfile = new WorkProfile();

            $workProfile = $workProfile->fill($request->all());
            $workProfile->user_id = $user->id;
            $workProfile->sub_service_category_id = json_encode($subServiceCategory);
            $workProfile->status = ($workProfile->status == ACTIVE_STATUS) ? $workProfile->status : INACTIVE_STATUS;
            if (! $workProfile->save()) {
                return returnErrorResponse('Unable to save workProfile');
            }
            $subcategories = json_decode($workProfile->sub_service_category_id,true);
            foreach ($subcategories as $tempcat) {
                $exist = ProviderOtherCategory::where([
                    'user_id' => $user->id,
                    'sub_service_category_id' => $tempcat
                ])->first();
                
                $model = ! empty($exist) ? $exist : new ProviderOtherCategory();
                $model->user_id = $user->id;
                $model->sub_service_category_id = $tempcat;
                $model->save();
            }
            if ($request->hasFile('file')) {
                saveMultipleFiles($request, $workProfile);
            }
            if (! empty($request->specialist) && is_array($request->specialist)) {
                foreach ($request->specialist as $key => $value) {
                    $existSpecialist = Specialist::where([
                        'specialist_id' => $value,
                        'state_id' => ACTIVE_STATUS
                    ])->where('user_id', '!=', $user->id)->exists();
                    $specialistName = User::where([
                        'id' => $value
                    ])->value('full_name');
                    if (! empty($existSpecialist)) {
                        return returnErrorResponse('This Specialist ' . ucwords($specialistName) . ' already linked with other service provider business profile.');
                    }
                    $specialist = Specialist::where([
                        'specialist_id' => $value
                    ])->where('user_id', $user->id)->first();
                    $sendNotification = ! empty($specialist) ? false : true;
                    $specialist = ! empty($specialist) ? $specialist : new Specialist();
                    $specialist->specialist_id = $value;
                    $specialist->work_profile_id = $workProfile->id;
                    $specialist->user_id = $user->id;
                    $specialist->state_id = ! empty($specialist->state_id) ? $specialist->state_id : INACTIVE_STATUS;
                    $specialist->save();
                    if (! empty($sendNotification)) {
                        Notification::sendNotification([
                            'sender_id' => $user->id,
                            'receiver_id' => $value,
                            'model' => $specialist,
                            'type' => NOTIFICATION_TYPE_SPECIALIST_ADD_BY_BUSINESS,
                            'message' => getNotificationMessage(NOTIFICATION_TYPE_SPECIALIST_ADD_BY_BUSINESS)
                        ]);
                    }
                }
            }
            if (empty($user->step_completed)) {
                $user->step_completed = json_encode([]);
            }

            $completedSteps = json_decode($user->step_completed, true);
            if (! in_array(2, $completedSteps)) {

                array_push($completedSteps, 2);

                $user->step_completed = json_encode($completedSteps);

                $user->save();
            }

            $status = (int) ProviderTiming::where([
                'user_id' => $user->id
            ])->exists();

            if (empty($status)) {

                $defaultData = json_decode('{"days":[{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]}]}');
                $days = $defaultData->days;
                foreach ($days as $day => $time) {
                    ProviderTiming::where([
                        'day' => $day,
                        'user_id' => $user->id
                    ])->delete();
                    $timeArray = explode('to', $time->time[0]);
                    $start_time = @$timeArray[0];
                    $end_time = @$timeArray[1];
                    $model = new ProviderTiming();
                    $model->day = $day;
                    $model->off_day_type = $time->full_day;
                    $model->start_time = $start_time;
                    $model->end_time = $end_time;
                    $model->user_id = $user->id;
                    $model->save();
                }
            }

            DB::commit();

            return returnSuccessResponse('Work Profile Updated successfully', $workProfile->jsonResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return returnErrorResponse($e->getMessage());
        }
    }

    public function uploadId(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'image' => 'sometimes',
            'video' => 'sometimes'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $user = User::find($request->user_id);

        if (empty($user))
            return returnErrorResponse('User not found');

        if ($request->hasFile('image')) {
            $user->unlinkProfileIdentityFile();
            $user->profile_identity_file = saveUploadedFile($request->image);
            $user->profile_identity_file_status = INACTIVE_STATUS;
        }
        if ($request->hasFile('video')) {
            $user->unlinkProfileVideo();
            $user->profile_identity_video = saveUploadedFile($request->video, 'videos');
            $user->profile_identity_video_status = INACTIVE_STATUS;
        }
        if (empty($user->step_completed)) {

            $user->step_completed = json_encode([]);
        }

        $completedSteps = json_decode($user->step_completed, true);

        if (! in_array(3, $completedSteps)) {

            array_push($completedSteps, 3);

            $user->step_completed = json_encode($completedSteps);
        }
        $user->verification_type = $request->verification_type;
        if ($user->save())
            return returnSuccessResponse('Document uploaded successfully', $user->jsonResponse());

        return returnErrorResponse('Unable to Update Your Work Profile. Please try again later');
    }

    public function uploadBankStatement(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'bank_statement' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $user = User::find($request->user_id);

        if (empty($user))
            return returnErrorResponse('User not found');

        if ($request->hasFile('bank_statement')) {
            $user->unlinkBankStatemenetFile();
            $user->bank_statement = saveUploadedFile($request->bank_statement);
            $user->bank_statement_file_status = INACTIVE_STATUS;
        }

        if (empty($user->step_completed)) {

            $user->step_completed = json_encode([]);
        }

        $completedSteps = json_decode($user->step_completed, true);

        if (! in_array(4, $completedSteps)) {

            array_push($completedSteps, 4);

            $user->step_completed = json_encode($completedSteps);

            $user->save();
        }

        if ($user->save())
            return returnSuccessResponse('Bank Statement Uploaded successfully', $user->jsonResponse());

        return returnErrorResponse('Unable to Update Your Work Profile. Please try again later');
    }

    public function getProviderByUniqueId(Request $request)
    {
        $uniqueId = $request->unique_id;

        if (empty($uniqueId)) {
            return returnErrorResponse('please send unique_id');
        }

        $user = User::where([
            'unique_id' => $uniqueId
        ])->first();
        if (empty($user)) {
            return returnErrorResponse('Service provider not found');
        }
        if (empty($user->workProfile)) {
            return returnErrorResponse('Service provider not found');
        }
        if ($user->workProfile->account_type == BUSINESS_PROFILE) {
            return returnErrorResponse('Please send valid Individual Service provider unique ID.');
        }
        return returnSuccessResponse('Service provider detail sent successfully', $user->jsonResponse());
    }

    public function changeEmail(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'new_email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $user = auth()->user();

        if ($user->email != $request->email) {

            return returnValidationErrorResponse('please enter correct email');
        }

        if (! Hash::check($request->password, $user->password)) {

            return returnValidationErrorResponse('Please enter correct password');
        }

        $oldEmail = $user->email;
        $user->otp = $user->generateOtp();
        $user->save();
        return returnSuccessResponse('Otp Sent successfully', $user->jsonResponse());
    }

    public function verifyChangeEmailOtp(Request $request)
    {
        $rules = [
            'new_email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'otp' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $user = auth()->user();

        if ($user->otp != $request->otp)
            return returnNotFoundResponse('Invalid OTP');

        $user->email = $request->new_email;
        $user->email_verified_at = Carbon::now();
        $user->otp = null;

        if ($user->save())
            return returnSuccessResponse('Email changed successfully', $user->jsonResponse());

        return returnErrorResponse('Unable to verify OTP');
    }

    public function changePhoneNumber(Request $request)
    {
        $rules = [
            'phone_number' => 'required',
            'phone_code' => 'required',
            'iso_code' => 'required',
            'new_phone_number' => "required|unique:users,phone_number,NULL,id,phone_code," . $request->phone_code,
            'password' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $user = auth()->user();

        if ($user->phone_number != $request->phone_number) {

            return returnValidationErrorResponse('please enter correct phone number');
        }

        if (! Hash::check($request->password, $user->password)) {

            return returnValidationErrorResponse('Please enter correct password');
        }

        $oldEmail = $user->email;
        $user->otp = $user->generateOtp();
        $user->save();

        return returnSuccessResponse('Otp Sent successfully', $user->jsonResponse());
    }

    public function verifyChangePhoneNumberOtp(Request $request)
    {
        $rules = [
            'phone_code' => 'required',
            'iso_code' => 'required',
            'new_phone_number' => "required|unique:users,phone_number,NULL,id,phone_code," . $request->phone_code,
            'otp' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $user = auth()->user();

        if ($user->otp != $request->otp)
            return returnNotFoundResponse('Invalid OTP');

        $user->phone_number = $request->new_phone_number;
        $user->phone_code = $request->phone_code;
        $user->iso_code = $request->iso_code;

        $user->otp = null;

        if ($user->save())
            return returnSuccessResponse('Phone number changed successfully', $user->jsonResponse());

        return returnErrorResponse('Unable to verify OTP');
    }

    public function getProfileStatus(Request $request)
    {
        $user = auth()->user();

        if ($user) {

            return returnSuccessResponse('Profile status list.', $user->jsonProfileStatusResponse());
        }

        return returnErrorResponse('Unable to get profile status list');
    }

    public function getProfileStatusDetails(Request $request, $type)
    {
        $user = auth()->user();

        if ($user) {

            return returnSuccessResponse('Profile status details.', $user->jsonProfileStatusDetails($type));
        }

        return returnErrorResponse('Unable to get details');
    }

    public function saveFirebaseChatToken(Request $request)
    {
        $rules = [
            'firebase_chat_token' => 'required',
            'user_id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
        }
        $user = User::find($request->user_id);
        if(!empty($user)){
                $user->firebase_chat_token = $request->firebase_chat_token;
                if ($user->save()){
                    return returnSuccessResponse('Fire-base chat token updated successfully', $user->jsonResponse());
                }
                return returnErrorResponse('Unable to updated fire-base chat token');
            }
             return returnErrorResponse('Invalid user id');
    }
}
