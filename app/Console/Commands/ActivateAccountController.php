<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ride;
use Illuminate\Support\Facades\Log;
use App\Models\SuspendAccount;
use App\Models\User;
use App\Models\Notification;

class ActivateAccountController extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:activateAccount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reported users account activated after three days';

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
        $suspendAccounts = SuspendAccount::whereDate('activate_date', '<', date("Y-m-d"))->get();
        if (! $suspendAccounts->isEmpty()) {
            foreach ($suspendAccounts as $suspendAccount) {
                $user = User::find($suspendAccount->user_id);
                if (! empty($user)) {
                    $user->active_status = ACTIVE_STATUS;
                    $user->save();
                    $suspendAccount->status = ACTIVE_STATUS;
                    $suspendAccount->save();
                    $suspendAccount->delete();
                    Notification::sendNotification([
                        'sender_id' => $user->id,
                        'receiver_id' => $user->id,
                        'model' => $user,
                        'type' => NOTIFICATION_TYPE_ACCOUNT_ACTIVE,
                        'message' => getNotificationTitle(NOTIFICATION_TYPE_ACCOUNT_ACTIVE),
                        'current_action' => __NAMESPACE__ . '\ActivateAccount@ActivateAccount',
                        
                     ]);
                }
                
            }
        }
    }
}
