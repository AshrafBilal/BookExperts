<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCashPaymentStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            Schema::table('bookings', function (Blueprint $table) {
                $column = Schema::hasColumn('bookings','payment_method','payment_status');
                if (empty($column)) {
                    $table->integer('payment_method')->default(1)->comment('1-COD,2-Online');
                    $table->integer('payment_status')->default(0)->comment('0-Pending,1-Success,2-Failed');
                    
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
        
        Schema::table('bookings', function (Blueprint $table) {
            $column = Schema::hasColumn('bookings','payment_method','payment_status');
            if (! empty($column)) {
                $table->dropColumn([
                    'payment_method',
                    'payment_status'
                ]);
            }
        });
    }
}
