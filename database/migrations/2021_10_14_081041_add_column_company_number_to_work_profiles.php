<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCompanyNumberToWorkProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_profiles', function (Blueprint $table) {
            $company_number =  Schema::hasColumn('work_profiles', 'company_number');
            if(empty($company_number)){
                $table->string('company_number')->nullable();
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
            $company_number =  Schema::hasColumn('work_profiles', 'company_number');
            if(!empty($company_number)){
                $table->dropColumn(['company_number']);
            }
        });
    }
}
