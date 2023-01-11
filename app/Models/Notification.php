<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Swift_Mailer;
use Swift_SmtpTransport;
use PHPMailer\PHPMailer\PHPMailer;

/**
 *
 * @property integer $id
 * @property integer $sender_id
 * @property integer $receiver_id
 * @property integer $model_id
 * @property string $model_type
 * @property string $type
 * @property string $message
 * @property string $read
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property User $user
 */
class Notification extends Model
{

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'model_id',
        'model_type',
        'type',
        'message',
        'read',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }

    public function jsonResponse()
    {
        $json = [];
        $json['id'] = $this->id;
        $json['sender_id'] = $this->sender_id;
        $json['receiver_id'] = $this->receiver_id;
        $json['notification_type'] = $this->type;
        $json['order_id'] = $this->getOrderID();
        $json['customer_id'] = $this->getCustomerID();
        $json['provider_id'] = $this->getProviderID();
        $json['role_id'] = $this->getProviderRoleID();
        $json['account_type'] = $this->getProviderAccountType();
        $json['post_id'] = $this->getPostID();
        $json['message'] = $this->message;
        $json['read'] = $this->read;
        $json['time_ago'] = @$this->created_at->diffForHumans();
        $json['created_at'] = @$this->created_at->toDateTime()->format('Y-m-d H:i:s');
        $createdBy = $this->sender;
        $json['sender_image'] = @$createdBy->profile_file;
        $json['sender_name'] = @$createdBy->full_name;
        return $json;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'receiver_id');
    }

    /*
     * protected $appends = [
     * 'time_ago'
     * ];
     *
     * public function getTimeAgoAttribute()
     * {
     * return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']))->diffForHumans();
     * }
     */
    public static function getMyNotifications($request)
    {
        $notifications = self::where('receiver_id', $request->receiver_id)->latest()->paginate(10);
        $data['next_page'] = ($notifications->nextPageUrl()) ? true : false;
        $data['notifications'] = $notifications->items();
        return $data;
    }

    public static function getMyWebNotifications($request)
    {
        return self::with('sender:id,name,username,profile_pic', 'post:id,type,url,thumbnail,created_at')->where('receiver_id', $request->receiver_id)
            ->latest()
            ->get();
    }

    public static function markRead($request)
    {
        $notification = self::whereIn('id', explode(',', $request->notification_id))->where('receiver_id', $request->receiver_id)
            ->where('read', NOTIFICATION_UN_READ)
            ->update([
            'read' => NOTIFICATION_READ
        ]);
        if (! empty($notification)) {
            return true;
        }
        return false;
    }

    public static function markReadNotification($request)
    {
        Notification::where('id', $request->notification_id)->where('receiver_id', $request->receiver_id)
            ->where('read', NOTIFICATION_UN_READ)
            ->update([
            'read' => NOTIFICATION_READ
        ]);

        return Notification::where('receiver_id', $request->receiver_id)->where('read', NOTIFICATION_UN_READ)->count();
    }

    public static function markAllRead($request)
    {
        $notification = self::where('receiver_id', $request->receiver_id)->where('read', NOTIFICATION_UN_READ)->update([
            'read' => NOTIFICATION_READ
        ]);
        if (! empty($notification)) {
            return true;
        }
        return false;
    }

    public static function deleteNotification($request)
    {
        if ($request->notification_id) {
            $notification = self::where('receiver_id', $request->receiver_id)->where('id', $request->notification_id)->first();
            if (! empty($notification)) {
                return $notification->delete();
            }
        }
        return false;
    }

    public static function sendNotification($param = [])
    {
        $notification = Notification::where([
            'sender_id' => $param['sender_id'],
            'receiver_id' => $param['receiver_id'],
            'model_id' => $param['model']->id,
            'model_type' => get_class($param['model']),
            'type' => $param['type']
        ])->first();
        $notification = empty($notification) ? new Notification() : $notification;
        $notification->sender_id = ! empty($param['sender_id']) ? $param['sender_id'] : Auth::id();
        $notification->receiver_id = $param['receiver_id'];
        $notification->model_id = $param['model']->id;
        $notification->model_type = get_class($param['model']);
        $notification->type = isset($param['type']) ? $param['type'] : null;
        $notification->message = isset($param['message']) ? preg_replace('/[\s$@_*]+/', ' ', $param['message']) : null;
        $notification->read = NOTIFICATION_UN_READ;
        if (! $notification->save()) {
            return false;
        }

        $receiver_id = User::find($notification->receiver_id);
        if (! empty($receiver_id->email) && ! empty($receiver_id->email_notification)) {
            $notification->mail_send_status = INACTIVE_STATUS;
            $notification->save();
        }
        if (! empty($receiver_id->notification_status)) {
            $notification->sendNotificationOnApp($param);
        }

        return true;
    }

    public function sendMail()
    {
        /*
         * $smtpConnection = $this->checkSmtpConnection();
         * if (empty($smtpConnection)) {
         * return true;
         * }
         */
        $loginUser = Auth::user();
        $message = $this->message;
        $title = getNotificationTitle($this->type);
        $user = User::where('id', $this->receiver_id)->first();
        $customer = User::where([
            'id' => $this->sender_id,
            'role_id' => NORMAL_USER_TYPE
        ])->first();
        if (empty($customer)) {
            $customer = User::where([
                'id' => $this->receiver_id,
                'role_id' => NORMAL_USER_TYPE
            ])->first();
        }
        $serviceProvider = User::where([
            'id' => $this->sender_id,
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->first();
        if (empty($serviceProvider)) {
            $serviceProvider = User::where([
                'id' => $this->receiver_id,
                'role_id' => SERVICE_PROVIDER_USER_TYPE
            ])->first();
        }
        if (! empty($user)) {
            $postData['full_name'] = $user->getFullName();
            if (@$loginUser->role_id == NORMAL_USER_TYPE) {
                $postData['customer_name'] = @! empty($customer) ? $customer->getFullName() : null;
            }

            if (@$loginUser->role_id == SERVICE_PROVIDER_USER_TYPE) {
                $postData['provider_name'] = @! empty($serviceProvider) ? $serviceProvider->getFullName() : null;
            }
            $postData['email'] = $user->email;
            $postData['title'] = $title;
            $postData['description'] = $message;
            $postData['subject'] = env('APP_NAME', 'Just Say What') . ": $title ";
            $postData['layout'] = 'mail.notification';
            $mail = emailSend($postData);
        } else {
            Log::write('error', 'No User Found');
        }
        return true;
    }

    public function checkSmtpConnection()
    {
        try {
            $mail = new PHPMailer(true);
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->Host = env('MAIL_HOST');
            $mail->Port = env('MAIL_PORT');

            // This function returns TRUE if authentication
            // was successful, or throws an exception otherwise
            $validCredentials = $mail->SmtpConnect();
            if (! empty($validCredentials)) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCustomerID()
    {
        $sender = User::find($this->sender_id);
        if (! empty($sender) && $sender->role_id == NORMAL_USER_TYPE) {
            return $sender->id;
        }
        $receiver = User::find($this->receiver_id);
        if (! empty($receiver) && $receiver->role_id == NORMAL_USER_TYPE) {
            return $receiver->id;
        }
    }

    public function getProviderID()
    {
        $sender = User::find($this->sender_id);
        if (! empty($sender) && $sender->role_id == SERVICE_PROVIDER_USER_TYPE) {
            return $sender->id;
        }
        $receiver = User::find($this->receiver_id);
        if (! empty($receiver) && $receiver->role_id == SERVICE_PROVIDER_USER_TYPE) {
            return $receiver->id;
        }
    }

    public function getProviderRoleID()
    {
        $sender = User::find($this->sender_id);
        if (! empty($sender) && $sender->role_id == SERVICE_PROVIDER_USER_TYPE) {
            return $sender->role_id;
        }
        $receiver = User::find($this->receiver_id);
        if (! empty($receiver) && $receiver->role_id == SERVICE_PROVIDER_USER_TYPE) {
            return $receiver->role_id;
        }
    }

    public function getProviderAccountType()
    {
        $sender = User::find($this->sender_id);
        if (! empty($sender) && $sender->role_id == SERVICE_PROVIDER_USER_TYPE) {
            $workProfile = $sender->workProfile;
            if (! empty($workProfile)) {
                return $workProfile->account_type;
            }
        }
        $receiver = User::find($this->receiver_id);
        if (! empty($receiver) && $receiver->role_id == SERVICE_PROVIDER_USER_TYPE) {
            $workProfile = @$sender->workProfile;
            if (! empty($workProfile)) {
                return $workProfile->account_type;
            }
        }
    }

    public function getOrderID()
    {
        if (! empty($this->model_type)) {
            $orderObject = new $this->model_type();
            if (is_object($orderObject) && $orderObject instanceof Booking) {
                return $this->model_id;
            }
        }
        return null;
    }

    public function getPostID()
    {
        if (! empty($this->model_type)) {
            $orderObject = new $this->model_type();
            if (is_object($orderObject) && $orderObject instanceof Like) {
                $like = Like::find($this->model_id);
                return @$like->post->id;
            }
            if (is_object($orderObject) && $orderObject instanceof Comment) {
                $comment = Comment::find($this->model_id);
                return @$comment->post->id;
            }
            if (is_object($orderObject) && $orderObject instanceof CommentLike) {
                $comment = CommentLike::find($this->model_id);
                return @$comment->post->id;
            }
        }
        return null;
    }

    public function sendNotificationOnApp($param = [])
    {
        $currentAction = Route::currentRouteAction();
        $currentAction = ! empty($currentAction) ? $currentAction : @$param['current_action'];
        $arrayEnd = explode('@', $currentAction);
        $action = end($arrayEnd);
        list ($controller, $method) = explode('@', $currentAction);
        $controller = preg_replace('/.*\\\/', '', $controller);
        $pushmessage = $this->message;
        $androidtoken = [];
        $iostoken = [];
        $title = getNotificationTitle($this->type);
        $user = User::where('id', $this->receiver_id)->first();
        $admin_approved_status = $user->getAdminApprovedStatus();
        $customer_id = $this->getCustomerID();
        $provider_id = $this->getProviderID();
        $order_id = $this->getOrderID();
        $post_id = $this->getPostID();
        if (! empty($user)) {

            $token = $user->fcm_token;
            if (! empty($token)) {

                if ($user->device_type == ANDROID) {
                    $androidtoken[] = $user->fcm_token;
                }
                if ($user->device_type == IOS) {
                    $iostoken[] = $user->fcm_token;
                }

                $url = 'https://fcm.googleapis.com/fcm/send';
                $server_key = env('SERVER_APIKEY', 'AAAAqZjI-18:APA91bGuxlyVS3dCdQYEg4zlDRmN47snsynPgZakPY1QniXhwV6GOxBAAWQ2ltnBH7er7YazCXNsZRs2cJApFEGX_yPOsjgIFwaCJcxYoI1BSJSUYuNXM6bA85b-fDn75zFEvCrX0ISr');
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
                            'type' => $this->type,
                            'title' => $title,
                            'description' => $pushmessage,
                            'admin_approved_status' => $admin_approved_status,
                            'customer_id' => $customer_id,
                            'provider_id' => $provider_id,
                            'account_type' => @$user->workProfile->account_type,
                            'order_id' => $order_id,
                            'post_id' => $post_id
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
                        } /*
                           * else {
                           * Log::info('Send Data --' . json_encode($fields));
                           * Log::info('Android Notification successfully --' . $pushmessage . "--" . $title . "--" . $user->id);
                           * }
                           */
                        curl_close($ch);
                    } catch (\Exception $e) {
                        Log::write('error', 'android NOTIFICATION SEND ERRROR');
                        Log::write('error', $e->getMessage());
                    }
                }

                if (! empty($iostoken)) {
                    try {

                        $data = array(
                            'type' => $this->type,
                            'admin_approved_status' => $admin_approved_status,
                            'type' => @$param['type'],
                            'label' => @$title,
                            'msg' => @$pushmessage,
                            'customer_id' => $customer_id,
                            'provider_id' => $provider_id,
                            'account_type' => @$user->workProfile->account_type,
                            'order_id' => $order_id,
                            'post_id' => $post_id
                        );
                        // debug($iostoken);

                        $notification = array(
                            'title' => @$title,
                            'body' => @$pushmessage,
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

                        /*
                         * if ($result === FALSE) {
                         * Log::write('error', 'FCM Send Error: ' . curl_error($ch));
                         * }
                         */
                        curl_close($ch);
                        // Log::info('Send Data --' . $json);
                        return $result;
                    } catch (\Exception $e) {
                        Log::write('error', 'IOS NOTIFICATION SEND ERRROR');
                        Log::write('error', $e->getMessage());
                    }
                }
            }
        } else {
            Log::write('error', 'No User Found');
        }
    }

    public static function clearNotifications($request)
    {
        return Notification::where([
            'receiver_id' => $request->user_id
        ])->delete();
    }

    public static function getNotificationList($request)
    {
        /*
         * delete notifications that orders soft deleted
         * $deletedOrders = Booking::withTrashed()->whereNotNull('deleted_at')
         * ->pluck('id')
         * ->toArray();
         * self::whereIn('model_id', $deletedOrders)->where([
         * 'model_type' => get_class(new Booking())
         * ])->delete();
         */
        self::where([
            'type' => NOTIFICATION_TYPE_REPORT_USER_BY_PROVIDER,
            'model_type' => get_class(new ReportUser())
        ])->delete();
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 10;
        $query = self::where('receiver_id', Auth::id());
        $query = $query->orderBy('created_at', 'desc')->paginate($page_limit);
        self::whereIn('id', $query->modelKeys())->update([
            'read' => NOTIFICATION_READ
        ]);
        $items = $query->items();
        $finalJson = [];
        foreach ($items as $key => $item) {
            $finalJson[] = $item->jsonResponse();
        }
        $data['next_page'] = ($query->nextPageUrl()) ? true : false;
        $data['notifications'] = $finalJson;
        $data['current_page'] = $query->currentPage();
        $data['per_page'] = $query->perPage();
        $data['links'] = $query->linkCollection();
        $data['total'] = $query->total();
        $data['total_pages'] = (int) ceil($query->total() / $query->perPage());
        return $data;
    }
}
