<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('notifications', function (Blueprint $table) {
                $column = Schema::hasColumn('notifications','mail_send_status','notification_send_status');
                if (empty($column)) {
                    $table->tinyInteger('mail_send_status')->default(1);
                    $table->tinyInteger('notification_send_status')->default(1);
                }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $column = Schema::hasColumn('notifications','mail_send_status','notification_send_status');
            if (! empty($column)) {
                $table->dropColumn([
                    'mail_send_status',
                    'notification_send_status'
                ]);
            }
        });
    }
}
