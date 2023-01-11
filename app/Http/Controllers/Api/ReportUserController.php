<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\File;
use App\Models\ReportUser;

class ReportUserController extends Controller
{

    public function reportUser(Request $request)
    {
        $rules = [
            'report_to' => 'required|exists:users,id,deleted_at,NULL',
            'report_type' => 'required',
            'comment' => 'sometimes|max:500'
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $user = User::find($request->report_to);
        if (! empty($user)) {
            $request->request->add([
                'report_to' => $user->id,
                'reported_by' => Auth::id(),
                'status' => ACTIVE_STATUS
            ]);
            $report = ReportUser::reportUser($request);
            $loginUser = Auth::user();
            if (! empty($report) && $loginUser->role_id == SERVICE_PROVIDER_USER_TYPE) {
                Notification::sendNotification([
                    'sender_id' => $loginUser->id,
                    'receiver_id' => $user->id,
                    'model' => $report,
                    'type' => NOTIFICATION_TYPE_REPORT_USER_BY_PROVIDER,
                    'message' => "User report submitted successfully",
                    'is_not_save'=>true
                ]);
                return returnSuccessResponse('User report submitted successfully');
            } else {
                return returnErrorResponse('User report not submitted');
            }
        }
        return returnErrorResponse('User not found');
    }
}
