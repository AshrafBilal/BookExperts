<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;

class ServiceProviderController extends Controller
{

    public function index(Request $request, User $user)
    {
        if ($request->ajax()) {

            $request->request->add([
                'role_id' => SERVICE_PROVIDER_USER_TYPE
            ]);

            $users = $user->getAllCustomers($request);

            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('full_name', function ($user) {
                return $user->getFullName();
            })
                ->addColumn('phone_number', function ($user) {
                return $user->phone_code . "" . $user->phone_number;
            })
                ->addColumn('active_status', function ($user) {
                $status = '';
                if (! empty($user->active_status)) {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" checked >
                    <span class="slider round" data-toggle="tooltip"  title="Suspend"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Active</span>';
                } else {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" >
                    <span class="slider round" data-toggle="tooltip"  title="Active"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Suspend</span>';
                }
                return $status;
            })
                ->addColumn('profile_verified', function ($user) {
                if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_pending">Pending</span>';
                } else if ($user->profile_verified == PROFILE_VERIFICATION_APPROVED) {
                    $status = '<span  class="noHover btn btn-block btn-success custom_status_approved">Approved</span>';
                } else {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_rejected">Rejected</span>';
                }
                return $status;
            })
                ->addColumn('profile_status_action', function ($user) {
                $status = '';
                if (! empty($user->full_name)) {
                    if ($user->profile_verified == PROFILE_VERIFICATION_REJECT) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                    } else if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                        $status .= '<button user_id="' . $user->id . '" type="button" attribute="profile_verified" status="2" attribute1="personal_profile_comment_title" attribute2="personal_profile_comment_description" profile_step="1"  class="reject_common btn btn-block btn-primary custom_status_rejected" message="Are you sure want to change status pending to Reject?">Reject</button>';
                    }
                }
                return $status;
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($user) {
                $btn = '';
                $btn = '<a href="' . route('serviceProviders.show', base64_encode($user->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($user->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

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

        return view('admin.serviceProviders.index');
    }

    public function pendingAccount(Request $request, User $user)
    {
        if ($request->ajax()) {
            $users = User::whereNotNull([
                'profile_identity_video',
                'bank_statement'
            ])->where([
                'role_id' => SERVICE_PROVIDER_USER_TYPE,
                'profile_identity_video_status' => INACTIVE_STATUS,
                'bank_statement_file_status' => INACTIVE_STATUS
            ])
                ->latest()
                ->get();

            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('full_name', function ($user) {
                return $user->getFullName();
            })
                ->addColumn('phone_number', function ($user) {
                return $user->phone_code . "" . $user->phone_number;
            })
                ->addColumn('active_status', function ($user) {
                $status = '';
                if (! empty($user->active_status)) {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" checked >
                    <span class="slider round" data-toggle="tooltip"  title="Suspend"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Active</span>';
                } else {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" >
                    <span class="slider round" data-toggle="tooltip"  title="Active"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Suspend</span>';
                }
                return $status;
            })
                ->addColumn('profile_verified', function ($user) {
                if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_pending">Pending</span>';
                } else if ($user->profile_verified == PROFILE_VERIFICATION_APPROVED) {
                    $status = '<span  class="noHover btn btn-block btn-success custom_status_approved">Approved</span>';
                } else {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_rejected">Rejected</span>';
                }
                return $status;
            })
                ->addColumn('profile_status_action', function ($user) {
                $status = '';
                if (! empty($user->full_name)) {
                    if ($user->profile_verified == PROFILE_VERIFICATION_REJECT) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                    } else if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                        $status .= '<button user_id="' . $user->id . '" type="button" attribute="profile_verified" status="2" attribute1="personal_profile_comment_title" attribute2="personal_profile_comment_description" profile_step="1"  class="reject_common btn btn-block btn-primary custom_status_rejected" message="Are you sure want to change status pending to Reject?">Reject</button>';
                    }
                }
                return $status;
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($user) {
                $btn = '';
                $btn = '<a href="' . route('serviceProviders.show', base64_encode($user->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($user->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

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

        return view('admin.serviceProviders.pending-account');
    }

    public function approvedAccount(Request $request, User $user)
    {
        if ($request->ajax()) {
            $users = User::whereNotNull([
                'profile_identity_video',
                'bank_statement'
            ])->where([
                'role_id' => SERVICE_PROVIDER_USER_TYPE,
                'profile_identity_video_status' => ACTIVE_STATUS,
                'bank_statement_file_status' => ACTIVE_STATUS
            ])
                ->latest()
                ->get();

            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('full_name', function ($user) {
                return $user->getFullName();
            })
                ->addColumn('phone_number', function ($user) {
                return $user->phone_code . "" . $user->phone_number;
            })
                ->addColumn('active_status', function ($user) {
                $status = '';
                if (! empty($user->active_status)) {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" checked >
                    <span class="slider round" data-toggle="tooltip"  title="Suspend"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Active</span>';
                } else {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" >
                    <span class="slider round" data-toggle="tooltip"  title="Active"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Suspend</span>';
                }
                return $status;
            })
                ->addColumn('profile_verified', function ($user) {
                if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_pending">Pending</span>';
                } else if ($user->profile_verified == PROFILE_VERIFICATION_APPROVED) {
                    $status = '<span  class="noHover btn btn-block btn-success custom_status_approved">Approved</span>';
                } else {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_rejected">Rejected</span>';
                }
                return $status;
            })
                ->addColumn('profile_status_action', function ($user) {
                $status = '';
                if (! empty($user->full_name)) {
                    if ($user->profile_verified == PROFILE_VERIFICATION_REJECT) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                    } else if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                        $status .= '<button user_id="' . $user->id . '" type="button" attribute="profile_verified" status="2" attribute1="personal_profile_comment_title" attribute2="personal_profile_comment_description" profile_step="1"  class="reject_common btn btn-block btn-primary custom_status_rejected" message="Are you sure want to change status pending to Reject?">Reject</button>';
                    }
                }
                return $status;
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($user) {
                $btn = '';
                $btn = '<a href="' . route('serviceProviders.show', base64_encode($user->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($user->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

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

        return view('admin.serviceProviders.approved-account');
    }

    public function individualAccount(Request $request, User $user)
    {
        if ($request->ajax()) {
            $users = User::join('work_profiles', 'work_profiles.user_id', '=', 'users.id')->where([
                'role_id' => SERVICE_PROVIDER_USER_TYPE,
                'work_profiles.account_type' => INDIVIDUAL_PROFILE
            ])
                ->select('users.*', 'users.id as id')
                ->latest()
                ->get();

            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('full_name', function ($user) {
                return $user->getFullName();
            })
                ->addColumn('phone_number', function ($user) {
                return $user->phone_code . "" . $user->phone_number;
            })
                ->addColumn('active_status', function ($user) {
                $status = '';
                if (! empty($user->active_status)) {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" checked >
                    <span class="slider round" data-toggle="tooltip"  title="Suspend"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Active</span>';
                } else {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" >
                    <span class="slider round" data-toggle="tooltip"  title="Active"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Suspend</span>';
                }
                return $status;
            })
                ->addColumn('profile_verified', function ($user) {
                if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_pending">Pending</span>';
                } else if ($user->profile_verified == PROFILE_VERIFICATION_APPROVED) {
                    $status = '<span  class="noHover btn btn-block btn-success custom_status_approved">Approved</span>';
                } else {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_rejected">Rejected</span>';
                }
                return $status;
            })
                ->addColumn('profile_status_action', function ($user) {
                $status = '';
                if (! empty($user->full_name)) {
                    if ($user->profile_verified == PROFILE_VERIFICATION_REJECT) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                    } else if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                        $status .= '<button user_id="' . $user->id . '" type="button" attribute="profile_verified" status="2" attribute1="personal_profile_comment_title" attribute2="personal_profile_comment_description" profile_step="1"  class="reject_common btn btn-block btn-primary custom_status_rejected" message="Are you sure want to change status pending to Reject?">Reject</button>';
                    }
                }
                return $status;
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($user) {
                $btn = '';
                $btn = '<a href="' . route('serviceProviders.show', base64_encode($user->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($user->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

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

        return view('admin.serviceProviders.individual-account');
    }

    public function businessAccount(Request $request, User $user)
    {
        if ($request->ajax()) {
            $users = User::join('work_profiles', 'work_profiles.user_id', '=', 'users.id')->where([
                'role_id' => SERVICE_PROVIDER_USER_TYPE,
                'work_profiles.account_type' => BUSINESS_PROFILE
            ])
                ->select('users.*', 'users.id as id')
                ->latest()
                ->get();

            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('full_name', function ($user) {
                return $user->getFullName();
            })
                ->addColumn('phone_number', function ($user) {
                return $user->phone_code . "" . $user->phone_number;
            })
                ->addColumn('active_status', function ($user) {
                $status = '';
                if (! empty($user->active_status)) {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" checked >
                    <span class="slider round" data-toggle="tooltip"  title="Suspend"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Active</span>';
                } else {
                    $status = '<label class="switch">
                    <input type="checkbox" class="active_inactive_toggle" role_title="Service Provider" user_id="' . $user->id . '" value="' . $user->active_status . '" >
                    <span class="slider round" data-toggle="tooltip"  title="Active"></span>
                    </label>';
                    $status .= '<span class="toggle_text"  id="toggle_text_' . $user->id . '"> Suspend</span>';
                }
                return $status;
            })
                ->addColumn('profile_verified', function ($user) {
                if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_pending">Pending</span>';
                } else if ($user->profile_verified == PROFILE_VERIFICATION_APPROVED) {
                    $status = '<span  class="noHover btn btn-block btn-success custom_status_approved">Approved</span>';
                } else {
                    $status = '<span  class="noHover btn btn-block btn-primary custom_status_rejected">Rejected</span>';
                }
                return $status;
            })
                ->addColumn('profile_status_action', function ($user) {
                $status = '';
                if (! empty($user->full_name)) {
                    if ($user->profile_verified == PROFILE_VERIFICATION_REJECT) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                    } else if ($user->profile_verified == PROFILE_VERIFICATION_PENDING) {
                        $status = '<button user_id="' . $user->id . '"   class="approve-application approve_account_btn btn btn-block btn-primary custom_status_approved">Approve</button>';
                        $status .= '<button user_id="' . $user->id . '" type="button" attribute="profile_verified" status="2" attribute1="personal_profile_comment_title" attribute2="personal_profile_comment_description" profile_step="1"  class="reject_common btn btn-block btn-primary custom_status_rejected" message="Are you sure want to change status pending to Reject?">Reject</button>';
                    }
                }
                return $status;
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($user) {
                $btn = '';
                $btn = '<a href="' . route('serviceProviders.show', base64_encode($user->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($user->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

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

        return view('admin.serviceProviders.business-account');
    }

    public function show(Request $request, $id)
    {
        $serviceProvider = User::findOrFail(base64_decode($id));
        return view("admin.serviceProviders.view", compact('serviceProvider'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $serviceProvider = User::find(base64_decode($id));
        if (! $serviceProvider) {
            return returnNotFoundResponse('This Service Provider does not exist');
        }

        $hasDeleted = $serviceProvider->delete();
        if ($hasDeleted) {
            $database = new FirestoreClient([
                'keyFilePath' => __DIR__ . '/firebasekey.json'
            ]);

            $accounts = $database->collection('users')
                ->where('userId', '=', $serviceProvider->id)
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
            return returnSuccessResponse('Service Provider deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }

    public function rejectAccount(Request $request)
    {
        $data = [];
        $data['status'] = false;
        $data['icon'] = 'error';

        if ($request->ajax() && ! empty($request->user_id)) {
            $user = User::where([
                'id' => $request->user_id
            ])->first();
            if (! empty($user)) {
                if (empty($user->step_completed)) {
                    $user->step_completed = json_encode([]);
                }
                $steps = json_decode($user->step_completed, true);
                $steps = empty($steps) ? [] : $steps;
                foreach ($steps as $key => $step) {
                    if ($step == 1) {
                        unset($steps[$key]);
                    }
                }
                $user->step_completed = json_encode($steps);
                $user->profile_verified = PROFILE_VERIFICATION_REJECT;
                $user->profile_reject_comment = $request->profile_reject_comment;
                $user->save();
                $data['message'] = " Profile verification request rejected successfully.";

                $user->save();
                $data['user_status'] = $user->active_status;
                $data['status'] = true;
                $data['icon'] = 'success';
            }
        }
        return response()->json($data);
    }

    public function approveAccount(Request $request)
    {
        $data = [];
        $data['status'] = false;
        $data['icon'] = 'error';

        if ($request->ajax() && ! empty($request->user_id)) {
            $user = User::where([
                'id' => $request->user_id
            ])->first();
            if (! empty($user)) {
                if (empty($user->step_completed)) {
                    $user->step_completed = json_encode([]);
                }
                $steps = json_decode($user->step_completed, true);
                if (! in_array(1, $steps)) {
                    $steps[] = 1;
                }
                $user->step_completed = json_encode($steps);
                $user->profile_verified = PROFILE_VERIFICATION_APPROVED;
                $user->profile_reject_comment = null;
                $user->save();
                Notification::sendNotification([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $user->id,
                    'model' => $user,
                    'type' => NOTIFICATION_TYPE_APPROVED_PERSONAL_PROFILE,
                    'message' => getNotificationTitle(NOTIFICATION_TYPE_APPROVED_PERSONAL_PROFILE)
                ]);

                $data['message'] = "Profile verification request approved successfully.";
                $user->save();
                $data['user_status'] = $user->active_status;
                $data['status'] = true;
                $data['icon'] = 'success';
            }
        }
        return response()->json($data);
    }
}
