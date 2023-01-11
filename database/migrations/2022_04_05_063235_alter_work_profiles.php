<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWorkProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_profiles', function (Blueprint $table) {
            $column = Schema::hasColumn('work_profiles','service_category_id','sub_service_category_id');
            if (!empty($column)) {
                $table->integer('service_category_id')->nullable()->change();
                $table->text('sub_service_category_id')->nullable()->change();
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
        //
    }
}
