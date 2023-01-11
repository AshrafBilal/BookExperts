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
use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{

    /**
     * Get notification list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function notificationList(Request $request)
    {
        $data = Notification::getNotificationList($request);
        return returnSuccessResponse('Notification List', $data);
    }

    /**
     * Update customer or service provider notification setting
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function notificationSetting(Request $request)
    {
        $rules = [
            'notification_status' => 'required_without_all:email_notification|integer|between:0,1',
            'email_notification' => 'required_without_all:notification_status|integer|between:0,1'
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
        }
        $user = Auth::user();
        if (! empty($user)) {
            $user->notification_status = isset($request->notification_status) ? (int) $request->notification_status : $user->notification_status;
            $user->email_notification = isset($request->email_notification) ? (int) $request->email_notification : $user->email_notification;
            if ($user->save()) {
                return returnSuccessResponse('Notification setting updated successfully', $user->jsonResponse());
            }
            return returnErrorResponse('Unable to updated notification setting ');
        }
        return returnErrorResponse('Invalid user id');
    }

    public function sendTestNotification(Request $request)
    {
        $rules = [
            'fcm_token' => 'required',
            'server_key' => 'required'
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
        }
        if ($request->device_type == ANDROID) {
            $androidtoken[] = $request->fcm_token;
        }
        if ($request->device_type == IOS) {
            $iostoken[] = $request->fcm_token;
        }

        $url = 'https://fcm.googleapis.com/fcm/send';
        $server_key = $request->server_key;
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $server_key;
        $header = array(
            "authorization: key=" . $server_key . "",
            "content-type: application/json"
        );
        if (! empty($androidtoken)) {
            try {
                $msg = array(
                    'title' => "Test Notification",
                    'description' => "Test"
                );

                $fields = array(
                    // 'to' => $user->fcm_token, // for single user,
                    'notification' => (object) [],
                    'registration_ids' => $androidtoken, // for multiple users
                    'data' => $msg
                );
                $ch = curl_init();
                $timeout = 120;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                $result = curl_exec($ch);
                if ($result === FALSE) {
                    Log::write('error', 'FCM Send Error: ' . curl_error($ch));
                } else {
                    Log::info('Send Data --' . json_encode($fields));
                }

                curl_close($ch);
            } catch (\Exception $e) {
                Log::write('error', 'android NOTIFICATION SEND ERRROR');
                Log::write('error', $e->getMessage());
            }
        }

        if (! empty($iostoken)) {
            try {

                $data = array(

                    'label' => "Test",
                    'msg' => "Test"
                );

                $notification = array(
                    'title' => "Test",
                    'body' => "Test",
                    'sound' => "default",
                    'badge' => 1
                );

                $arrayToSend = array(
                    'registration_ids' => $iostoken,
                    'notification' => $notification,
                    'data' => $data,
                    'priority' => 'high'
                );
                $json = json_encode($arrayToSend);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Send the request
                $result = curl_exec($ch);

                if ($result === FALSE) {
                    Log::write('error', 'FCM Send Error: ' . curl_error($ch));
                }

                curl_close($ch);
                        // Log::info('Send Data --' . $json);
                        return $result;
                    } catch (\Exception $e) {
                        Log::write('error', 'IOS NOTIFICATION SEND ERRROR');
                        Log::write('error', $e->getMessage());
                    }
                }
                pp($result);
       
        
    }

    
}
