<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvailableForLiveBookingToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                $column = Schema::hasColumn('users','available_for_home_booking	');
                if (empty($column)) {
                    $table->integer('available_for_home_booking')->default(0);
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
        Schema::table('users', function (Blueprint $table) {
            $column = Schema::hasColumn('users','available_for_home_booking');
            if (! empty($column)) {
                $table->dropColumn([
                    'available_for_home_booking'
                ]);
            }
        });
    }
}
