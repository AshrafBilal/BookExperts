<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsOnlineUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $column = Schema::hasColumn('users', 'available_for_live_booking','socket_id');
            if (empty($column)) {
                $table->tinyInteger('available_for_live_booking')->default(0)->comment('0-offline,1-online');
                $table->string('socket_id')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            $column = Schema::hasColumn('users', 'available_for_live_booking','socket_id');
            if (! empty($column)) {
                $table->dropColumn([
                    'available_for_live_booking',
                    'socket_id'
                ]);
                
            }
        });
    }
}
