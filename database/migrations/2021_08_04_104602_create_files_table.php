<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
             $table->id();
            $table->string('file');
            $table->string('original_name')->nullable();
            $table->string('size')->nullable();
            $table->integer('model_id');
            $table->string('model_type');
            $table->integer('state_id')->default(1);
            $table->integer('type_id')->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
