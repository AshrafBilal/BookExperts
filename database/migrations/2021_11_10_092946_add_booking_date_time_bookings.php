<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingDateTimeBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $column = Schema::hasColumn('bookings', 'booking_date_time','order_id');
            if (empty($column)) {
                $table->dateTime('booking_date_time')->nullable();
                $table->string('order_id')->nullable();
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
            $column = Schema::hasColumn('bookings', 'booking_date_time','order_id');
            if (! empty($column)) {
                
                $table->dropColumn([
                    'booking_date_time',
                    'order_id'
                ]);
                
            }
        });
    }
}
