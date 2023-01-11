<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $column = Schema::hasColumn('users', 'street','zip_code');
            if (empty($column)) {
                $table->string('street')->nullable();
                $table->integer('zip_code')->nullable();
                
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
            $column = Schema::hasColumn('users', 'street','zip_code');
            if (! empty($column)) {
                $table->dropColumn([
                    'street',
                    'zip_code'
                ]);
                
            }
        });
    }
}
