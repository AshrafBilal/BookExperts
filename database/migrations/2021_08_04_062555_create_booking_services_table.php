<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->double('status')->default(0)->comment('0-Pending,1-Accept,2-IN-progress-3-Complete,4-Cancel,5-Reject');
            $table->tinyInteger('booking_type')->default(2)->comment('1 - Visit Homes 2 - Only at work place ',);
            $table->integer('total_quanity')->nullable();
            $table->double('total_amount')->nullable();
            $table->dateTime('service_started_at')->nullable();
            $table->dateTime('service_completed_at')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('service_provider_id')->nullable(true);
            $table->unsignedBigInteger('user_id')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('service_provider_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_services');
    }
}
