<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialTokenToTableUsers extends Migration
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
                $column =  Schema::hasColumn('users', 'social_token');
                if(empty($column)){
                    $table->text('social_token')->nullable();
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
            Schema::table('users', function (Blueprint $table) {
                $column =  Schema::hasColumn('users', 'social_token');
                if(!empty($column)){
                    $table->dropColumn(['social_token']);
                }
            });
        });
    }
}
