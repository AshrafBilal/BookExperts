<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsLiveBookingBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $column = Schema::hasColumn('bookings','is_live_booking');
            if (empty($column)) {
                $table->tinyInteger('is_live_booking')->default(0)->comment('0-No,1-Yes');
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
        Schema::table('bookings', function (Blueprint $table) {
            $column = Schema::hasColumn('bookings','is_live_booking');
            if (! empty($column)) {
                $table->dropColumn([
                    'is_live_booking'
                ]);
                
            }
        });
    }
}
