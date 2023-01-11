<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsStatusWorkProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_profiles', function (Blueprint $table) {
            $column =  Schema::hasColumn('work_profiles', 'status');
            if(empty($column)){
                $table->tinyInteger('status')->default(0)->comment('0 - Inactive,1-Active');
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
        Schema::table('work_profiles', function (Blueprint $table) {
            $column =  Schema::hasColumn('work_profiles', 'status');
            if(!empty($column)){
                $table->dropColumn(['status']);
            }
        });
    }
}
