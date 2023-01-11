<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserBookingDateTimeToBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
                Schema::table('bookings', function (Blueprint $table) {
                    $column = Schema::hasColumn('bookings','user_booking_date_time');
                    if (empty($column)) {
                        $table->dateTime('user_booking_date_time')->nullable();
                        
                    }
                });
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
            $column = Schema::hasColumn('bookings','user_booking_date_time');
            if (! empty($column)) {
                $table->dropColumn([
                    'user_booking_date_time',
                ]);
            }
        });
    }
}
