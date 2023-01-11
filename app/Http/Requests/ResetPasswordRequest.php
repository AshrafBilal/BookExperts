<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
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
            'user_id' => 'required',
            'new_password' => 'required',
            'confirm_new_password' => 'required|same:new_password',
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
            'user_id.required' => 'Please enter user id',
            'new_password.required' => 'Please enter the password',
            'confirm_new_password.same' => "Password and confirm password doesn't match",
            'confirm_new_password.required' => 'Please enter confirm password'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
    }
}
