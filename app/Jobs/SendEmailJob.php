<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->user->active_status == INACTIVE_STATUS) {
            Notification::sendNotification([
                'sender_id' => Auth::id(),
                'receiver_id' => $this->user->id,
                'model' => $this->user,
                'type' => NOTIFICATION_TYPE_ACCOUNT_DEACTIVATED,
                'message' => getNotificationTitle(NOTIFICATION_TYPE_ACCOUNT_DEACTIVATED)
            ]);
        } else {
            Notification::sendNotification([
                'sender_id' => Auth::id(),
                'receiver_id' => $this->user->id,
                'model' => $this->user,
                'type' => NOTIFICATION_TYPE_ACCOUNT_ACTIVE,
                'message' => getNotificationTitle(NOTIFICATION_TYPE_ACCOUNT_ACTIVE)
            ]);
        }
    }
}