<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;

class CustomerController extends Controller
{

    public function index(Request $request, User $user)
    {
        if ($request->ajax()) {

            $request->request->add([
                'role_id' => NORMAL_USER_TYPE
            ]);

            $users = $user->getAllCustomers($request);

            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('phone_number', function ($user) {
                return $user->phone_code . $user->phone_number;
            })
                ->addColumn('active_status', function ($user) {
                $status = '';
                if (! empty($user->active_status)) {

                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Customer" user_id="' . $user->id . '" value="' . $user->active_status . '" checked >
                    <span class="slider round" data-toggle="tooltip"  title="Suspend"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Active</span>';
                } else {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Customer" user_id="' . $user->id . '" value="' . $user->active_status . '" >
                    <span class="slider round" data-toggle="tooltip"  title="Active"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Suspend</span>';
                }
                return $status;
            })
                ->addColumn('created_at', function ($user) {
                return changeTimeZone($user->created_at);
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($user) {
                $btn = '';
                $btn = '<a href="' . route('customers.show', base64_encode($user->id)) . '" title="View"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_customer_form"  data-id="' . base64_encode($user->id) . '" class="delete-datatable-record text-danger delete-users-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action',
                'active_status'
            ])
                ->make(true);
        }

        return view('admin.customers.index');
    }

    public function show(Request $request, $id)
    {
        $customer = User::findOrFail(base64_decode($id));
        return view("admin.customers.view", compact('customer'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = User::find(base64_decode($id));

        if (! $customer) {
            return returnNotFoundResponse('This Customer does not exist');
        }

         $hasDeleted = $customer->delete();
       // $hasDeleted = true;
        if ($hasDeleted) {

            $database = new FirestoreClient([
                'keyFilePath' => __DIR__ . '/firebasekey.json'
            ]);

            $accounts = $database->collection('users')
                ->where('userId', '=', $customer->id)
                ->documents();
            if (! $accounts->isEmpty()) {
                foreach ($accounts as $account) {
                    if ($account->exists()) {
                        $serviceAccount = __DIR__ . '/firebasekey.json';
                        $factory = (new Factory())->withServiceAccount($serviceAccount);
                        $auth = $factory->createAuth();
                        $auth->deleteUser($account->id());
                        $database->collection('users')
                            ->document($account->id())
                            ->delete();
                    }
                }
            }
            return returnSuccessResponse('Customer deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }

    /**
     * User Forgot
     *
     * @return unknown
     */
    public function forgotPassword(Request $request)
    {
        $user_id = $request->user_id;
        $token = $request->token;
        $response['id'] = base64_decode($user_id);
        $response['token'] = $token;
        $User = User::where('id', $response['id'])->first();
        if ($User->password_reset_token == $response['token']) {

            return view('common.forgot_password_form', [
                'data' => $response
            ]);
        } else {
            $response['status'] = false;
            return view('common.message', [
                'data' => $response
            ]);
        }
    }

    public function newpassword(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData, [
            'new_password' => 'required|min:6|max:15',
            'confrim_password' => 'required|same:new_password'
        ]);
        if ($validator->fails()) {
            return redirect::back()->withErrors($validator)->withInput();
        }
        $user = User::where('id', $postData['user_id'])->first();
        if ($postData['token_id'] == $user['password_reset_token']) {
            $user['password'] = Hash::make($postData['new_password']);
            $user['id'] = $postData['user_id'];
            $data = User::changePassword($user);

            if ($data) {
                Session::flash('success', "Your Password Reset Successfully.");
                return view('common.success-message');
            } else {
                Session::flash('success', "Something went wrong!");
                return redirect()->back();
            }
        } else {
            Session::flash('success', "Something went wrong!");
            return redirect()->back();
        }
    }

    public function deleteFirebaseUser()
    {
        $database = new FirestoreClient([
            'keyFilePath' => __DIR__ . '/firebasekey.json'
        ]);

        $serviceAccount = __DIR__ . '/firebasekey.json';
        $factory = (new Factory())->withServiceAccount($serviceAccount);
        $auth = $factory->createAuth();
                      $result =  $auth->deleteUser('*');
                        
                      pp($result);
                        
                        $database->collection('users')
                        ->document($account->id())
                        ->delete();
                    
                
          
        
    }
    
}
