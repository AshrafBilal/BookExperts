<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ride;
use Illuminate\Support\Facades\Log;
use App\Models\SuspendAccount;
use App\Models\User;
use App\Models\Notification;
use App\Models\Like;
use App\Models\Follow;
use App\Models\Specialist;

class SendNotificationEmailController extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sendNotificationEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notifications = Notification::where('mail_send_status', INACTIVE_STATUS)->where('model_type', '!=', get_class(new Like()))
            ->where('model_type', '!=', get_class(new Follow()))
            ->where('model_type', '!=', get_class(new Specialist()))
            ->get();
        if (! $notifications->isEmpty()) {
            foreach ($notifications as $notification) {
                $notification->sendMail();
                $notification->mail_send_status = ACTIVE_STATUS;
                $notification->save();
            }
        }
    }
}
