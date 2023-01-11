<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Validator, Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\WorkProfile;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
use App\Models\SuspendAccount;
use App\Jobs\SendNotificationJob;

class AdminController extends Controller
{

    public $uploadUserProfilePath = 'images';

    /**
     * Admin Login
     *
     * @param Request $request
     * @return unknown|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function login(Request $request)
    {
        if (Auth::check()) {
            if (auth::user()->user_type) {
                return redirect()->route('admin.home');
            }
        }

        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'email' => "required|email:rfc,dns,filter",
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect::back()->withInput()
                    ->withErrors($validator)
                    ->withInput();
            }

            $username = $request->email;
            $password = $request->password;
            $matchCreadentials = [
                'email' => $username,
                'password' => $password,
                'role_id' => ADMIN_USER_TYPE
            ];

            if (Auth::attempt($matchCreadentials)) {
                $ip = $request->ip();
                if (ip2long($ip) !== false) {
                    try {

                        $ipInfo = file_get_contents('http://ip-api.com/json/?ip=' . $ip . "&position=true");
                        $ipInfo = json_decode($ipInfo);
                        $timeZone = @$ipInfo->timezone;

                        config([
                            'app.timezone' => $timeZone
                        ]);
                        if (! empty($timeZone)) {
                            $admin = Auth::user();
                            $admin->time_zone = $timeZone;
                            $admin->save();
                        }
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
                return redirect()->route('admin.home')->with('success', 'Logged in Successfully');
            } else {
                return Redirect::back()->withInput()->with('error', "Email and Password incorrect.");
            }
        }

        return view('admin.users.login');
    }

    /**
     * Get Admin dashboard graph data
     *
     * @param unknown $role
     * @return number[]
     */
    public function createGraphData($role)
    {
        $date = new \DateTime();
        $date->modify('-12  months');
        $count = array();
        for ($i = 1; $i <= 12; $i ++) {
            $date->modify('+1 months');
            $month = $date->format('Y-m');
            if ($role == ADMIN_USER_TYPE) {
                $count[$date->format('F')] = (int) User::where('created_at', 'like', "%$month%")->where('role_id', '!=', $role)->count();
            } else {
                $count[$date->format('F')] = (int) User::where('created_at', 'like', "%$month%")->where('role_id', '=', $role)->count();
            }
        }
        return $count;
    }

    /**
     * Admin Dashboard
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function dashboard()
    {
        $user = Auth::user();
        $userData = $this->createGraphData(NORMAL_USER_TYPE);
        $providerData = $this->createGraphData(SERVICE_PROVIDER_USER_TYPE);
        $totalData = $this->createGraphData(ADMIN_USER_TYPE);

        return view("admin.dashboard.index", compact('user', 'userData', 'providerData', 'totalData'));
    }

    /**
     * Logout admin
     *
     * @return unknown
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    /**
     * Admin update profile
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function adminProfile(Request $request)
    {
        $user = Auth::user();
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
                'email' => "required|email:rfc,dns,filter|unique:users,email,$user->id.NULL,id,deleted_at,NULL",
                'profile_file' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'password' => 'sometimes|nullable|min:6|max:15',
                'phone_number' => 'sometimes|nullable|min:6|max:16',
                'address' => 'required|max:500'
            ], [
                'phone_number.min' => 'The phone number must be at least 6 digits.',
                'phone_number.max' => 'The phone number must not be greater than 16 digits.'
            ]);
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            if (empty($request->latitude) || empty($request->longitude)) {
                return Redirect::back()->withInput()->with('error', "Invalid location address");
            }
            try {
                DB::beginTransaction();
                if ($request->hasFile('profile_file')) {
                    $user->unlinkProfileFile();
                    $upload_profile_pic = saveUploadedFile($request->profile_file, $this->uploadUserProfilePath);
                    if ($upload_profile_pic) {
                        $user->profile_file = $upload_profile_pic;
                    }
                }
                $user->full_name = $request->full_name;
                $user->email = $request->email;
                $user->phone_number = $request->phone_number;
                $user->address = $request->address;
                $user->latitude = $request->latitude;
                $user->longitude = $request->longitude;
                $user->about_me = $request->about_me;
                if (! empty($request->password)) {
                    $user->password = bcrypt($request->password);
                }
                if ($user->save()) {
                    DB::commit();
                    return Redirect::route('admin.home')->withInput()->with('success', "Profile updated successfully.");
                }
            } catch (\Exception $e) {
                DB::rollback();
                return Redirect::back()->withInput()->with('error', $e->getMessage());
            }
        }
        return view('admin.users.admin-profile', compact('user'));
    }

    public function change_password(Request $request)
    {
        $newpass = Hash::make($request->input('newPassword'));
        $profile_data = User::where('id', $request->input('user_id'))->first();

        if (Hash::check($request->input('oldPassword'), $profile_data->password)) {
            if ($request->input('oldPassword') == $request->input('newPassword')) {
                $result = 'Your old password is same as new!';
                return Redirect::back()->withInput()->with('error', $result);
            }

            $credentials = User::find($profile_data->id);
            $credentials->password = $newpass;
            $credentials->save();

            $result = "Your password has been updated Successfully";
            return Redirect::back()->with('success', $result);
        } else {
            $result = "Old password is mismatched ,Your password has failed to update. Please try again!";
        }

        return Redirect::back()->withInput()->with('error', $result);
    }

    public function activateOrSuspendUser(Request $request)
    {
        $data = [];
        $data['status'] = false;
        $data['icon'] = 'error';

        if ($request->ajax() && ! empty($request->user_id)) {
            $user = User::where([
                'id' => $request->user_id
            ])->first();
            if (! empty($user)) {
                $roleTitle = ($user->role_id == SERVICE_PROVIDER_USER_TYPE) ? 'Service Provider' : 'Customer';
                if ($user->active_status == ACTIVE_STATUS) {

                    $user->tokens->each(function ($token, $key) {
                        $token->delete();
                    });
                    $user->fcm_token = null;
                    $user->active_status = INACTIVE_STATUS;
                    $data['message'] = $roleTitle . " account Suspended successfully.";
                } else {
                    $user->active_status = ACTIVE_STATUS;
                    $data['message'] = $roleTitle . " account activated successfully.";
                }
                $user->save();
                dispatch(new SendNotificationJob($user));
                $data['user_status'] = $user->active_status;
                $data['status'] = true;
                $data['icon'] = 'success';
            }
        }
        return response()->json($data);
    }

    public function changeProfileStatus(Request $request)
    {
        $data = [];
        $data['status'] = false;
        $data['icon'] = 'error';

        if ($request->ajax() && ! empty($request->user_id)) {
            $user = User::where([
                'id' => $request->user_id
            ])->first();
            if (! empty($user)) {
                $attribute = $request->attribute;
                if (! empty($attribute)) {

                    $steps = json_decode($user->step_completed, true);

                    $user->$attribute = $request->status;
                    if ($user->profile_identity_file_status == 1 && $user->profile_identity_video_status == 1) {
                        if (! in_array(3, $steps)) {
                            $steps[] = 3;
                        }
                        $user->step_completed = json_encode($steps);
                    } else {
                        foreach ($steps as $key => $step) {
                            if ($step == 3) {
                                unset($steps[$key]);
                            }
                        }
                        $user->step_completed = json_encode($steps);
                    }

                    if ($user->profile_identity_file_status == 1) {
                        $user->identity_file_comment_title = null;
                        $user->identity_file_comment_description = null;
                    }

                    if ($user->profile_identity_video_status == 1) {
                        $user->identity_video_comment_title = null;
                        $user->identity_video_comment_description = null;
                    }

                    if ($user->bank_statement_file_status == 1) {
                        if (! in_array(4, $steps)) {
                            $steps[] = 4;
                        }
                        $user->step_completed = json_encode($steps);
                    } else {
                        $steps = empty($steps) ? [] : $steps;
                        foreach ($steps as $key => $step) {
                            if ($step == 4) {
                                unset($steps[$key]);
                            }
                        }
                        $user->step_completed = json_encode($steps);
                    }
                    $notification_type = getNotificationStatus($attribute, $request->status);
                    Notification::sendNotification([
                        'sender_id' => Auth::id(),
                        'receiver_id' => $user->id,
                        'model' => $user,
                        'type' => $notification_type,
                        'message' => getNotificationTitle($notification_type)
                    ]);
                    $data['message'] = "Status updated successfully.";
                    $user->save();
                    $data['user_status'] = $user->active_status;
                    $data['status'] = true;
                    $data['icon'] = 'success';
                }
            }
        }
        return response()->json($data);
    }

    public function changeWorkProfileStatus(Request $request)
    {
        $data = [];
        $data['status'] = false;
        $data['icon'] = 'error';

        if ($request->ajax() && ! empty($request->user_id)) {
            $user = User::where([
                'id' => $request->user_id
            ])->first();
            if (! empty($user)) {
                $workProfile = WorkProfile::where('user_id', $user->id)->first();
                if (! empty($workProfile)) {

                    $attribute = $request->attribute;
                    if (! empty($attribute)) {

                        $steps = json_decode($user->step_completed, true);
                        $workProfile->$attribute = $request->status;
                        if ($request->status == 2) {
                            $workProfile->reject_description = $request->reject_description;
                            $workProfile->reject_title = $request->reject_title;
                        } else {
                            $workProfile->reject_description = null;
                            $workProfile->reject_title = null;
                        }
                        if ($workProfile->status == 1) {
                            if (! in_array(2, $steps)) {
                                $steps[] = 2;
                            }
                            $user->step_completed = json_encode($steps);
                        } else {
                            foreach ($steps as $key => $step) {
                                if ($step == 2) {
                                    unset($steps[$key]);
                                }
                            }
                            $user->step_completed = json_encode($steps);
                        }

                        $data['message'] = "Status updated successfully.";
                        $user->save();
                        $workProfile->save();
                        if ($workProfile->status == ACTIVE_STATUS) {
                            Notification::sendNotification([
                                'sender_id' => Auth::id(),
                                'receiver_id' => $user->id,
                                'model' => $user,
                                'type' => NOTIFICATION_TYPE_APPROVE_WORK_PROFILE,
                                'message' => getNotificationTitle(NOTIFICATION_TYPE_APPROVE_WORK_PROFILE)
                            ]);
                        } else {
                            Notification::sendNotification([
                                'sender_id' => Auth::id(),
                                'receiver_id' => $user->id,
                                'model' => $user,
                                'type' => NOTIFICATION_TYPE_REJECT_WORK_PROFILE,
                                'message' => getNotificationTitle(NOTIFICATION_TYPE_REJECT_WORK_PROFILE)
                            ]);
                        }
                        $data['user_status'] = $user->active_status;
                        $data['status'] = true;
                        $data['icon'] = 'success';
                    }
                }
            }
        }
        return response()->json($data);
    }

    public function rejectCommon(Request $request)
    {
        $data = [];
        $data['status'] = false;
        $data['icon'] = 'error';
        if ($request->ajax() && ! empty($request->user_id)) {
            $user = User::where([
                'id' => $request->user_id
            ])->first();
            if (! empty($user)) {
                $attribute = $request->attribute;
                $comment = $request->attribute1;
                $commentDescription = $request->attribute2;
                if (! empty($attribute)) {
                    $profileStep = $request->profile_step;
                    if (empty($user->step_completed)) {
                        $user->step_completed = json_encode([]);
                    }
                    $steps = json_decode($user->step_completed, true);

                    $user->$attribute = $request->status;
                    if ($request->status == 2) {
                        $user->$comment = $request->reject_title;
                        $user->$commentDescription = $request->reject_description;
                    } else {
                        $user->$comment = null;
                        $user->$commentDescription = null;
                    }
                    if ($user->status == 1) {
                        if (! in_array($profileStep, $steps)) {
                            $steps[] = $profileStep;
                        }
                        $user->step_completed = json_encode($steps);
                    } else {
                        foreach ($steps as $key => $step) {
                            if ($step == $profileStep) {
                                unset($steps[$key]);
                            }
                        }
                        $user->step_completed = json_encode($steps);
                    }

                    $notification_type = getNotificationStatus($attribute, $request->status);

                    Notification::sendNotification([
                        'sender_id' => Auth::id(),
                        'receiver_id' => $user->id,
                        'model' => $user,
                        'type' => $notification_type,
                        'message' => getNotificationTitle($notification_type)
                    ]);
                    $data['message'] = "Status updated successfully.";

                    $user->save();
                    $data['user_status'] = $user->active_status;
                    $data['status'] = true;
                    $data['icon'] = 'success';
                }
            }
        }
        return response()->json($data);
    }

    public function activateOrSuspendUsers(Request $request)
    {
        $data = [];
        $data['status'] = false;
        $data['icon'] = 'error';

        if ($request->ajax() && ! empty($request->user_id)) {
            $user = User::where([
                'id' => $request->user_id
            ])->first();
            if (! empty($user)) {
                $roleTitle = ($user->role_id == SERVICE_PROVIDER_USER_TYPE) ? 'Service Provider' : 'Customer';
                if ($user->active_status == ACTIVE_STATUS) {

                    $user->tokens->each(function ($token, $key) {
                        $token->delete();
                    });
                    $user->fcm_token = null;
                    $user->active_status = INACTIVE_STATUS;
                    $data['message'] = $roleTitle . " account Suspended successfully.";
                } else {
                    $user->active_status = ACTIVE_STATUS;
                    $data['message'] = $roleTitle . " account activated successfully.";
                }
                $user->save();

                if ($user->active_status == INACTIVE_STATUS) {

                    $suspendAccount = SuspendAccount::where([
                        'user_id' => $user->id
                    ])->first();
                    $suspendAccount = ! empty($suspendAccount) ? $suspendAccount : new SuspendAccount();
                    $suspendAccount->user_id = $user->id;
                    $suspendAccount->status = INACTIVE_STATUS;
                    $date = date('Y-m-d h:i:s');
                    $suspendAccount->activate_date = date('Y-m-d h:i:s', strtotime($date . '+3 day'));
                    $suspendAccount->save();
                    Notification::sendNotification([
                        'sender_id' => Auth::id(),
                        'receiver_id' => $user->id,
                        'model' => $user,
                        'type' => NOTIFICATION_TYPE_ACCOUNT_DEACTIVATED,
                        'message' => getNotificationTitle(NOTIFICATION_TYPE_ACCOUNT_DEACTIVATED)
                    ]);
                } else {
                    Notification::sendNotification([
                        'sender_id' => Auth::id(),
                        'receiver_id' => $user->id,
                        'model' => $user,
                        'type' => NOTIFICATION_TYPE_ACCOUNT_ACTIVE,
                        'message' => getNotificationTitle(NOTIFICATION_TYPE_ACCOUNT_ACTIVE)
                    ]);
                }
                $data['user_status'] = $user->active_status;
                $data['status'] = true;
                $data['icon'] = 'success';
            }
        }
        return response()->json($data);
    }
}
