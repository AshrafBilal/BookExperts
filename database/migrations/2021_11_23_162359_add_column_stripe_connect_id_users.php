<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStripeConnectIdUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $column = Schema::hasColumn('users', 'stripe_connect_id','stripe_id');
            if (empty($column)) {
                $table->string('stripe_connect_id')->nullable();
                $table->string('stripe_id')->nullable();
                
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
        Schema::table('users', function (Blueprint $table) {
            $column = Schema::hasColumn('users', 'stripe_connect_id','stripe_id');
            if (! empty($column)) {
                $table->dropColumn([
                    'stripe_connect_id',
                    'stripe_id'
                ]);
                
            }
        });
    }
}
