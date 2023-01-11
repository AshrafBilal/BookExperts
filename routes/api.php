<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReController;


/*
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register API routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | is assigned the "api" middleware group. Enjoy building your API!
 * |
 */    

// Route::post('/registerUser', 'AuthController@createUser');

// Route::post('/userLogin', 'AuthController@userLogin')->name('userLogin');
//   Route::post('/userLogin', [AuthController::class, 'userLogin']);

Route::prefix('v1')->namespace('Api')->group(function () {
    //  Route::post('/register', [AuthController::class, 'createUser']);


    Route::prefix('user')->name('user.')
        ->group(function () {
        Route::any('/send-notification', 'NotificationController@sendTestNotification')->name('sendTestNotification');
        Route::post('/customerRegister', 'AuthController@customerRegister')->name('customerRegister');
        Route::post('/providerRegister', 'AuthController@providerRegister')->name('providerRegister');
        Route::post('/resendOtp', 'AuthController@resendOtp')->name('resendOtp');
        Route::post('/verifyOtp', 'AuthController@verifyOtp')->name('verifyOtp');
        Route::post('/login', 'AuthController@login')->name('login');
        Route::post('/forgotPassword', 'AuthController@forgotPassword')->name('forgotPassword');
        Route::post('/verifyResetPasswordOtp', 'AuthController@verifyResetPasswordOtp')->name('verifyResetPasswordOtp');
        Route::post('/resetPassword', 'AuthController@resetPassword')->name('resetPassword');
        Route::post('/social-login', 'AuthController@socialLogin')->name('socialLogin');
        //User 
        Route::post('/registerUser', 'AuthController@createUser')->name('createUser');
         Route::post('/userLogin', 'AuthController@userLogin')->name('userLogin');



        // Provider API start
        Route::middleware([
            'auth:sanctum'
        ])->group(function () {
            Route::post('/personalProfile', 'AccountController@personalProfile')->name('personalProfile');
            Route::post('/workProfile', 'AccountController@workProfile')->name('workProfile');
            Route::post('/uploadId', 'AccountController@uploadId')->name('uploadId');
            Route::post('/uploadBankStatement', 'AccountController@uploadBankStatement')->name('uploadBankStatement');

        });
        Route::post('/getProviderByUniqueId', 'AccountController@getProviderByUniqueId')->name('getProviderByUniqueId');
        Route::get('/getServiceCategory', 'HomeController@getServiceCategory')->name('getServiceCategory');

        Route::middleware([
            'auth:sanctum'
        ])->group(function () {

            Route::get('/logout', 'AuthController@logout')->name('logout');
            Route::post('/changePassword', 'AccountController@changePassword')->name('changePassword');
            Route::get('/getProfile', 'AccountController@getProfile')->name('getProfile');
            Route::post('/updateProfile', 'AccountController@updateProfile')->name('updateProfile');
            Route::post('/changeEmail', 'AccountController@changeEmail')->name('changeEmail');
            Route::post('/verifyChangeEmailOtp', 'AccountController@verifyChangeEmailOtp')->name('verifyChangeEmailOtp');
            Route::post('/changePhoneNumber', 'AccountController@changePhoneNumber')->name('changePhoneNumber');
            Route::post('/verifyChangePhoneNumberOtp', 'AccountController@verifyChangePhoneNumberOtp')->name('verifyChangePhoneNumberOtp');
            Route::get('/getProfileStatus', 'AccountController@getProfileStatus')->name('getProfileStatus');
            Route::get('/getProfileStatusDetails/{type}', 'AccountController@getProfileStatusDetails')->name('getProfileStatusDetails');
            Route::get('/getSubServiceCategory', 'HomeController@getSubServiceCategory')->name('getSubServiceCategory');
            Route::post('/address/add', 'AddressController@add')->name('addAddress');
            Route::post('/bank-account/add', 'BankAccountController@add')->name('addBank');
            Route::get('/get-address', 'AddressController@getAddress')->name('getAddress');
            Route::get('/get-bank-account', 'BankAccountController@getBankAccount')->name('getBank');
            Route::get('/nearby-providers', 'CustomerController@nearbyProviders')->name('nearbyProviders');
            Route::get('/sub-service-categories/{id}', 'CustomerController@getCustomerSubServiceCategory')->name('getCustomerSubServiceCategory');
            Route::get('/recommended-providers', 'CustomerController@recommendedProviders')->name('recommendedProviders');
            Route::post('/follow-unfollow', 'FollowController@followUnfollow')->name('followUnfollow');
            Route::post('/business-details', 'CustomerController@businessDetails')->name('businessDetails');
            Route::get('/following-list', 'FollowController@followingList')->name('followingList');
            Route::post('/provider-details', 'CustomerController@providerDetails')->name('providerDetails');
            Route::post('/book-service', 'BookServiceController@bookService')->name('bookService');
            Route::post('/add-address', 'AddressController@customerAddAddress')->name('customerAddAddress');
            Route::post('/update-address', 'AddressController@customerUpdateAddress')->name('customerUpdateAddress');
            Route::get('/address-list', 'AddressController@getCustomerAddressList')->name('getCustomerAddressList');
            Route::post('/address-details', 'AddressController@getCustomerAddress')->name('getCustomerAddress');
            Route::post('/delete-address', 'AddressController@deleteAddress')->name('deleteCustomerAddress');
            Route::post('/make-default-address', 'AddressController@makeDefaultAddress')->name('makeDefaultAddress');
            Route::get('/my-bookings', 'BookServiceController@myBookings')->name('myBookings');
            Route::get('/booking-details', 'BookServiceController@bookingDetails')->name('bookingDetails');
            Route::post('/check-provider-availability', 'BookServiceController@checkProviderAvailability')->name('checkProviderAvailability');
            Route::get('/get-menu', 'ServiceProviderController@getMenu')->name('getProviderMenuByUser');
            Route::get('/get-provider-timing', 'ProviderTimingController@getProviderTiming')->name('providerTiming');
            Route::post('/post-like', 'PostController@likePost')->name('likePost');
            Route::get('/review-list', 'RatingController@reviewList')->name('reviewList');
            Route::post('/rate-and-review', 'RatingController@rateAndReview')->name('rateAndReview');
            Route::post('/add-card','StripeConfigController@addCard')->name('addCard');
            Route::get('/card-listing','StripeConfigController@cardListing')->name('cardListing');
            Route::post('/set-default-card','StripeConfigController@setDefaultCard')->name('setDefaultCard');
            Route::post('/delete-card','StripeConfigController@deleteCard')->name('deleteCard');
            Route::post('/add-tip', 'BookServiceController@addTip')->name('addTip');
            Route::get('/notification-list', 'NotificationController@notificationList')->name('notificationList');
            Route::any('/get-page', 'HomeController@getPage')->name('getCustomerPage');
            Route::get('/post-listing', 'PostController@myPosts')->name('providerPostListing');
            Route::get('/story-details', 'PostController@storyDetails')->name('providerStoryDetails');
            Route::post('/like-dislike-post', 'PostController@likeDislikePost')->name('likeDislikePost');
            Route::get('/post-likes', 'PostController@postLikes')->name('customerPostLikes');
            Route::post('/add-post-comment', 'CommentController@addPostComment')->name('customerAddPostComment');
            Route::get('/post-comments', 'CommentController@postComments')->name('customerPostComments');
            Route::post('/like-dislike-comment', 'CommentController@likeDislikeComment')->name('likeDislikeComment');
            Route::get('/post-details', 'PostController@postDetails')->name('postDetails');            
            Route::post('/contact-us', 'HomeController@contactUs')->name('contactUs');
            Route::post('/report-post', 'PostController@reportPost')->name('reportPost');
            Route::post('/notification-setting', 'NotificationController@notificationSetting')->name('userNotificationSetting');
            Route::post('/cancel-booking', 'BookServiceController@customerCancelBooking')->name('customerCancelBooking');
            Route::post('/report-user', 'ReportUserController@reportUser')->name('customer.reportUser');
            
         });
            Route::post('/save-firebase-chat-token', 'AccountController@saveFirebaseChatToken')->name('saveFirebaseChatToken');
    });


    /**
     * Start Service Provider Routes *
     */

    Route::prefix('provider')->name('provider.')
        ->group(function () {

        Route::middleware([
            'auth:sanctum'
        ])->group(function () {
            Route::get('/provider-detail-steps', 'ServiceProviderController@providerDetailSteps')->name('providerDetailSteps');
            Route::post('/add-menu', 'ServiceProviderController@addMenu')->name('addMenu');
            Route::get('/service-provider-list', 'ServiceProviderController@getServiceProvider')->name('getServiceProvider');
            Route::get('/get-menu', 'ServiceProviderController@getMenu')->name('getMenu');
            Route::post('/add-service-provider', 'ServiceProviderController@addServiceProvider')->name('addServiceProvider');
            Route::post('/delete-menu', 'ServiceProviderController@deleteMenu')->name('deleteMenu');
            Route::post('/provider-timing/add', 'ProviderTimingController@add')->name('addProviderTiming');
            Route::get('/get-provider-timing', 'ProviderTimingController@getProviderTiming')->name('getProviderTiming');
            Route::post('/delete-provider-timing', 'ProviderTimingController@deleteProviderTiming')->name('deleteProviderTiming');
            Route::post('/remove-service-provider', 'ServiceProviderController@removeServiceProvider')->name('deleteServiceProvider');
            Route::post('/delete-work-profile-image', 'ServiceProviderController@deleteWorkProfileImage')->name('deleteWorkProfileImage');
            Route::post('/update-profile', 'ServiceProviderController@updateProfile')->name('updateProfile');
            Route::get('/my-orders', 'BookServiceController@myOrders')->name('myOrders');
            Route::get('/order-details', 'BookServiceController@orderDetails')->name('ordersDetails');
            Route::post('/update-order-status', 'BookServiceController@updateOrderStatus')->name('updateOrderStatus');            
            Route::post('/add-post', 'PostController@addPost')->name('addPost');
            Route::post('/update-post', 'PostController@updatePost')->name('updatePost');
            Route::post('/delete-post', 'PostController@deletePost')->name('deletePost');
            Route::post('/rate-and-review', 'RatingController@rateAndReviewCustomer')->name('customerRateAndReview');
            Route::get('/order-details/', 'BookServiceController@orderDetails')->name('ordersDetails');
            Route::get('/my-posts', 'PostController@myPosts')->name('myPosts');
            Route::get('/stripe-data','StripeConfigController@stripeData')->name('stripeData');
            Route::get('/connect-with-stripe','StripeConfigController@connectWithStripe')->name('connectWithStripe');
            Route::post('/disconnect-with-stripe','StripeConfigController@disconnectWithStripe')->name('disconnectWithStripe');
            Route::post('/accept-reject-business-request','ServiceProviderController@acceptRejectBusinessRequest')->name('acceptRejectBusinessRequest');
            Route::post('/provider-details', 'CustomerController@providerDetails')->name('providerDetails');
            Route::post('/business-details', 'CustomerController@businessDetails')->name('businessDetails');
            Route::post('/remove-service-provider-from-business', 'ServiceProviderController@removeServiceProviderFormBusiness')->name('removeServiceProviderFormBusiness');
            Route::post('/service-provider-left-business', 'ServiceProviderController@serviceProviderLeftBusiness')->name('serviceProviderLeftBusiness');
            Route::get('/my-earnings', 'BookServiceController@myEarnings')->name('myEarnings');
            Route::get('/notification-list', 'NotificationController@notificationList')->name('notificationList');
            Route::post('/update-live-booking-status', 'ServiceProviderController@updateLiveBookingStatus')->name('updateLiveBookingStatus');
            Route::get('/following-list', 'FollowController@providerFollowingList')->name('providerFollowingList');
            Route::any('/get-page', 'HomeController@getPage')->name('getProviderPage');
            Route::get('/post-listing', 'PostController@myPosts')->name('myPosts');
            Route::get('/story-details', 'PostController@storyDetails')->name('storyDetails');
            Route::get('/post-likes', 'PostController@postLikes')->name('providerPostLikes');
            Route::post('/add-post-comment', 'CommentController@addPostComment')->name('providerAddPostComment');
            Route::get('/post-comments', 'CommentController@postComments')->name('providerPostComments');
            Route::post('/like-dislike-comment', 'CommentController@likeDislikeComment')->name('providerLikeDislikeComment');
            Route::get('/post-details', 'PostController@postDetails')->name('providerPostDetails');
            Route::post('/delete-post', 'PostController@deletePost')->name('deletePost');
            Route::get('/getProfile', 'AccountController@getProviderProfile')->name('getProviderProfile');            
            Route::post('/contact-us', 'HomeController@contactUs')->name('providerContactUs');
            Route::post('/notification-setting', 'NotificationController@notificationSetting')->name('providerNotificationSetting');
            Route::post('/update-home-booking-status', 'ServiceProviderController@updateHomeBookingStatus')->name('updateHomeBookingStatus');
            Route::get('/get-individual-provider-timing', 'ProviderTimingController@getIndividualProviderTiming')->name('getIndividualProviderTiming');
            Route::post('/update-menu', 'ServiceProviderController@updateMenu')->name('updateMenu');
            Route::get('/customer-details', 'ServiceProviderController@customerDetails')->name('customerDetails');
            Route::post('/report-user', 'ReportUserController@reportUser')->name('provider.reportUser');
  });
            Route::post('/save-firebase-chat-token', 'AccountController@saveFirebaseChatToken')->name('saveFirebaseChatToken');
        
        });
    });
