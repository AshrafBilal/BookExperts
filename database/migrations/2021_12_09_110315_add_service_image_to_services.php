<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceImageToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $column = Schema::hasColumn('services', 'service_image');
            if (empty($column)) {
                $table->string('service_image')->nullable();
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
        Schema::table('services', function (Blueprint $table) {
            $column = Schema::hasColumn('services', 'service_image');
            if (! empty($column)) {
                $table->dropColumn([
                    'service_image'
                ]);
                
            }
        });
    }
}
