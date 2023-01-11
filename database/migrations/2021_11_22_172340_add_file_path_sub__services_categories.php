<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilePathSubServicesCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_service_categories', function (Blueprint $table) {
            $column = Schema::hasColumn('sub_service_categories', 'file_path');
            if (empty($column)) {
                $table->string('file_path')->nullable();
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
        Schema::table('sub_service_categories', function (Blueprint $table) {
            $column = Schema::hasColumn('sub_service_categories', 'file_path');
            if (! empty($column)) {
                $table->dropColumn([
                    'file_path'
                ]);
                
            }
        });
    }
}