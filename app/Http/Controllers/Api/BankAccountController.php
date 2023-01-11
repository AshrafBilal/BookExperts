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

class BankAccountController extends Controller
{

    public function add(Request $request, BankAccount $bankAccount)
    {
        $user = $request->user();
        $request->request->add([
            'user_id' => $user->id
        ]);
        $rules = [
            'user_id' => 'required',
            'account_holder' => 'required',
            'account_number' => 'required|min:8|max:16',
            'sort_code' => 'required|min:3|max:16',
            [
                'account_number.min' => 'The account number must be at least 8 digits.',
                'account_number.max' => 'The account number must not be greater than 16 digits.'
            ]
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $bankAccount = BankAccount::where('user_id',Auth::id())->first();
        $bankAccount = !empty($bankAccount)?$bankAccount:new BankAccount();
        $result = $bankAccount->fill($request->all());

        if (! $result->save()) {

            return returnErrorResponse('Unable to save');
        }

        return returnSuccessResponse('Bank Account detail added successfully', $result->jsonResponse());
    }
    
    public function getBankAccount(Request $request)
    {
        $BankAccount = BankAccount::where([
            'user_id' => Auth::id()
        ])->first();
        
        if (! empty($BankAccount)){
            return returnSuccessResponse('Bank Account details', $BankAccount->jsonResponse());
        }
        return returnErrorResponse('Bank Account not found');
    }
}
