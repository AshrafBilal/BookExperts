<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnParentCommentIdComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $column = Schema::hasColumn('comments','parent_comment_id');
            if (empty($column)) {
                $table->unsignedBigInteger('parent_comment_id')->nullable();
                $table->foreign('parent_comment_id')->references('id')->on('comments')->onDelete('cascade');
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
        Schema::table('comments', function (Blueprint $table) {
            $column = Schema::hasColumn('comments','parent_comment_id');
            if (! empty($column)) {
                $table->dropColumn([
                    'parent_comment_id'
                ]);
                
            }
        });
    }
}
