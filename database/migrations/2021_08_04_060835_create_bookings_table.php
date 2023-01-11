<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->double('status')->default(0)->comment('0-Pending,1-Accept,2-IN-progress-3-Complete,4-Cancel,5-Reject');
            $table->tinyInteger('booking_type')->default(2)->comment('1 - Visit Homes 2 - Only at work place ',);
            $table->string('country_code')->nullable();
            $table->string('contact_number')->nullable();
            $table->integer('total_quanity')->nullable();
            $table->double('total_amount')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->unsignedBigInteger('service_provider_id')->nullable(true);
            $table->unsignedBigInteger('user_id')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('bookings');
    }
}
