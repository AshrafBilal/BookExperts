<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirebaseChatTokenUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $column = Schema::hasColumn('users','firebase_chat_token');
            if (empty($column)) {
                $table->string('firebase_chat_token')->nullable();
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
            $column = Schema::hasColumn('users','firebase_chat_token');
            if (! empty($column)) {
                $table->dropColumn([
                    'firebase_chat_token'
                ]);
            }
        });
    }
}
