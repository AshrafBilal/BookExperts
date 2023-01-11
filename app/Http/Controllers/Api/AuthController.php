<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\RegisterUsingRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\UserSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{

    public function customerRegister(UserRegisterRequest $request, User $user)
    {
        $registerType = $request->register_using;
        $rules = [
            'phone_code' => 'required',
            'iso_code' => 'required',
            'phone_number' => "required|unique:users,phone_number,NULL,id,deleted_at,NULL,phone_code," . $request->phone_code,
            'first_name' => 'required',
            'last_name' => 'required',
            'register_type' => 'required'
        ];

        $messages = [
            'phone_number.required' => 'Please enter phone number',
            'phone_number.unique' => 'This phone number already exists'
        ];

        if ($registerType == REGISTERED_USING_EMAIL) {
            $rules = [
                'email' => 'required|email||email:rfc,filter|unique:users,email,NULL,id,deleted_at,NULL',
                'first_name' => 'required',
                'last_name' => 'required',
                'register_type' => 'required'
            ];

            $messages = [
                'email.required' => 'Please enter your email',
                'email.unique' => 'This email already exists',
                'email.email' => 'Please enter a valid email'
            ];
        }

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules, $messages);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        try {
            DB::beginTransaction();

            $userArr = $request->except([
                'confirm_password'
            ]);

            if ($registerType == REGISTERED_USING_EMAIL) {
                $userArr['phone_code'] = null;
                $userArr['iso_code'] = null;
                $userArr['phone_number'] = null;
            } else {
                $userArr['email'] = null;
            }

            $model = $user->fill($userArr);
            $model->otp = $model->generateOtp();
            $model->role_id = User::ROLE_USER;

            if (! $model->save()) {
                return returnErrorResponse('Unable to register user. Please try again later');
            }

            if ($registerType == REGISTERED_USING_EMAIL) {
                $postData['email'] = $user->email;
                $postData['full_name'] = $user->full_name;
                $postData['otp'] = $user->otp;
                $postData['subject'] = env('APP_NAME', 'Just Say What') . ": OTP Verification";
                $postData['layout'] = 'mail.otp_verification';
                $sendResult = emailSend($postData);
            } else {
                $sendResult = $user->sendOtpVerificationSms();
            }
            if (! empty($sendResult)) {
                $signupMessage = ($registerType == REGISTERED_USING_EMAIL) ? "email" : "phone number";
                DB::commit();
                return returnSuccessResponse("Please verify your $signupMessage to proceed.", $model->jsonResponse());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return returnErrorResponse($e->getMessage());
        }
    }

    public function providerRegister(UserRegisterRequest $request, User $user)
    {
        $registerType = $request->register_using;

        $rules = [
            'phone_code' => 'required',
            'iso_code' => 'required',
            'register_type' => 'required',
            'phone_number' => "required|unique:users,phone_number,NULL,id,deleted_at,NULL,phone_code," . $request->phone_code
        ];

        $messages = [
            'phone_number.required' => 'Please enter phone number',
            'phone_number.unique' => 'This phone number already exists'
        ];

        if ($registerType == REGISTERED_USING_EMAIL) {
            $rules = [
                'email' => 'required|email||email:rfc,filter|unique:users,email,NULL,id,deleted_at,NULL,deleted_at,NULL',
                'register_type' => 'required'
            ];

            $messages = [
                'email.required' => 'Please enter your email',
                'email.unique' => 'This email already exists',
                'email.email' => 'Please enter a valid email'
            ];
        }

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules, $messages);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        try {
            DB::beginTransaction();
            $userArr = $request->except([
                'confirm_password'
            ]);

            if ($registerType == REGISTERED_USING_EMAIL) {
                $userArr['phone_code'] = null;
                $userArr['iso_code'] = null;
                $userArr['phone_number'] = null;
            } else {
                $userArr['email'] = null;
            }

            $model = $user->fill($userArr);
            $model->otp = $model->generateOtp();
            $model->role_id = User::ROLE_PROVIDER;
            $model->active_status = User::STATUS_PENDING;
            $model->unique_id = $model->generateUniqueId();

            if (! $model->save()) {
                return returnErrorResponse('Unable to register user. Please try again later');
            }
            if ($registerType == REGISTERED_USING_EMAIL) {
                $postData['email'] = $user->email;
                $postData['full_name'] = $user->full_name;
                $postData['otp'] = $user->otp;
                $postData['subject'] = env('APP_NAME', 'Just Say What') . ": OTP Verification";
                $postData['layout'] = 'mail.otp_verification';
                $sendResult = emailSend($postData);
            } else {
                $sendResult = $user->sendOtpVerificationSms();
            }
            if (! empty($sendResult)) {

                $signupMessage = ($registerType == REGISTERED_USING_EMAIL) ? "email" : "phone number";
                DB::commit();
                return returnSuccessResponse("Please verify your $signupMessage to proceed.", $model->jsonResponse());
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return returnErrorResponse($e->getMessage());
        }
    }

    public function resendOtp(Request $request, User $user)
    {
        $rules = [
            'user_id' => 'required|exists:users,id'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        try {

            $model = User::find($request->user_id);
            if (! $model) {
                return returnNotFoundResponse('User not found with this Input');
            }

            $model->otp = $model->generateOtp();
            if (! $model->save()) {
                return returnErrorResponse('Unable to send OTP');
            }
            if ($model->register_using == REGISTERED_USING_EMAIL) {
                $postData['email'] = $model->email;
                $postData['full_name'] = $model->full_name;
                $postData['otp'] = $model->otp;
                $postData['subject'] = env('APP_NAME', 'Just Say What') . ": OTP Verification";
                $postData['layout'] = 'mail.otp_verification';
                $sendResult = emailSend($postData);
            } else {
                $sendResult = $model->sendOtpVerificationSms();
            }
            if (! empty($sendResult)) {
                return returnSuccessResponse('OTP resent successfully', $model->jsonResponse());
            }
            return returnErrorResponse("Unable to send OTP");
        } catch (\Exception $e) {
            return returnErrorResponse($e->getMessage());
        }
    }

    public function verifyOtp(Request $request, User $user)
    {
        $rules = [
            'user_id' => 'required',
            'otp' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $model = User::find($request->user_id);

        if (empty($model)) {
            return returnNotFoundResponse('user not found');
        }
        $model = User::where([
            'id' => $model->id
        ])->whereBetween('updated_at', [
            now()->subMinutes(5),
            now()
        ])
            ->first();
        if (empty($model)) {
            return returnNotFoundResponse('OTP expired ,please click on re-send OTP');
        }
        $model = User::where([
            'id' => $model->id,
            'otp' => $request->otp
        ])->first();

        if (empty($model)) {
            return returnNotFoundResponse('Incorrect OTP');
        }

        $model->otp = null;
        $model->otp_verified = 1;

        if (! $model->save()) {
            return returnErrorResponse('Unable to verify OTP');
        }

        $authToken = $model->createToken('authToken')->plainTextToken;
        $returnArr = $model->jsonResponse();
        $returnArr['auth_token'] = $authToken;
        return returnSuccessResponse('OTP verified successfully', $returnArr);
    }

    public function login(Request $request)
    {
        return 'kdsjflajsf';
        $registerType = $request->register_using;
        $rules = [
            'phone_code' => 'required',
            'iso_code' => 'required',
            'phone_number' => 'required',
            'password' => 'required',
            // 'device_type' => 'required',
            'fcm_token' => 'required',
            'role_id' => 'required'
        ];

        $messages = [
            'phone_number.required' => 'Please enter phone number',
            'phone_number.unique' => 'This phone number already exists'
        ];

        if ($registerType == REGISTERED_USING_EMAIL) {
            $rules = [
                'email' => 'required|email:rfc,filter',
                'password' => 'required',
                // 'device_type' => 'required',
                'fcm_token' => 'required',
                'role_id' => 'required'
            ];

            $messages = [
                'email.required' => 'Please enter your email',
                'email.email' => 'Please enter a valid email'
            ];
        }

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules, $messages);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $model = User::getUser($registerType, $request);

        if (empty($model))
            return returnNotFoundResponse('user not found');

        if ($registerType == REGISTERED_USING_EMAIL) {

            if (! Auth::attempt([
                'email' => $inputArr['email'],
                'password' => $inputArr['password'],
                'role_id' => $request->role_id,
                'deleted_at' => null
            ])) {
                // return if invalid credentails
                return returnNotFoundResponse('Invalid password.');
            }
        }
        if ($registerType == REGISTERED_USING_PHONE) {

            if (! Auth::attempt([
                'phone_number' => $inputArr['phone_number'],
                'phone_code' => $inputArr['phone_code'],
                'password' => $inputArr['password'],
                'deleted_at' => null
            ])) {
                // return if invalid credentails
                return returnNotFoundResponse('Invalid password.');
            }
        }

        $userverify = User::where([
            'id' => $model->id,
            'otp_verified' => OTP_NOT_VERIFIED
        ])->count();

        if ($userverify) {

            $model->otp = $model->generateOtp();

            $model->save();

            return returnError301Response('Please verify your email/phone to proceed.', $model->jsonResponse());
        }

        // $model->device_type = $inputArr['device_type'];
        $model->fcm_token = $inputArr['fcm_token'];
        $model->time_zone = @$inputArr['time_zone'];
        $model->save();
        $model->tokens->each(function ($token, $key) {
            $token->delete();
        });
        if (Auth::check() && Auth::user()->active_status == INACTIVE_STATUS) {
            $message = 'Your account is deactivated. Please contact with administrator.';
            return notAuthorizedResponse($message);
        }
        $authToken = $model->createToken('authToken')->plainTextToken;
        $returnArr = $model->jsonResponse(true);
        $returnArr['auth_token'] = $authToken;

        return returnSuccessResponse('User logged in successfully', $returnArr);
    }

    public function forgotPassword(RegisterUsingRequest $request, User $user)
    {
        $registerType = $request->register_using;

        $rules = [
            'phone_code' => 'required',
            'role_id' => 'required',
            'phone_number' => 'required'
        ];

        $messages = [
            'phone_number.required' => 'Please enter phone number'
        ];

        if ($registerType == REGISTERED_USING_EMAIL) {
            $rules = [
                'email' => 'required|email:rfc,filter',
                'role_id' => 'required'
            ];

            $messages = [
                'email.required' => 'Please enter your email',
                'email.email' => 'Please enter a valid email'
            ];
        }

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules, $messages);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $model = User::getUser($registerType, $request);

        if (empty($model)) {
            return returnNotFoundResponse('User not found.');
        }

        $model->otp = $model->generateOtp();
        $model->save();
        if ($model->register_using == REGISTERED_USING_EMAIL) {
            $postData['email'] = $model->email;
            $postData['full_name'] = $model->full_name;
            $postData['otp'] = $model->otp;
            $postData['subject'] = env('APP_NAME', 'Just Say What') . ": OTP Verification";
            $postData['layout'] = 'mail.otp_verification';
            $sendResult = emailSend($postData);
        } else {
            $sendResult = $model->sendOtpVerificationSms();
        }
        if (! empty($sendResult)) {
            return returnSuccessResponse('Reset password OTP sent successfully', $model->jsonResponse());
        }
    }

    public function verifyResetPasswordOtp(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'otp' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $userObj = User::where('id', $inputArr['user_id'])->where('otp', $inputArr['otp'])->first();
        if (! $userObj) {
            return returnNotFoundResponse('Invalid reset password OTP');
        }

        $userObj->otp = null;
        $userObj->save();

        return returnSuccessResponse('Otp verified successfully');
    }

    public function resetPassword(ResetPasswordRequest $request, User $user)
    {
        $inputArr = $request->all();
        $userObj = User::where('id', $inputArr['user_id'])->first();
        if (! $userObj) {
            return returnNotFoundResponse('Invalid reset password OTP');
        }
        $userObj->password = $inputArr['new_password'];
        $userObj->save();

        return returnSuccessResponse('Password reset successfully');
    }

    public function logout(Request $request)
    {
        $userObj = $request->user();
        if (! $userObj) {
            return notAuthorizedResponse('You are not authorized');
        }

        $userObj->tokens()->delete();
        $userObj->fcm_token = null;
        $userObj->save();
        return returnSuccessResponse('User logged out successfully');
    }

    public function socialLogin(Request $request, User $user)
    {
        $rules = [
            'social_token' => 'required',
            // 'device_type' => 'required|integer|between:0,1',
            'register_type' => 'required',
            'fcm_token' => 'required',
            'role_id' => 'required|integer|between:2,3'
        ];
        if (! empty($request->input('full_name'))) {
            $parts = explode(" ", $request->input('full_name'));
            if (count($parts) > 1) {
                $lastname = array_pop($parts);
                $firstname = implode(" ", $parts);
            } else {
                $firstname = $request->input('full_name');
                $lastname = " ";
            }
            $request->request->add([
                'first_name' => $firstname,
                'last_name' => $lastname,
                'firebase_chat_token' => $request->input('social_token')
            ]);
        }
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($validateerror[0]));
        }

        $userObj = $user->fill($inputArr);

        if (! $userObj) {
            return returnErrorResponse('Unable to register user. Please try again later');
        }
        $userToken = $request->get('social_token');
        $isExistUser = User::where('social_token', $userToken)->whereNull('deleted_at')->first();
        if ($isExistUser) {

            if ($request->has('fcm_token')) {
                $isExistUser->fill([
                    'fcm_token' => $request->input('fcm_token'),
                    'role_id' => $request->input('role_id'),
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'register_type' => $request->input('register_type'),
                    'social_token' => $request->input('social_token'),
                    'firebase_chat_token' => $request->input('social_token'),
                    'time_zone' => ! empty($request->input('time_zone')) ? $request->input('time_zone') : $isExistUser->time_zone
                ]);
                $isExistUser->save();
            }

            $authToken = $isExistUser->createToken('authToken')->plainTextToken;
            $returnArr = $isExistUser->jsonResponse();
            $returnArr['auth_token'] = $authToken;
            return returnSuccessResponse('User logged in successfully', $returnArr);
        }

        // Social login when user visit first time
        $rules = [
            // 'full_name' => 'sometimes',
            'social_token' => 'required|unique:users,social_token,NULL,id,deleted_at,NULL',
            // 'device_type' => 'required|integer|between:0,1',
            'register_type' => 'required|integer|between:1,5',
            'email' => 'sometimes|email||email:rfc,filter|unique:users,email,NULL,id,deleted_at,NULL,deleted_at,NULL'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($validateerror[0]));
        }
        $userObj = $user->fill($inputArr);
        if (! $userObj->save()) {
            return returnErrorResponse('Unable to register user. Please try again later');
        }
        $authToken = $userObj->createToken('authToken')->plainTextToken;
        $returnArr = $userObj->jsonResponse();
        $returnArr['auth_token'] = $authToken;
        
        return returnSuccessResponse('Your account created successfully.', $returnArr);
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            
        ]);

        if($validator->fails()){
            returnErrorResponse('Unable to register user. Please try again later');
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['full_name'] =  $user->full_name;
        $success['first_name'] =  $user->first_name;
        $success['last_name'] =  $user->last_name;
        return returnSuccessResponse('Your account created successfully.', $success);

    }
    public function userLogin(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['full_name'] =  $user->full_name;
            // $success['first_name'] =  $user->first_name;
            // $success['last_name'] =  $user->last_name;

            return returnSuccessResponse('User logged in successfully.', $success);

        }
        else{
            // return returnErrorResponse('Unauthorised.');
            return notAuthorizedResponse('You are not authorized');
        } 
    }
}
