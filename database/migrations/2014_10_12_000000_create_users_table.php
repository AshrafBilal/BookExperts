<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('phone_code')->nullable();
            $table->string('iso_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('password_reset_token')->nullable();
            $table->tinyInteger('steps_completed')->default(0);
            $table->tinyInteger('profile_identity_file_status')->default(0)->comment('0 -pending 1 - approved,2-rejected');
            $table->tinyInteger('profile_identity_video_status')->default(0)->comment('0 -pending 1 - approved,2-rejected');
            $table->tinyInteger('bank_statement_file_status')->default(0)->comment('0 -pending 1 - approved,2-rejected');
            $table->tinyInteger('notification_status')->default(1)->comment('0 -off 1 - On');
            $table->tinyInteger('email_notification')->default(1)->comment('0 -off 1 - On');
            $table->tinyInteger('role_id')->default(2)->comment('1 - Admin, 2- User,3- Provider');
            $table->tinyInteger('active_status')->default(1)->comment('0 -IN-Active 1 - Active 3-Delete');
            $table->tinyInteger('profile_verified')->default(0)->comment('0 -Not Verified 1 - Verified');
            $table->tinyInteger('work_profile')->default(2)->comment('1 - Visit Homes 2 - Only at work place 3 - Both ');
            $table->tinyInteger('register_type')->default(1)->comment('1-Basic,2- Face-book,3-Google,4-Apple');
            $table->tinyInteger('device_type')->default(1)->comment('0-Andriod,1-IOS');
            $table->string('profile_file')->nullable();
            $table->string('profile_identity_file')->nullable();
            $table->string('profile_identity_video')->nullable();
            $table->string('bank_statement')->nullable();
            $table->date('dob')->nullable();
            $table->text('about_me')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('otp')->nullable();            
            $table->tinyInteger('otp_verification_type')->nullable()->comment('1 - verify sign-up otp, 2- Change password otp,3- Change email otp');
            $table->string('change_email_request')->nullable();
            $table->text('fcm_token')->nullable();
            $table->tinyInteger('otp_verified')->default(0)->comment('0 -No 1 - Yes');
            $table->datetime('last_active')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
