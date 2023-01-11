<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryTypeToServiceCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $category_type =  Schema::hasColumn('service_categories', 'category_type');
            if(empty($category_type)){
                $table->integer('category_type')->nullable()->comment('1-Normal,2-other')->after('name');
            }
            $status =  Schema::hasColumn('service_categories', 'status');
            if(empty($status)){
                $table->integer('status')->default(1)->comment('0-pending,1-approve,2-reject');
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
        Schema::table('service_categories', function (Blueprint $table) {
            $category_type =  Schema::hasColumn('service_categories', 'category_type');
            if(!empty($category_type)){
                $table->dropColumn(['category_type']);
            }
            $status =  Schema::hasColumn('service_categories', 'status');
            if(!empty($status)){
                $table->dropColumn(['status']);
            }
        });
    }
}
