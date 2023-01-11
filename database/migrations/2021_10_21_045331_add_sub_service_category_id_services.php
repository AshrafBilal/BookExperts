<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubServiceCategoryIdServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $column =  Schema::hasColumn('services', 'sub_service_category_id','service_provider_id','service_visit');
            if(empty($column)){
                $table->integer('service_visit')->nullable()->comment('1 - Visit Homes 2 - Only at work place 3 - Both');
                $table->unsignedBigInteger('sub_service_category_id')->nullable();
                $table->unsignedBigInteger('service_provider_id')->nullable();
                $table->foreign('sub_service_category_id')->references('id')->on('sub_service_categories')->onDelete('cascade');
                $table->foreign('service_provider_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::table('services', function (Blueprint $table) {
            Schema::table('services', function (Blueprint $table) {
                $column =  Schema::hasColumn('services', 'sub_service_category_id','service_provider_id','service_visit');
                if(!empty($column)){
                    $table->dropForeign(['sub_service_category_id']);
                    $table->dropColumn(['sub_service_category_id']);
                    $table->dropForeign(['service_provider_id']);
                    $table->dropColumn(['service_provider_id']);
                    $table->dropColumn(['service_visit']);
                }
            });
        });
    }
}
