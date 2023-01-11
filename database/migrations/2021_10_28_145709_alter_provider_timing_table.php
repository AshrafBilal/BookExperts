<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProviderTimingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_timings', function (Blueprint $table) {
                $table->dropColumn(['start_time','end_time']);
        });
            Schema::table('provider_timings', function (Blueprint $table) {
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                
                
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
}
