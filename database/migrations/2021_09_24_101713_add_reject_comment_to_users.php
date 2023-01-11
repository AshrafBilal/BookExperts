<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectCommentToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $profile_reject_comment =  Schema::hasColumn('users', 'profile_reject_comment');
            if(empty($profile_reject_comment)){
                $table->string('profile_reject_comment')->nullable();
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
            $profile_reject_comment =  Schema::hasColumn('users', 'profile_reject_comment');
            if(!empty($profile_reject_comment)){
                $table->dropColumn(['profile_reject_comment']);
            }
        });
    }
}
