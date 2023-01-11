<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $isAdminExist = \DB::table('users')->count();
        if($isAdminExist <= 0){
        $users = [
    		[
	    		'full_name' => 'Super Admin',
	    		'email' => 'admin@yopmail.com',
    		    'role_id' => ADMIN_USER_TYPE,
	    		'password' =>Hash::make(123456),
	    		'created_at' => date('Y-m-d H:i:s')
    		]
    	];

        //INSERT DATA INTO VISITOR TYPES TABLE
        \DB::table('users')->insert($users);
    }
  }
}
