<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_to')->nullable();
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->unsignedBigInteger('post_id')->nullable();
            $table->integer('report_type')->default(1);
            $table->text('comment')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->foreign('report_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
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
        Schema::dropIfExists('report_posts');
    }
}
