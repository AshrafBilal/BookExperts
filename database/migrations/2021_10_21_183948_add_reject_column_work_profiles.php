<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectColumnWorkProfiles extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_profiles', function (Blueprint $table) {
            $column = Schema::hasColumn('work_profiles', 'reject_title', 'reject_description');
            if (empty($column)) {
                $table->string('reject_title')->nullable();
                $table->text('reject_description')->nullable();
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
                $column = Schema::hasColumn('work_profiles', 'reject_title', 'reject_description');
                if (! empty($column)) {
                 
                    $table->dropColumn([
                        'reject_title','reject_description'
                    ]);
                   
                }
            });
        
    }
}
