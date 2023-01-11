<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingDateTimeBookingServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $column = Schema::hasColumn('booking_services', 'booking_date_time','price_per_unit','service_id');
            if (empty($column)) {
                $table->dateTime('booking_date_time')->nullable();
                $table->double('price_per_unit')->nullable();
                $table->unsignedBigInteger('service_id')->nullable();
                $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
                
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
        Schema::table('booking_services', function (Blueprint $table) {
            $column = Schema::hasColumn('booking_services', 'booking_date_time','price_per_unit','service_id');
            if (! empty($column)) {
                $table->dropForeign('service_id');
                $table->dropColumn([
                    'booking_date_time',
                    'price_per_unit',
                    'service_id'
                ]);
                
            }
        });
    }
}
