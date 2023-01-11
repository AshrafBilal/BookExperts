<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'register_using'=>'required|boolean',
            'password' => 'required',
            //'confirm_password' => 'required|same:password',
            // 'device_type' => 'required|boolean',
            'fcm_token' => 'required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.required' => 'Please enter the password',
            'password.max' => 'Please enter maximum 255 characters',
            //'confirm_password.same' => "Password and confirm password doesn't match",
            //'confirm_password.required' => 'Please enter confirm password',
            'device_type.required'  => 'Please select device type',
            'device_type.boolean' => 'Invalid device type',
            'fcm_token.required'  => 'Please enter fcm token',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();

        throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));

    }
}
