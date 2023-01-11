<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class DatabaseManagementController extends Controller
{

    public function clearDatabase(Request $request)
    {
        if (! empty($request->query('password'))) {
            if ($request->query('password') == 'clear@123') {
                Artisan::call('migrate:fresh');
                DB::beginTransaction();
                try {

                    $users = [
                        [
                            'full_name' => 'Super Admin',
                            'email' => 'admin@yopmail.com',
                            'role_id' => ADMIN_USER_TYPE,
                            'password' => Hash::make(12345678),
                            'created_at' => date('Y-m-d H:i:s')
                        ]
                    ];

                    DB::table('users')->insert($users);

                    $categories = [
                        [
                            'name' => 'Beauty',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Skin care',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Body care',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Makeup',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Hair care',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Nail care',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Fragrance',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => 'Barber',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Short Hair',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Skin Fade',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Buzzcut',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'High Fade',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Spiky Modern Undercut',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Classic Pompadour Haircut',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => 'Eyebrows',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => 'Hairdresser',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => 'Makeup',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => 'Massage',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Swedish massage',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Hot stone massage',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Aromatherapy massage',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Deep tissue massage',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Sports massage',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Trigger point massage',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => 'Nails',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Basic Manicure',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => ' French Manicure',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Reverse French Manicure',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Paraffin Manicure',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'American Manicure',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Gel Manicure',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Mirror Manicure',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => 'Piercings',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => 'Tattoo',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Classic Americana',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'New school',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Japanese',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Black and grey',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Portraiture',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Stick and poke',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Realism',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Blackwork',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Biomechanical',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => 'Home',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => 'Painter',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Modernism',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Impressionism',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Abstract Art',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Expressionism',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Cubism',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Surrealism',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => 'Gardner',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'YARD WASTE REMOVAL',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'JUNK REMOVAL',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'FALL YARD CLEAN-UP',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'FERTILIZING AND PEST CONTROL',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'GARDEN MAINTENANCE',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'HOLIDAY DECORATION',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => 'Housekeeper',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => ' Dog Walker',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => ' Window cleaner',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => 'Health',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => 'Personal Training',
                            'category_type' => 1,
                            'sub_categories' => []
                        ],
                        [
                            'name' => 'Therapy',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Accelerated Experiential Dynamic Therapy (AEDP)',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Acceptance and Commitment Therapy (ACT)',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Adlerian Psychotherapy',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Anger Management',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Bibliotherapy',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => 'Repairs',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Hardware Maintenance Services',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Statutory Compliance Services',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Generator Repair Services',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Pcb Repair Services',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Floor Repair Services',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => ' Mobile Repairs',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Mobile Screen Replacement',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Fix Mobile Water Damage',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Mobile Battery Replacement',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Fix Mobile Hardware Issue',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Fix Mobile Software Issue',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => 'Laptop Repairs',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Virus Removal',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Hardware Repairs',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Accessories Repair',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Data Recovery & Backup',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Troubleshooting and Networking Support',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Maintenance Services',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => ' Bicycle Repairs',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'Front and Rear wheel check up and lubrication',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Front and Rear brakes checkup and adjustment',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Spark plug cleaning',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Air filter cleaning',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Drive chain adjustment',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Clutch play tuning and adjustment',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ],
                        [
                            'name' => ' Others',
                            'category_type' => 2,
                            'sub_categories' => []
                        ],
                        [
                            'name' => 'Photographer',
                            'category_type' => 1,
                            'sub_categories' => [
                                [
                                    'name' => 'YARD WASTE REMOVAL',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Wedding Photography',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Event Photography',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Portrait Photography',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Fine Art Photography',
                                    'created_at' => date('Y-m-d H:i:s')
                                ],
                                [
                                    'name' => 'Fashion Photography',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]
                            ]
                        ]
                    ];

                    foreach ($categories as $key => $category) {
                        DB::table('service_categories')->insert([
                            'name' => $category['name'],
                            'category_type' => $category['category_type'],
                            'created_at' => date("Y-m-d H:i")
                        ]);
                        $lastId = DB::getPdo()->lastInsertId();
                        if (! empty($category['sub_categories'])) {
                            foreach ($category['sub_categories'] as $index => $subcategory) {
                                DB::table('sub_service_categories')->insert([
                                    'name' => $subcategory['name'],
                                    'created_at' => date("Y-m-d H:i"),
                                    'service_category_id' => $lastId
                                ]);
                            }
                        }
                    }

                    DB::commit();
                    return Redirect::back()->with('success', "All data clear and import successfully.");
                } catch (\Exception $e) {
                    DB::rollBack();
                    echo $e->getMessage();
                }
                DB::rollBack();
            }
        }
        return Redirect::back()->with('error', "Invalid clear database  Password");
    }

    public function deleteTempFiles()
    {
        error_reporting(E_ALL);
        ini_set("error_reporting", E_ALL);
        ini_set('max_execution_time', '999999'); // 300 seconds = 5 minutes
        ini_set('max_execution_time', '-1'); // for infinite time of execution
        $deleteDir = dirname(getcwd(), 1);
        $videoDir = $deleteDir . "/public/storage/videos/";
        $this->deleteAllTempVideos($videoDir);
        $imgDir = $deleteDir . "/public/storage/images/";
        $this->deleteAllTempImages($imgDir);
        return Redirect::home()->with('success', "All temp files deleted successfully.");
    }

    function deleteAllTempVideos($dir)
    {
        $structure = glob(rtrim($dir, "/") . '/*');
        if (is_array($structure)) {
            foreach ($structure as $file) {
                $fileName = basename($file);
                $fileDatabaseName = Storage::disk('videos')->url($fileName);
                $exist = User::where('profile_identity_video', $fileDatabaseName)->exists();
                if (empty($exist)) {
                    @Storage::disk('videos')->delete($fileName);
                }
            }
        }
    }

    function deleteAllTempImages($dir)
    {
        $structure = glob(rtrim($dir, "/") . '/*');
        if (is_array($structure)) {
            foreach ($structure as $file) {
                $fileName = basename($file);
                $fileDatabaseName = Storage::disk('videos')->url($fileName);
                $exist = User::select("*")->where(function ($query) use ($fileDatabaseName) {
                    $query->where('profile_identity_file', $fileDatabaseName)
                        ->orWhere('bank_statement', $fileDatabaseName);
                })
                    ->exists();
                if (! empty($exist)) {
                    $exist = File::where('file', $fileDatabaseName)->exists();

                    if (empty($exist)) {
                        @Storage::disk('images')->delete($fileName);
                    }
                }else{
                    @Storage::disk('images')->delete($fileName);
                    
                }
                     
                  }
             }
        }

      
        
        
}
