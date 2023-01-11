<?php
namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class ImportTestDataSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('migrate:fresh');
        DB::beginTransaction();
        try {

            $this->call(UserSeeder::class);
            for ($i = 0; $i < 50; $i ++) {

                $faker = Factory::create();

                DB::table('service_categories')->insert([
                    'name' => $faker->name,
                    'description' => $faker->text,
                    'created_at' => date("Y-m-d H:i")
                ]);
            }

            for ($i = 0; $i < 50; $i ++) {

                $faker = Factory::create();

                DB::table('sub_service_categories')->insert([
                    'name' => $faker->name,
                    'description' => $faker->text,
                    'service_category_id' => rand(1, 50),
                    'created_at' => date("Y-m-d H:i")
                ]);
            }

            for ($i = 0; $i < 100; $i ++) {

                $faker = Factory::create();
                $firstname = $faker->firstName;
                $lastname = $faker->lastName;
                DB::table('users')->insert([
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'full_name' => $firstname . " " . $lastname,
                    'profile_file' => 'images/test_profile_file.jpg.jpg',
                    'profile_identity_file' => 'images/test_identity_file.jpg',
                    'profile_identity_video' => 'test_video.mp4',
                    'bank_statement' => 'images/test_bank_statement.jpg',
                    'email' => $faker->email,
                    'role_id' => SERVICE_PROVIDER_USER_TYPE,
                    'active_status' => ACTIVE_STATUS,
                    'phone_number' => $faker->phoneNumber,
                    'password' => Hash::make('password'),
                    'created_at' => date("Y-m-d H:i:s"),
                    'address' => $faker->address
                ]);

                $lastId = DB::getPdo()->lastInsertId();
                DB::table('work_profiles')->insert([
                    'business_name' => $faker->name,
                    'service_category_id' => rand(1, 100),
                    'tagline_for_business' => $faker->name,
                    'location' => $faker->address,
                    'about_business' => $faker->name,
                    'account_type' => rand(1, 2),
                    'user_id' => $lastId,
                   'service_category_id'=>rand(1,100),
                    'sub_service_category_id'=>rand(1,100),
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }

            for ($i = 0; $i < 100; $i ++) {

                $faker = Factory::create();
                $firstname = $faker->firstName;
                $lastname = $faker->lastName;
                DB::table('users')->insert([
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'full_name' => $firstname . " " . $lastname,
                    'profile_file' => 'images/test_profile_file.jpg.jpg',
                    'email' => $faker->email,
                    'role_id' => NORMAL_USER_TYPE,
                    'active_status' => ACTIVE_STATUS,
                    'phone_number' => $faker->phoneNumber,
                    'password' => Hash::make('password'),
                    'created_at' => date("Y-m-d H:i:s"),
                    'address' => $faker->address
                ]);
            }

            for ($i = 0; $i < 100; $i ++) {

                $faker = Factory::create();
                $firstname = $faker->firstName;
                $lastname = $faker->lastName;
                DB::table('services')->insert([
                    'name' => $faker->name,
                    'description' => $faker->text,
                    'price' => rand(10, 999),
                    'time' => rand(10, 1000),
                    'service_category_id' => rand(1, 50),
                    'created_at' => date("Y-m-d H:i")
                ]);
            }
            
            for ($i = 0; $i < 100; $i ++) {
                
                $faker = Factory::create();
                $firstname = $faker->firstName;
                $lastname = $faker->lastName;
                DB::table('addresses')->insert([
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'phone_code' => $faker->countryISOAlpha3(),
                    'iso_code' => rand(3, 3),
                    'phone_number' => $faker->phoneNumber,
                    'address1' => $faker->address,
                    'address2' => $faker->address,
                    'street' => $faker->streetName,
                    'city' => $faker->city,
                    'user_id' => rand(2, 100),
                    'state' => $faker->city,
                    'country' => $faker->country,
                    'zip_code' => $faker->postcode,
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
            
            for ($i = 0; $i < 100; $i ++) {
                
                $faker = Factory::create();
              
                DB::table('bookings')->insert([
                    'status' =>rand(0,5),
                    'status' =>rand(1,2),
                    'country_code' => $faker->countryCode,
                    'contact_number' => $faker->phoneNumber,
                    'total_quanity' => rand(1, 99),
                    'total_amount' => rand(1, 999),
                    'address_id' => rand(1, 100),
                    'service_provider_id' => rand(2, 100),
                    'user_id' => rand(2, 100),
                    'created_at' => date("Y-m-d H:i")
                ]);
            }

            for ($i = 0; $i < 100; $i ++) {

                $faker = Factory::create();
                $firstname = $faker->firstName;
                $lastname = $faker->lastName;
                DB::table('posts')->insert([
                    'user_id' => rand(2, 101),
                    'file_type' => rand(1, 3),
                    'post_type' => rand(1, 3),
                    'url' => $faker->url,
                    'description' => $faker->text
                ]);
            }

           

            for ($i = 0; $i < 100; $i ++) {

                $faker = Factory::create();
                $firstname = $faker->firstName;
                $lastname = $faker->lastName;
                DB::table('transactions')->insert([
                    'transaction_id' => $faker->randomNumber(),
                    'amount' => rand(10, 250),
                    'user_id' => rand(101, 150),
                    'booking_id' => rand(1, 100),
                    'card_id' => rand(1, 99),
                    'card_number' => rand(10000, 99999),
                    'status' => rand(0, 2),
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
