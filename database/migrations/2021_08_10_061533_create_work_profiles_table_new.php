<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkProfilesTableNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::dropIfExists('work_profiles');
       Schema::create('work_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->nullable();
            $table->integer('service_category_id');
            $table->text('tagline_for_business')->nullable();
            $table->text('location')->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->binary('about_business')->nullable();
            $table->integer('account_type')->default(1)->comment('1-Individual 2-Business');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_profiles_table_new');
    }
}
