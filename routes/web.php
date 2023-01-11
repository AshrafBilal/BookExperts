<?php


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SiteController;
use QCod\AppSettings\Controllers\AppSettingController;
use App\Http\Controllers\Admin\DatabaseManagementController;
use App\Http\Controllers\Api\StripeConfigController;
use App\Http\Controllers\Admin\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    
    return Redirect::back()->with('success', 'All cache cleared successfully.');
});

    Route::any('privacy-policy', [SiteController::class,'privacyPolicy'])->name("privacy-policy");
    Route::any('term-condition',  [SiteController::class,'termCondition'])->name("term-condition");
    Route::any('about-us',  [SiteController::class,'aboutUs'])->name("aboutUs");
  //  Route::any('about-us',  [HomeController::class,'aboutUs'])->name("aboutUs");
    Route::any('test-socket',  [HomeController::class,'testSocket'])->name("testSocket");
    Route::post('contact-us', [SiteController::class,'contactUs'])->name('contact-us');
    
    Route::get('connect-with-stripe',[StripeConfigController::class,'connectWithStripe'])->name('connectWithStripe');
    

    Route::group(['namespace'=>"Admin",'middleware' => ['guest']], function(){
        
  
        Route::get('/', function () {
            return view('site.index');
        })->name('/');
    Route::any('admin_login', [AdminController::class,'login'])->name('admin.login');
    Route::any('forgot_password', [UserController::class,'forgotPassword'])->name("forgot_password");
    Route::any('new-password', [UserController::class,'newpassword'])->name("newpassword");
    
 });    
    
        
    
Route::group([
    'prefix' => 'admin',
    'namespace'=>"Admin",
    'middleware' => [
        'auth',
        'admin',
        'preventBackHistory']], function(){
       
    Route::get('delete-temp-files', [DatabaseManagementController::class,'deleteTempFiles'])->name('deleteTempFiles');
    Route::get('clear-database', [DatabaseManagementController::class,'clearDatabase'])->name('clearDatabase');
    Route::any('admin-home', [AdminController::class,'dashboard'])->name('admin.home');
    Route::any('home', [AdminController::class,'dashboard'])->name('home');
    Route::any('logout', [AdminController::class,'logout'])->name('logout');
    Route::any('admin-profile', [AdminController::class,'adminProfile'])->name('adminProfile');
    Route::any('reject-account','ServiceProviderController@rejectAccount')->name('rejectAccount');
    Route::any('approve-account','ServiceProviderController@approveAccount' )->name('approveAccount');
    Route::get('settings', [AppSettingController::class,'index'])->name('settings');
    Route::post('settings', [AppSettingController::class,'store'])->name('store');
    Route::any('activate-or-suspend-user', [AdminController::class,'activateOrSuspendUser'])->name('activateOrSuspendUser');
    Route::post('change-profile-status', [AdminController::class,'changeProfileStatus'])->name('changeProfileStatus');
    Route::post('change-work-profile-status', [AdminController::class,'changeWorkProfileStatus'])->name('changeWorkProfileStatus');
    Route::post('reject-common', [AdminController::class,'rejectCommon'])->name('rejectCommon');
    Route::any('reportIndex/{id}', 'ReportedPostController@reportIndex')->name('reportedPosts.reportIndex');
    Route::any('pending-account', 'ServiceProviderController@pendingAccount')->name('pendingAccount');
    Route::any('approved-account', 'ServiceProviderController@approvedAccount')->name('approvedAccount');
    Route::any('individual-account', 'ServiceProviderController@individualAccount')->name('individualAccount');
    Route::any('business-account', 'ServiceProviderController@businessAccount')->name('businessAccount');
    Route::post('activate-or-suspend-users', [AdminController::class,'activateOrSuspendUsers'])->name('activateOrSuspendUsers');
    
    Route::any('/pages', [PageController::class,'index'])->name('pages');
    Route::any('pages/add-page', [PageController::class,'addPage'])->name('pages.addPage');
    Route::any('pages/update-page/{id}', [PageController::class,'updatePage'])->name('pages.updatePage');
    Route::delete('pages/delete-page', [PageController::class,'deletePage'])->name('pages.deletePage');
    Route::any('pages/page-details/{id}', [PageController::class,'pageDetails'])->name('pages.pageDetails');
    
    
    Route::resources([
        'serviceCategory' => ServiceCategoryController::class,
        'subServiceCategory' => SubServiceCategoryController::class,
        'customers' => CustomerController::class,
        'serviceProviders' => ServiceProviderController::class,
        'posts' => PostController::class,
        'services' => ServiceController::class,
        'bookings' => BookingController::class,
        'address' => AddressController::class,
        'transactions' => TransactionController::class,
        'reportedPosts' => ReportedPostController::class,
        'reportedUsers' => ReportedUserController::class,
    ]);
      
  
   
    
});
