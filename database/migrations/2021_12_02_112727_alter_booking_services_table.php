<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $column = Schema::hasColumn('booking_services', 'business_id');
            if (empty($column)) {
                $table->unsignedBigInteger('business_id')->nullable();
                $table->foreign('business_id')->references('id')->on('users')->onDelete('cascade');
                
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
            $column = Schema::hasColumn('booking_services', 'business_id');
            if (! empty($column)) {
                $table->dropColumn([
                    'business_id',
                ]);
                
            }
        });
    }
}
