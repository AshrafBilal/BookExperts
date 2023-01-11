<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeZoneUsers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $column = Schema::hasColumn('users', 'time_zone');
            if (empty($column)) {
                $table->string('time_zone')->nullable();
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
            $column = Schema::hasColumn('users', 'time_zone');
            if (! empty($column)) {

                $table->dropColumn([
                    'time_zone'
                ]);
                
            }
        });
    }
}
