<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByToBookingStatusHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_status_histories', function (Blueprint $table) {
            $column = Schema::hasColumn('booking_status_histories','created_by');
            if (empty($column)) {
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                
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
        Schema::table('booking_status_histories', function (Blueprint $table) {
            $column = Schema::hasColumn('booking_status_histories','created_by');
            if (! empty($column)) {
                $table->dropColumn([
                    'created_by'
                ]);
            }
        });
    }
}
