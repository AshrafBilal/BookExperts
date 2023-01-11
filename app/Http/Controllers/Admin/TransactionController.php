<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;

class TransactionController extends Controller
{

    public function index(Request $request, Transaction $transaction)
    {
        if ($request->ajax()) {

           

            $transactions = $transaction->getAllTransactions($request);

            return datatables()->of($transactions)
                ->addIndexColumn()
               
                ->addColumn('user_id', function ($transaction) {
                    return @$transaction->user->full_name;
                })
                ->addColumn('status', function ($transaction) {
                    return @$transaction->getPaymentStatus()    ;
                })
                ->addColumn('type', function ($transaction) {
                    return @$transaction->getPaymentType();
                })
                ->addColumn('payment_mode', function ($transaction) {
                    return @$transaction->getPaymentMode();
                })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($transaction) {
                $btn = '';
                $btn = '<a href="' . route('transactions.show', base64_encode($transaction->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($transaction->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action',
                'active_status',
                'profile_verified',
                'profile_status_action'
            ])
                ->make(true);
        }

        return view('admin.transactions.index');
    }

    public function show(Request $request, $id)
    {
        $transaction = Transaction::findOrFail(base64_decode($id));
        return view("admin.transactions.view", compact('transaction'));
    }

  
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productObj = Transaction::find(base64_decode($id));
        
        if(!$productObj){
            return returnNotFoundResponse('This transaction does not exist');
        }
        
        $hasDeleted = $productObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Transaction deleted successfully');
        }
        
        return returnErrorResponse('Something went wrong. Please try again later');
    }
    

  
       
}
