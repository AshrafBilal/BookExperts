<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUserIdServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $company_number =  Schema::hasColumn('services', 'user_id');
            if(empty($company_number)){
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
            $company_number =  Schema::hasColumn('services', 'user_id');
            if(!empty($company_number)){
                $table->dropForeign(['user_id']);
                $table->dropColumn(['user_id']);
            }
        });
    }
}
