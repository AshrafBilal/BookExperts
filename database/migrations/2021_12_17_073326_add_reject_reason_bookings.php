<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectReasonBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $column = Schema::hasColumn('bookings','reject_reason');
            if (empty($column)) {
                $table->string('reject_reason')->nullable();
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
            $column = Schema::hasColumn('bookings','reject_reason');
            if (! empty($column)) {
                $table->dropColumn([
                    'reject_reason'
                ]);
                
            }
        });
    }
}
