<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Follow;
use App\Models\User;
use App\Models\Notification;

class FollowController extends Controller
{

    public function followUnfollow(Request $request, Follow $follow)
    {
        $user = $request->user();
        $rules = [
            'provider_id' => 'required',
            'status' => 'required|integer|between:0,1'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        try {
            $request->request->add([
                'follow_by' => $user->id,
                'user_id' => $request->provider_id
            ]);

            $provider = User::getServiceProvider($request->user_id);
            if (empty($provider)) {
                return returnErrorResponse("Service provider not found.");
            }

            $message = ($request->status == INACTIVE_STATUS) ? "You un-follow $provider->full_name" : "You follow $provider->full_name";
            $follow = Follow::where([
                'follow_by' => $user->id,
                'user_id' => $provider->id
            ])->first();
            if (empty($request->status)) {
                if (! empty($follow) && $follow->delete()) {
                    return returnSuccessResponse($message);
                }
            } else {
                $follow = ! empty($follow) ? $follow : new Follow();
                $follow = $follow->fill($request->all());

                if (! $follow->save()) {
                    return returnErrorResponse('Unable to save');
                }
                if ($request->status == ACTIVE_STATUS) {
                    Notification::sendNotification([
                        'sender_id' => $user->id,
                        'receiver_id' => $provider->id,
                        'model' => $follow,
                        'type' => NOTIFICATION_TYPE_FOLLOW_PROVIDER,
                        'message' => ucwords($user->getFullName()) . ' started to follow you .'
                    ]);
                }
                return returnSuccessResponse($message);
            }
            return returnErrorResponse("Service provider not found.");
        } catch (\Exception $e) {
            return returnErrorResponse($e->getMessage());
        }
    }

    /**
     * Get customer following List
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function followingList(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        if ($user) {
            $followings = Follow::getFollowingList($request);
            return returnSuccessResponse("Following list", $followings);
        }

        return returnErrorResponse('Unable to get Following list.');
    }

    /**
     * Get recommended Providers provider list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function providerFollowingList(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'live_booking_status' => 'required'          
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        if ($user) {
            $followings = Follow::providerFollowingList($request);
            return returnSuccessResponse("Following list", $followings);
        }
        
        return returnErrorResponse('Unable to get Following list.');
    }
}
