<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;

class WorkProfileRequest extends FormRequest
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
            'user_id'=>'required',
            'category_type'=>'required|integer|min:1|max:2',
            'service_category_id'=>'required',
            //'sub_service_category_id' => 'required',
            'account_type' => 'required|integer|min:1|max:2'
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
            'account_type.required' => 'Please enter business type'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();

        throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));

    }
}
