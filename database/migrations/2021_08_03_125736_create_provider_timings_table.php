<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_timings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day')->comment('1-Monday,7-Sunday');
            $table->tinyInteger('off_day_type')->default(1)->comment('1-Schedule Current Day,2-Schedule All Days');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provider_timings');
    }
}
