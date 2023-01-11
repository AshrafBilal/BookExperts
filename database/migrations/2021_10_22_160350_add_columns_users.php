<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsUsers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('personal_profile_comment_title')->nullable();
            $table->text('personal_profile_comment_description')->nullable();
            $table->string('identity_file_comment_title')->nullable();
            $table->text('identity_file_comment_description')->nullable();
            $table->string('bank_statement_comment_title')->nullable();
            $table->text('bank_statement_comment_description')->nullable();
            $table->string('identity_video_comment_title')->nullable();
            $table->text('identity_video_comment_description')->nullable();
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

                $table->dropColumn([
                    'personal_profile_comment_title',
                    'personal_profile_comment_description',
                    'identity_file_comment_title',
                    'identity_file_comment_description',
                    'bank_statement_comment_title',
                    'bank_statement_comment_description',
                    'identity_video_comment_title',
                    'identity_video_comment_description',
                    ]);
                    
                
            });
        });
    }
}
