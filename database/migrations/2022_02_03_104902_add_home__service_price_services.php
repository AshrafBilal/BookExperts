<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHomeServicePriceServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            Schema::table('services', function (Blueprint $table) {
                $column = Schema::hasColumn('services','home_service_price');
                if (empty($column)) {
                    $table->double('home_service_price')->nullable();
                    
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
        
        Schema::table('services', function (Blueprint $table) {
            $column = Schema::hasColumn('services','home_service_price');
            if (! empty($column)) {
                $table->dropColumn([
                    'home_service_price',
                ]);
            }
        });
    }
}
