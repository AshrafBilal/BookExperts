<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $column = Schema::hasColumn('transactions', 'stripe_connect_id','stripe_id');
            if (empty($column)) {
                $table->string('card_id')->change();
                $table->double('total_amount', 11,2)->nullable();
                $table->double('commission_amount', 11,2)->nullable();
                
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
        Schema::table('transactions', function (Blueprint $table) {
            $column = Schema::hasColumn('transactions', 'total_amount','commission_amount');
            if (! empty($column)) {
                $table->bigInteger('card_id')->change();
                $table->dropColumn([
                    'total_amount',
                    'commission_amount'
                ]);
                
            }
        });
    }
}
