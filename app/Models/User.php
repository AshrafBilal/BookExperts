<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Stripe\Customer;
use Stripe\Stripe;
use League\Flysystem\Plugin\EmptyDir;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class User extends Authenticatable
{
    use HasFactory, Notifiable,SoftDeletes,HasApiTokens;

    const ROLE_ADMIN = 1;

    const ROLE_USER = 2;

    const ROLE_PROVIDER = 3;

    const ROLE_SPECIALIST = 4;

    const STATUS_ACTIVE = 0;

    const STATUS_INACTIVE = 1;

    const STATUS_PENDING = 2;

    const STATUS_DELETED = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'phone_code',
        'iso_code',
        'password',
        'address',
        'latitude',
        'longitude',
        'country',
        'city',
        'state',
        'role_id',
        'device_type',
        'register_using',
        'fcm_token',
        'notification_status',
        'email_notification',
        'availability_status',
        'social_token',
        'step_completed',
        'profile_verified',
        'time_zone',
        'profile_file',
        'register_type',
        'street',
        'zip_code',
        'available_for_live_booking',
        'firebase_chat_token',
        'verification_type',
        'available_for_home_booking',
        'about_me',
    ];

    private $stripe;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        return parent::__construct();
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public $email_verified;

    protected $appends = [
        'email_verified',
        'otp'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function setPasswordAttribute($value)
    {
        return $this->attributes['password'] = Hash::make($value);
    }

    public function jsonResponse($story = false)
    {
        $json = [];

        $json['id'] = $this->id;
        $json['full_name'] = $this->getFullName();
        $json['first_name'] = $this->first_name;
        $json['last_name'] = $this->last_name;
        $json['email'] = $this->email;
        $json['iso_code'] = $this->iso_code;
        $json['phone_code'] = $this->phone_code;
        $json['phone_number'] = $this->phone_number;
        $json['profile_file'] = asset('admin/images/default_image.png');
        if (! empty($this->profile_file))
            $json['profile_file'] = $this->profile_file;

        $json['profile_identity_file'] = $this->getProfileIdentityFile();
        $json['profile_identity_video'] = $this->getProfileIdentityVideoFile();
        $json['bank_statement'] = $this->getBankStatementFile();
        $json['address'] = $this->address;
        $json['latitude'] = $this->latitude;
        $json['longitude'] = $this->longitude;
        $json['city'] = $this->city;
        $json['street'] = $this->street;
        $json['zip_code'] = $this->zip_code;
        $json['country'] = $this->country;
        $json['otp'] = $this->otp;
        $json['otp_verified'] = $this->otp_verified;
        $json['reset_password_otp'] = $this->reset_password_otp;
        $json['email_verified_at'] = isset($this->email_verified_at) ? $this->email_verified_at : null;
        $json['notification_status'] = isset($this->notification_status) ? $this->notification_status : ACTIVE_STATUS;
        $json['email_notification'] = isset($this->email_notification) ? $this->email_notification : ACTIVE_STATUS;
        $json['availability_status'] = isset($this->availability_status) ? $this->availability_status : ACTIVE_STATUS;
        $json['register_using'] = $this->register_using;
        $json['step_completed'] = json_decode($this->step_completed);
        $json['unique_id'] = $this->unique_id;
        $json['step_completed'] = $this->step_completed;
        $json['device_type'] = $this->device_type;
        $json['fcm_token'] = $this->fcm_token;
        $json['active_status'] = $this->active_status;
        $json['account_type'] = @$this->workProfile->account_type;
        $json['service_category_id'] = @$this->workProfile->service_category_id;
        $json['service_category_name'] = @$this->workProfile->serviceCategory->name;
        $json['role_id'] = (int) $this->role_id;
        $json['social_token'] = $this->social_token;
        $json['created_at'] = $this->created_at;
        $json['updated_at'] = $this->updated_at;
        $json['admin_approved_status'] = $this->getAdminApprovedStatus();
        $json['bank_and_video_file_status'] = $this->getBankAndVideoFileStatus();
        $json['provider_details_verified_status'] = $this->getProviderDetailsVerifiedStatus();
        $json['total_follow'] = $this->getTotalFollow();
        $json['default_address_id'] = $this->getDefaultAddressID();
        $json['default_card_id'] = $this->getDefaultStripeCardID();
        $json['rating'] = $this->getRating();
        $json['stripe_connect_id'] = $this->stripe_connect_id;
        $json['business_id'] = $this->getBusinessID();
        $json['available_for_live_booking'] = $this->available_for_live_booking;
        $json['available_for_home_booking'] = $this->available_for_home_booking;
        $json['provider_total_followers'] = $this->getProviderTotalFollowers();
        $json['firebase_chat_token'] = $this->firebase_chat_token;
        $json['fcm_token'] = $this->fcm_token;
        $json['about_me'] = $this->about_me;
        $json['story'] = [];
        if (! empty($story)) {
            $stories = Post::latest()->where([
                'post_type' => POST_TYPE_STATUS_STORY,
                'user_id' => $this->id
            ])
                ->where('created_at', '>=', Carbon::now()->subDay()
                ->toDateTimeString())
                ->get();
            foreach ($stories as $item) {
                $json['story'][] = $item->jsonResponse(false);
            }
        }
        return $json;
    }

    public function customerReviewJsonResponse()
    {
        $json = [];

        $json['id'] = $this->id;
        $json['full_name'] = $this->getFullName();
        $json['first_name'] = $this->first_name;
        $json['last_name'] = $this->last_name;
        $json['email'] = $this->email;
        $json['iso_code'] = $this->iso_code;
        $json['phone_code'] = $this->phone_code;
        $json['phone_number'] = $this->phone_number;
        $json['profile_file'] = asset('admin/images/default_image.png');
        if (! empty($this->profile_file)) {
            $json['profile_file'] = $this->profile_file;
        }
        $json['rating'] = $this->getRating();
        $json['firebase_chat_token'] = $this->firebase_chat_token;
        $json['fcm_token'] = $this->fcm_token;
        $json['reviews'] = [];
        $reviews = Rating::where([
            'user_id' => $this->id
        ])->whereNull('booking_service_id')->get();
        foreach ($reviews as $review) {

            $json['reviews'][] = @$review->jsonResponse();
        }

        return $json;
    }

    public function getProviderTotalFollowers()
    {
        $follows = Follow::where([
            'user_id' => $this->id
        ])->pluck('follow_by')->toArray();
        return User::whereIn('id', $follows)->where([
            'role_id' => NORMAL_USER_TYPE
        ])->count();
    }

    public function minimizeJsonCustomerResponse()
    {
        $json = [];
        $json['id'] = $this->id;
        $json['full_name'] = $this->getFullName();
        $json['profile_file'] = asset('admin/images/default_image.png');
        if (! empty($this->profile_file)) {
            $json['profile_file'] = $this->profile_file;
        }
        return $json;
    }

    public function minimizeJsonResponse($story = false,$single=false)
    {
        
        $json = [];
        $workProfile = $this->workProfile;
        $json['id'] = $this->id;
        $json['full_name'] = $this->getFullName();
        $json['business_name'] = @$workProfile->business_name;
        $json['about_business'] = @$workProfile->about_business;
        $json['service_category_id'] = @$workProfile->service_category_id;
        $json['service_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName($single) : '';
        $json['account_type'] = @$this->workProfile->account_type;
        $json['email'] = $this->email;
        $json['phone_code'] = $this->phone_code;
        $json['phone_number'] = $this->phone_number;
        $json['firebase_chat_token'] = $this->firebase_chat_token;
        $json['fcm_token'] = $this->fcm_token;
        $json['profile_file'] = asset('admin/images/default_image.png');
        if (! empty($this->profile_file)) {
            $json['profile_file'] = $this->profile_file;
        }
        $user = Auth::user();
        if ($user->role_id == NORMAL_USER_TYPE) {
            $reportedPosts = ReportPost::where('reported_by', $user->id)->pluck('post_id')->toArray();
        } else {
            $reportedPosts = [];
        }
        $json['story'] = [];
        if (! empty($story)) {
            $stories = Post::latest()->where([
                'post_type' => POST_TYPE_STATUS_STORY,
                'user_id' => $this->id
            ])
                ->whereNotIn('id', $reportedPosts)
                ->where('created_at', '>=', Carbon::now()->subDay()
                ->toDateTimeString())
                ->get();
            foreach ($stories as $item) {
                $json['story'][] = $item->jsonResponse(false);
            }
        }
        return $json;
    }

    public function getDefaultAddressID()
    {
        $address = Address::where([
            'user_id' => $this->id,
            'default_address' => ACTIVE_STATUS
        ])->first();
        if (! empty($address)) {
            return $address->id;
        }
        $address = Address::where([
            'user_id' => $this->id
        ])->first();
        if (! empty($address)) {
            $address->default_address = ACTIVE_STATUS;
            $address->save();
            return $address->id;
        }
        return null;
    }

    public function getBusinessID()
    {
        $specialist = Specialist::where([
            'specialist_id' => $this->id,
            'state_id' => ACTIVE_STATUS
        ])->first();
        if (! empty($specialist)) {
            return $specialist->user_id;
        }
        return null;
    }

    public function getBusinessDetails()
    {
        $specialist = Specialist::where([
            'specialist_id' => $this->id,
            'state_id' => ACTIVE_STATUS
        ])->first();

        if (! empty($specialist)) {
            $business = User::find($specialist->user_id);
            if (! empty($business)) {
                return $business->minimizeJsonResponse();
            }
        }
        return (object) [];
    }

    public function getIsProviderLinkedWithBusiness()
    {
        return (int) Specialist::where([
            'specialist_id' => $this->id,
            'state_id' => ACTIVE_STATUS
        ])->exists();
    }

    public function getDefaultStripeCardID()
    {
        if ($this->role_id == NORMAL_USER_TYPE && ! empty($this->stripe_id)) {
            $customer = Customer::retrieve($this->stripe_id);
            if (! empty($customer->default_source)) {
                return $customer->default_source;
            }
        }
        return null;
    }

    public function getTotalFollow()
    {
        return Follow::join('users', 'users.id', 'follows.user_id')->whereNull('users.deleted_at')
            ->where([
            'follow_by' => $this->id,
            'status' => ACTIVE_STATUS
        ])
            ->count();
    }

    public function getBankAndVideoFileStatus()
    {
        $document = $this->geProfileStatusID(3);
        $bankStatement = $this->geProfileStatusID(4);
        if ($document == PROFILE_SUCCESS && $bankStatement == PROFILE_SUCCESS) {
            return ACTIVE_STATUS;
        }
        return INACTIVE_STATUS;
    }

    public function getAdminApprovedStatus()
    {
        $document = $bankStatement = 2;
        $personalProfile = $this->geProfileStatusID(1);
        $workProfile = $this->geProfileStatusID(2);
        if (! empty($this->available_for_home_booking)) {
            $document = $this->geProfileStatusID(3);
            $bankStatement = $this->geProfileStatusID(4);
        }
        if ($personalProfile == PROFILE_SUCCESS && $workProfile == PROFILE_SUCCESS && $document == PROFILE_SUCCESS && $bankStatement == PROFILE_SUCCESS) {
            return ACTIVE_STATUS;
        }
        return INACTIVE_STATUS;
    }

    public function getProviderDetailsVerifiedStatus()
    {
        $bank = BankAccount::where('user_id', $this->id)->exists();
        $location = Address::where('user_id', $this->id)->exists();
        $services = Service::where('user_id', $this->id)->exists();
        if (! empty($bank) && ! empty($location) && ! empty($services)) {
            return ACTIVE_STATUS;
        }
        return INACTIVE_STATUS;
    }

    public function jsonProfileStatusResponse()
    {
        if (! empty($this->available_for_home_booking)) {
            $list = [
                '1' => 'Personal profile',
                '2' => 'Work profile',
                '3' => 'Verify documents',
                '4' => 'Bank statement'
            ];
        } else {
            $personalProfile = $this->geProfileStatusID(1);
            $workProfile = $this->geProfileStatusID(2);
            $bank = BankAccount::where('user_id', $this->id)->exists();
            $location = Address::where('user_id', $this->id)->exists();
            if ($workProfile == PROFILE_SUCCESS && $personalProfile == PROFILE_SUCCESS && ! empty($bank) && ! empty($location)) {
                $list = [
                    '1' => 'Personal profile',
                    '2' => 'Work profile',
                    '3' => 'Verify documents',
                    '4' => 'Bank statement'
                ];
            } else {
                $list = [
                    '1' => 'Personal profile',
                    '2' => 'Work profile'
                ];
            }
        }

        $json = [];
        foreach ($list as $key => $list) {
            $json[] = [
                'name' => $list,
                'status_type' => $key,
                'icon_path' => asset('admin/images/icon_' . $key . ".png"),
                'status' => $this->geProfileStatus($key),
                'status_id' => $this->geProfileStatusID($key)
            ];
        }
        return $json;
    }

    public function jsonProfileStatusDetails($status_type)
    {
        $status = $this->geProfileStatusID($status_type);
        $status = (int) $status;
        $json = [];
        switch ($status_type) {
            case $status_type == 1:
                $json['id'] = $this->id;
                $json['full_name'] = $this->full_name;
                $json['first_name'] = $this->first_name;
                $json['last_name'] = $this->last_name;
                $json['email'] = $this->email;
                $json['address'] = $this->address;
                $json['latitude'] = $this->latitude;
                $json['longitude'] = $this->longitude;
                $json['city'] = $this->city;
                $json['street'] = $this->street;
                $json['zip_code'] = $this->zip_code;
                $json['status'] = $status;
                $json['reject_comment_title'] = ! empty($this->personal_profile_comment_title) ? $this->personal_profile_comment_title : '';
                $json['reject_comment'] = ! empty($this->personal_profile_comment_description) ? $this->personal_profile_comment_description : '';
                $json['profile_file'] = asset('assets/images/default.png');
                if (! empty($this->profile_file))
                    $json['profile_file'] = $this->profile_file;
                break;
            case $status_type == 2:
                $json = $this->jsonResponseWorkProfile();
                $json['status'] = $status;
                $json['reject_comment_title'] = ! empty($this->workProfile->reject_title) ? $this->workProfile->reject_title : '';
                $json['reject_comment'] = ! empty($this->workProfile->reject_description) ? $this->workProfile->reject_description : '';
                break;
            case $status_type == 3:
                $json['id'] = $this->id;
                $json['verification_type'] = $this->verification_type;
                $json['profile_identity_file'] = $this->profile_identity_file;
                $json['profile_identity_video'] = $this->profile_identity_video;
                $json['status'] = $status;
                $json['file_verification_status'] = $this->getFileVerificationStatus();
                $json['video_verification_status'] = $this->getVideoVerificationStatus();
                $json['reject_comment_title'] = ! empty($this->identity_file_comment_title) ? $this->identity_file_comment_title : '';
                $json['reject_comment'] = ! empty($this->identity_file_comment_description) ? $this->identity_file_comment_description : '';
                $json['identity_video_comment_title'] = ! empty($this->identity_video_comment_title) ? $this->identity_video_comment_title : '';
                $json['identity_video_comment_description'] = ! empty($this->identity_video_comment_description) ? $this->identity_video_comment_description : '';
                break;
            case $status_type == 4:
                $json['id'] = $this->id;
                $json['bank_statement'] = $this->bank_statement;
                $json['status'] = $status;
                $json['reject_comment_title'] = ! empty($this->bank_statement_comment_title) ? $this->bank_statement_comment_title : '';
                $json['reject_comment'] = ! empty($this->bank_statement_comment_description) ? $this->bank_statement_comment_description : '';
                break;
        }
        return $json;
    }

    public function getFileVerificationStatus()
    {
        if (empty($this->profile_identity_file)) {
            return PROFILE_NOT_SUBMIT;
        } elseif (empty($this->profile_identity_file_status)) {
            return PROFILE_SUCCESS;
        } elseif ($this->profile_identity_file_status == ACTIVE_STATUS) {
            return PROFILE_SUCCESS;
        }
        return PROFILE_REJECT;
    }

    public function getVideoVerificationStatus()
    {
        if (empty($this->profile_identity_video)) {
            return PROFILE_NOT_SUBMIT;
        } elseif (empty($this->profile_identity_video_status)) {
            return PROFILE_SUCCESS;
        } elseif ($this->profile_identity_video_status == ACTIVE_STATUS) {
            return PROFILE_SUCCESS;
        }
        return PROFILE_REJECT;
    }

    public function jsonResponseWorkProfile($menu = false, $address = false)
    {
        $workProfile = $this->workProfile;
        $json = [];
        $json['id'] = $this->id;
        if (! empty($workProfile)) {
            $json['business_name'] = $workProfile->business_name;
            $json['about_business'] = $workProfile->about_business;
            $json['service_category_id'] = $workProfile->service_category_id;
            $json['service_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName() : '';
            $json['tagline_for_business'] = $workProfile->tagline_for_business;
            $json['company_number'] = $workProfile->company_number;
            $json['account_type'] = $workProfile->account_type;
            $json['firebase_chat_token'] = $this->firebase_chat_token;
            $json['fcm_token'] = $this->fcm_token;
            $json['sub_services'] = $workProfile->getSubServices();
            $json['images'] = $workProfile->getWorkProfileImages();
            $json['specialists'] = $this->getWorkProfileSpecialists();
            $json['rating'] = $this->getRating();
            if (! empty($menu)) {
                $json['menu'] = $this->getMenuResponse();
            }
            if (! empty($address)) {
                $json['address'] = $this->providerAddress->jsonResponse();
            }
        } else {
            $json['message'] = "Worked profile not completed";
        }

        return $json;
    }

    public function jsonResponseSpecialists($menu = false, $address = false, $story = true)
    {
        $workProfile = $this->workProfile;
        $json = [];
        $json['id'] = $this->id;
        if (! empty($workProfile)) {
            $json['email'] = $this->email;
            $json['profile_file'] = $this->getProfilePhoto();
            $json['business_name'] = $workProfile->business_name;
            $json['about_business'] = $workProfile->about_business;
            $json['service_category_id'] = $workProfile->service_category_id;
            $json['service_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName() : '';
            $json['tagline_for_business'] = $workProfile->tagline_for_business;
            $json['company_number'] = $workProfile->company_number;
            $json['account_type'] = $workProfile->account_type;
            $json['firebase_chat_token'] = $this->firebase_chat_token;
            $json['fcm_token'] = $this->fcm_token;
            $json['is_timeslots_available'] = $this->isTimeslotsAvailable();
            $json['available_timeslots'] = ProviderTiming::getProviderTiming($this->id);
            $json['sub_services'] = $workProfile->getSubServices();
            $json['rating'] = $this->getRating();
            $json['images'] = $workProfile->getWorkProfileImages();
            $json['specialists'] = $this->getBusinessProfileSpecialists();
            $json['reviews'] = $this->getReviews();
            $json['business_pending_request'] = $this->getBusinessPendingRequest();
            $json['business_id'] = $this->getBusinessID();
            $json['available_for_live_booking'] = $this->available_for_live_booking;
            $json['available_for_home_booking'] = $this->available_for_home_booking;
            $json['is_follow'] = $this->isFollow();
            $json['firebase_chat_token'] = $this->firebase_chat_token;
            $json['fcm_token'] = $this->fcm_token;
            if (! empty($menu)) {
                $json['menu'] = $this->getMenuResponse();
            }
            if (! empty($address) && ! empty($this->providerAddress)) {
                $json['address'] = $this->providerAddress->jsonResponse();
            }
            $json['story'] = [];
            if (! empty($story)) {
                $stories = Post::latest()->where([
                    'post_type' => POST_TYPE_STATUS_STORY,
                    'user_id' => $this->id
                ])
                    ->where('created_at', '>=', Carbon::now()->subDay()
                    ->toDateTimeString())
                    ->get();
                foreach ($stories as $item) {
                    $json['story'][] = $item->jsonResponse(false);
                }
            }
        } else {
            $json['message'] = "Worked profile not completed";
        }

        return $json;
    }

    public function getBusinessPendingRequest()
    {
        $request = Specialist::where([
            'specialist_id' => Auth::id(),
            'user_id' => $this->id,
            'state_id' => INACTIVE_STATUS
        ])->first();
        return ! empty($request) ? $request : (object) [];
    }

    public function getReviews()
    {
        $reviews = Rating::where('service_provider_id', $this->id)->whereNotNull('booking_service_id')
            ->limit(5)
            ->latest()
            ->get();
        if (! empty($reviews->count())) {
            foreach ($reviews as $review) {
                $createdBy = User::find($review->created_by);
                $review->customer_image = @$createdBy->profile_file;
                $review->customer_name = @$createdBy->getFullName();
                $review->created_by = null;
            }
            return $reviews;
        }
        return [];
    }

    public function jsonResponseIndividualProvider($story = true)
    {
        $workProfile = $this->workProfile;
        $json = [];
        $json['id'] = $this->id;
        if (! empty($workProfile)) {
            $json['full_name'] = $this->getFullName();
            $json['email'] = $this->email;
            $json['profile_file'] = $this->profile_file;
            $json['service_category_id'] = $workProfile->service_category_id;
            $json['service_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName() : '';
            $json['account_type'] = $workProfile->account_type;
            $json['is_timeslots_available'] = $this->isTimeslotsAvailable();
            $json['about_business'] = @$workProfile->about_business;
            $json['tagline_for_business'] = @$workProfile->tagline_for_business;
            $json['about'] = $this->about;
            $json['available_for_live_booking'] = $this->available_for_live_booking;
            $json['available_for_home_booking'] = $this->available_for_home_booking;
            $json['firebase_chat_token'] = $this->firebase_chat_token;
            $json['fcm_token'] = $this->fcm_token;
            $json['rating'] = $this->getRating();
            $json['is_follow'] = $this->isFollow();
            $json['is_home_services'] = $this->isHomeServices();
            $json['images'] = $workProfile->getWorkProfileImages();
            $json['address'] = $this->providerAddress->jsonResponse();
            $json['reviews'] = $this->getReviews();
            $json['business_pending_request'] = $this->getBusinessPendingRequest();
            $json['business_details'] = $this->getBusinessDetails();
            $json['sub_services'] = $workProfile->getSubServices();
            $json['story'] = [];
            if (! empty($story)) {
                $stories = Post::latest()->where([
                    'post_type' => POST_TYPE_STATUS_STORY,
                    'user_id' => $this->id
                ])
                    ->where('created_at', '>=', Carbon::now()->subDay()
                    ->toDateTimeString())
                    ->get();
                foreach ($stories as $item) {
                    $json['story'][] = $item->jsonResponse(false);
                }
            }
        } else {
            $json['message'] = "Worked profile not completed";
        }
        return $json;
    }

    public function isTimeslotsAvailable()
    {
        $status = (int) ProviderTiming::where([
            'user_id' => $this->id
        ])->exists();

        if (empty($status) && ! empty($this->workProfile)) {

            $defaultData = json_decode('{"days":[{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]},{"full_day":"1","time":["09:00 to 19:00"]}]}');
            $days = $defaultData->days;
            foreach ($days as $day => $time) {
                ProviderTiming::where([
                    'day' => $day,
                    'user_id' => $this->id
                ])->delete();
                $timeArray = explode('to', $time->time[0]);
                $start_time = @$timeArray[0];
                $end_time = @$timeArray[1];
                $model = new ProviderTiming();
                $model->day = $day;
                $model->off_day_type = $time->full_day;
                $model->start_time = $start_time;
                $model->end_time = $end_time;
                $model->user_id = $this->id;
                $model->save();
            }
            $status = ACTIVE_STATUS;
        }
        return $status;
    }

    public function isHomeServices()
    {
        return (int) DB::table('services')->where('user_id', $this->id)
            ->where(function ($query) {
            $query->where('service_visit', BOOKING_TYPE_BOTH)
                ->orWhere('service_visit', BOOKING_TYPE_VISIT_HOMES);
        })
            ->exists();
    }

    public function getMenuResponse()
    {
        $services = Service::where([
            'user_id' => $this->id
        ])->get();
        $response = [];

        foreach ($services as $key => $service) {
            $response[] = $service->jsonResponse();
        }
        return $response;
    }

    public function getWorkProfileSpecialists()
    {
        $specialist_id = Specialist::select('specialist_id', 'state_id')->where([
            'user_id' => $this->id,
            'state_id' => ACTIVE_STATUS
        ])
            ->get()
            ->toArray();
        $serviceProviders = self::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->whereIn('id', $specialist_id)->get();
        $response = [];

        foreach ($serviceProviders as $key => $serviceProvider) {
            $response[] = $serviceProvider->jsonResponse();
        }
        return $response;
    }

    public function getBusinessProfileSpecialists()
    {
        $specialist_id = Specialist::select('specialist_id')->where([
            'user_id' => $this->id,
            'state_id' => ACTIVE_STATUS
        ])
            ->get()
            ->toArray();
        $serviceProviders = self::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->whereIn('id', $specialist_id)->get();
        $customJson = [];
        $data = [];
        foreach ($serviceProviders as $serviceProvider) {
            $workProfile = $serviceProvider->WorkProfile;
            $data['user_id'] = $serviceProvider->id;
            $data['full_name'] = $serviceProvider->getFullName();
            $data['profile_file'] = $serviceProvider->profile_file;
            $data['main_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName() : '';
            $data['all_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName(true) : '';
            $data['is_follow'] = $serviceProvider->isFollow();
            $data['is_timeslots_available'] = (int) ProviderTiming::where([
                'user_id' => $serviceProvider->id
            ])->exists();
            $customJson[] = $data;
        }
        return $customJson;
    }

    public function getFullName()
    {
        if (! empty($this->first_name) && $this->deleted_at == null) {
            $model = self::where('id', $this->id)->first();
            if (! empty($model)) {
                $model->full_name = ucwords($this->first_name . " " . $this->last_name);
                $model->save();
            }
        }
        return ucwords($this->first_name . " " . $this->last_name);
    }

    public function geProfileStatus($status_type)
    {
        switch ($status_type) {
            case $status_type == 1:
                if (empty($this->first_name)) {
                    return PROFILE_NOT_SUBMIT_STATUS;
                }
                if (empty($this->profile_verified)) {
                    return PROFILE_SUBMIT_STATUS;
                } else if ($this->profile_verified == 1) {
                    return PROFILE_SUCCESS_STATUS;
                } else {
                    return PROFILE_REJECT_STATUS;
                }
                break;
            case $status_type == 2:
                if (empty($this->workProfile)) {
                    return PROFILE_NOT_SUBMIT_STATUS;
                } else if ($this->workProfile->status == 1) {
                    return PROFILE_SUCCESS_STATUS;
                } else if ($this->workProfile->status == 0) {
                    return PROFILE_SUBMIT_STATUS;
                } else {
                    return PROFILE_REJECT_STATUS;
                }
                break;
            case $status_type == 3:
                if (empty($this->profile_identity_video) && empty($this->profile_identity_file)) {
                    return PROFILE_NOT_SUBMIT_STATUS;
                } else if ($this->profile_identity_video_status == 0 || $this->profile_identity_file_status == 0) {
                    return PROFILE_SUBMIT_STATUS;
                } else if ($this->profile_identity_video_status == 1 && $this->profile_identity_file_status == 1) {
                    return PROFILE_SUCCESS_STATUS;
                } else {
                    return PROFILE_REJECT_STATUS;
                }
                break;
            case $status_type == 4:
                if (empty($this->bank_statement)) {
                    return PROFILE_NOT_SUBMIT_STATUS;
                } else if ($this->bank_statement_file_status == 0) {
                    return PROFILE_SUBMIT_STATUS;
                } else if ($this->bank_statement_file_status == 1) {
                    return PROFILE_SUCCESS_STATUS;
                } else {
                    return PROFILE_REJECT_STATUS;
                }
                break;

            default:
                return PROFILE_REJECT_STATUS;
                break;
        }
    }

    public function geProfileStatusID($status_type)
    {
        switch ($status_type) {
            case $status_type == 1:
                if (empty($this->first_name)) {
                    return PROFILE_NOT_SUBMIT;
                }
                if (empty($this->profile_verified)) {
                    return PROFILE_SUBMIT;
                } else if ($this->profile_verified == 1) {
                    return PROFILE_SUCCESS;
                } else {
                    return PROFILE_REJECT;
                }
                break;
            case $status_type == 2:
                if (empty($this->workProfile)) {
                    return PROFILE_NOT_SUBMIT;
                } else if ($this->workProfile->status == 1) {
                    return PROFILE_SUCCESS;
                } else if ($this->workProfile->status == 0) {
                    return PROFILE_SUBMIT;
                } else {
                    return PROFILE_REJECT;
                }
                break;
            case $status_type == 3:
                if (empty($this->profile_identity_video) && empty($this->profile_identity_file)) {
                    return PROFILE_NOT_SUBMIT;
                } else if ($this->profile_identity_video_status == 2 || $this->profile_identity_file_status == 2) {
                    return PROFILE_REJECT;
                } else if ($this->profile_identity_video_status == 0 || $this->profile_identity_file_status == 0) {
                    return PROFILE_SUBMIT;
                } else if ($this->profile_identity_video_status == 1 && $this->profile_identity_file_status == 1) {
                    return PROFILE_SUCCESS;
                } else {
                    return PROFILE_REJECT;
                }
                break;
            case $status_type == 4:
                if (empty($this->bank_statement)) {
                    return PROFILE_NOT_SUBMIT;
                } else if ($this->bank_statement_file_status == 0) {
                    return PROFILE_SUBMIT;
                } else if ($this->bank_statement_file_status == 1) {
                    return PROFILE_SUCCESS;
                } else {
                    return PROFILE_REJECT;
                }
                break;

            default:
                return PROFILE_REJECT;
                break;
        }
    }

    public function getProviderDetailStep()
    {
        $list = [
            '1' => 'Add Menu',
            '2' => 'Add Location',
            '3' => 'Add Bank Account'
        ];
        $json = [];
        foreach ($list as $key => $list) {
            $json[] = [
                'name' => $list,
                'type' => $key,
                'icon_path' => asset('admin/images/menu_' . $key . ".png"),
                // 'data' => $this->getProviderDetailStepData($key),
                'status' => $this->getProviderDetailStepStatus($key)
            ];
        }
        return $json;
    }

    public function getProviderDetailStepData($type)
    {
        if ($type == 1) {
            return $this->getMenu();
        } elseif ($type == 2) {
            $location = Address::where('user_id', $this->id)->first();
            return ! empty($location) ? $location->jsonResponse() : [];
        } else {
            $BankAccount = BankAccount::where('user_id', $this->id)->first();
            return ! empty($BankAccount) ? $BankAccount->jsonResponse() : [];
        }
    }

    public function getProviderDetailStepStatus($type)
    {
        if ($type == 1) {
            return Service::where('user_id', $this->id)->exists();
        } elseif ($type == 2) {
            return Address::where('user_id', $this->id)->exists();
        } else {
            return BankAccount::where('user_id', $this->id)->exists();
        }
    }

    public function getMenu()
    {
        return Service::where('user_id', $this->id)->get();
    }

    public function generateOtp()
    {
        $otp = mt_rand(1000, 9999);
        // $otp = 123456;
        // return $otp;
        $count = self::where('otp', $otp)->count();
        if ($count > 0) {
            $this->generateOtp();
        }
        return $otp;
    }

    public function generateUniqueId()
    {
        $uniqueId = mt_rand(10000000, 99999999);
        $count = self::where('unique_id', $uniqueId)->count();

        if ($count > 0) {
            $this->generateUniqueId();
        }

        return $uniqueId;
    }

    public static function getUser($registerType, $request)
    {
        $model = null;
        if ($registerType == REGISTERED_USING_EMAIL) {
            $email = $request->email;
            $model = self::where([
                'email' => $email
            ]);
            if (! empty($request->role_id)) {
                $model = $model->where('role_id', $request->role_id);
            }
            $model = $model->first();
        }

        if ($registerType == REGISTERED_USING_PHONE) {
            $phone_number = $request->phone_number;
            $model = self::where([
                'phone_number' => $phone_number
            ]);
            if (! empty($request->phone_number)) {
                $model = $model->where('phone_number', $request->phone_number);
            }
            if (! empty($request->role_id)) {
                $model = $model->where('role_id', $request->role_id);
            }
            $model = $model->first();
        }

        return $model;
    }

    public function getOtpAttribute()
    {
        if (! empty($this->attributes['otp'])) {
            return (int) $this->attributes['otp'];
        }
        return null;
    }

    public function getEmailVerifiedAttribute()
    {
        if (! empty($this->attributes['email_verified_at'])) {
            return $this->attributes['email_verified'] = true;
        }
        return $this->attributes['email_verified'] = false;
    }

    public function delete()
    {
        Specialist::where('user_id', $this->id)->delete();
        Post::where('user_id', $this->id)->get()->each(function ($post) {
            $post->delete();
        });
        Comment::where('user_id', $this->id)->get()->each(function ($comment) {
            $comment->delete();
        });
        Like::where('user_id', $this->id)->get()->each(function ($like) {
            $like->delete();
        });
        $this->unlinkFiles();
        return parent::delete();
    }

    public function unlinkFiles()
    {
        $profile_file = basename($this->profile_file);
        if (! empty($profile_file)) {
            @Storage::disk('images')->delete($profile_file);
        }
        $profile_identity_file = basename($this->profile_identity_file);
        if (! empty($profile_identity_file)) {
            @Storage::disk('images')->delete($profile_identity_file);
        }
        $profile_identity_video = basename($this->profile_identity_video);
        if (! empty($profile_identity_video)) {
            @Storage::disk('videos')->delete($profile_identity_video);
        }

        $bank_statement = basename($this->bank_statement);
        if (! empty($bank_statement)) {
            @Storage::disk('images')->delete($bank_statement);
        }
        return true;
    }

    public function unlinkProfileFile()
    {
        $profile_file = basename($this->profile_file);
        if (! empty($profile_file)) {
            @Storage::disk('images')->delete($profile_file);
        }
    }

    public function unlinkProfileIdentityFile()
    {
        $file = basename($this->profile_identity_file);
        if (! empty($file)) {
            @Storage::disk('images')->delete($file);
        }
    }

    public function unlinkProfileVideo()
    {
        $file = basename($this->profile_identity_video);
        if (! empty($file)) {
            @Storage::disk('videos')->delete($file);
        }
    }

    public function unlinkBankStatemenetFile()
    {
        $file = basename($this->bank_statement);
        if (! empty($file)) {
            @Storage::disk('images')->delete($file);
        }
    }

    public function getProfilePhoto($default = true)
    {
        if ($this->register_type != REGISTER_TYPE_BASIC && ! empty($this->profile_file)) {
            return $this->profile_file;
        }
        $exists = Storage::disk('images')->exists(basename($this->profile_file));
        if ($exists) {
            return $this->profile_file;
        } else {
            return asset('admin/images/default_image.png');
        }
        return null;
    }

    public function getBankStatementFile($default = true)
    {
        $exists = Storage::disk('images')->exists(basename($this->bank_statement));
        if ($exists) {
            return $this->bank_statement;
        } else {
            return asset('admin/images/default_image.png');
        }
        return null;
    }

    public function getProfileIdentityFile($default = true)
    {
        $exists = Storage::disk('images')->exists(basename($this->profile_identity_file));
        if ($exists) {
            return $this->profile_identity_file;
        } else {
            return asset('admin/images/default_image.png');
        }
        return null;
    }

    public function getProfileIdentityVideoFile($default = true)
    {
        $exists = Storage::disk('videos')->exists(basename($this->profile_identity_video));
        if ($exists) {
            return $this->profile_identity_video;
        } else {
            return asset('admin/images/video_sample.mp4');
        }
        return null;
    }

    /* CHANGE PASSWORD OF USER */
    public static function changePassword($data)
    {
        $user = User::find($data['id']);
        $user->password = $data['password'];
        $user->password_reset_token = null;
        return $user->save();
    }

    public static function getPrivacyPolicy()
    {
        return url('privacy-policy');
        $page = Page::where('page_type', PRIVACY_POLICY)->value('description');
        return ! empty($page) ? $page : "";
    }

    public static function getTermCondition()
    {
        return url('term-condition');
        $page = Page::where('page_type', TERMS_AND_CONDITION)->value('description');
        return ! empty($page) ? $page : "";
    }

    public static function getAboutUs()
    {
        return url('about-us');
        $page = Page::where('page_type', ABOUT_US)->value('description');
        return ! empty($page) ? $page : "";
    }

    public function authSessions()
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }

    public function saveUploadedFile($request, $folder)
    {
        $extension = $request->extension();
        $fileName = basename($request->getClientOriginalName());
        $fileName = time() . '-' . uniqueId() . '.' . $extension;
        $request->storeAs("/public/upload/$folder", $fileName);

        return $fileName;
    }

    public function getAllCustomers($request = null)
    {
        $query = self::orderBy('id', 'DESC');
        if (! empty($request->role_id)) {
            $query->where('role_id', $request->role_id);
        }

        $query = $query->get();
        return $query;
    }

    public function getFileStatus($attribute)
    {
        if (empty($this->$attribute)) {
            return "Pending";
        } elseif ($this->$attribute == ACTIVE_STATUS) {
            return "Approved";
        }
        return "Rejected";
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workProfile()
    {
        return $this->hasOne(WorkProfile::class);
    }

    public function providerAddress()
    {
        return $this->hasOne(Address::class);
    }

    public function providerRatings()
    {
        $this->hasMany(Rating::class, 'id', 'service_provider_id');
    }

    public function otherCategories()
    {
        return $this->hasMany(ProviderOtherCategory::class, 'user_id', 'id');
    }

    public static function getNearByServiceProviders($request)
    {
        $customJson = [];
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 10;
        $nearby_radius = ! empty(settings('provider_nearby_radius')) ? settings('provider_nearby_radius') : 100;
        $customerLatitude = $request->latitude;
        $customerLongitude = $request->longitude;
        $query = self::where([
            'users.role_id' => SERVICE_PROVIDER_USER_TYPE,
            'users.profile_verified' => ACTIVE_STATUS
        ])->leftJoin('ratings', 'ratings.service_provider_id', '=', 'users.id')
            ->join("addresses", "addresses.user_id", "=", "users.id")
            ->join("work_profiles", "work_profiles.user_id", "=", "users.id")
            ->join("services", "services.user_id", "=", "users.id")
            ->leftJoin('provider_other_categories', 'users.id', '=', 'provider_other_categories.user_id')
            ->leftJoin('service_categories', 'work_profiles.service_category_id', '=', 'service_categories.id')
            ->leftJoin('sub_service_categories', 'provider_other_categories.sub_service_category_id', '=', 'sub_service_categories.id')
            ->whereNotNull("work_profiles.user_id")
            ->whereNull('users.deleted_at')
            ->select('users.*', DB::raw('avg(ratings.rating) as rating'), DB::raw(sprintf('(6371 * acos(cos(radians(%1$.7f)) * cos(radians(addresses.latitude)) * cos(radians(addresses.longitude) - radians(%2$.7f)) + sin(radians(%1$.7f)) * sin(radians(addresses.latitude)))) AS distance', $customerLatitude, $customerLongitude)))
            ->groupBy("id")
            ->having('distance', '<', $nearby_radius);

        if (! empty($request->keyword)) {

            $query->where(function ($query) use ($request) {
                $query->where([
                    'users.role_id' => SERVICE_PROVIDER_USER_TYPE,
                    'users.profile_verified' => ACTIVE_STATUS
                ]);
                $query->where('users.first_name', 'like', "%{$request->keyword}%");
                $query->orWhere('users.last_name', 'like', "%{$request->keyword}%");
                $query->orWhere('users.full_name', 'like', "%{$request->keyword}%");
                $query->orWhere('service_categories.name', 'like', "%{$request->keyword}%");
                $query->orWhere('work_profiles.business_name', 'like', "%{$request->keyword}%");
                $query->orWhere('sub_service_categories.name', 'like', "%{$request->keyword}%");
            });
        }

        if ($request->sort_by_rating == SORT_BY_RATING_DESC) {
            $query = $query->orderBy('rating', 'desc');
        }

        if ($request->sort_by_rating == SORT_BY_RATING_ASC) {
            $query = $query->orderBy('rating', 'asc');
        }

        if ($request->sort_by_visit_type == SORT_BY_AVAIABLE_HOME_SERVICES) {
            $query = $query->where('users.available_for_home_booking', ACTIVE_STATUS);
        }

        if (! empty($request->service_category_id)) {
            $query = $query->where('work_profiles.service_category_id', $request->service_category_id);
        }

        if (! empty($request->sub_service_category_id)) {
            /*  $subCatName = SubServiceCategory::find($request->sub_service_category_id);
           $keyWord = !empty($subCatName->name)?$subCatName->name:$request->sub_service_category_id;
           $query->orWhere('sub_service_categories.name', 'like', "%{$keyWord}%");
           $query->where(function ($query) use ($keyWord) {
                $query->where([
                    'users.role_id' => SERVICE_PROVIDER_USER_TYPE,
                    'users.profile_verified' => ACTIVE_STATUS
                ]);
                $query->where('sub_service_categories.name', 'like', "%{$keyWord}%");
            }); */
             $query->where('provider_other_categories.sub_service_category_id', $request->sub_service_category_id);
        }

        if ($request->sort_by_visit_type == SORT_BY_AVAIABLE_WOK_PLACE_SERVICES) {

            $query = $query->where('users.available_for_home_booking', INACTIVE_STATUS);
        }

        $reportedProviders = ReportUser::where('reported_by', auth::id())->pluck('report_to')->toArray();

        if (! empty($reportedProviders) && is_array($reportedProviders) && ! empty(count($reportedProviders))) {
            $query = $query->whereNotIn('users.id', $reportedProviders);
        }

        $query = $query->orderBy('distance')->paginate($page_limit);
        $items = $query->items();
        $finalJson = [];
        foreach ($items as $key => $item) {

            $workProfile = $item->WorkProfile;
            $subcategories = json_decode($workProfile->sub_service_category_id, true);
             foreach ($subcategories as $tempcat) {
                $exist = ProviderOtherCategory::where([
                    'user_id' => $item->id,
                    'sub_service_category_id' => $tempcat
                ])->first();

                $model = ! empty($exist) ? $exist : new ProviderOtherCategory();
                $model->user_id = $item->id;
                $model->sub_service_category_id = $tempcat;
                $model->save();
            } 
            $address = $item->providerAddress;
            $customJson['user_id'] = $item->id;
            $customJson['full_name'] = $item->getFullName();
            $customJson['first_name'] = $item->first_name;
            $customJson['last_name'] = $item->last_name;
            $customJson['profile_file'] = $item->profile_file;
            $customJson['business_name'] = $workProfile->business_name;
            $customJson['tagline_for_business'] = $workProfile->tagline_for_business;
            $customJson['account_type'] = $workProfile->account_type;
            $customJson['address'] = @$address->address1;
            $customJson['latitude'] = @$address->latitude;
            $customJson['longitude'] = @$address->longitude;
            $customJson['city'] = @$address->city;
            $customJson['street'] = @$address->street;
            $customJson['zip_code'] = @$address->zip_code;
            $customJson['rating'] = @$item->getRating();
            $customJson['total_distance'] = @$item->distance;
            $customJson['main_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName() : '';
            $customJson['all_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName(true) : '';
            $customJson['selected_sub_category'] = $workProfile->sub_service_category_id;
            $sub_category_list = [];
            foreach ($item->otherCategories as $key => $otherCategory) {
                $sub_category_list[] = $otherCategory->jsonResponse();
            }
            $customJson['sub_category_list'] = $sub_category_list;
            
            $customJson['is_follow'] = $item->isFollow();
            $customJson['is_home_services'] = @$item->isHomeServices();
            $customJson['available_for_live_booking'] = $item->available_for_live_booking;
            $customJson['available_for_home_booking'] = $item->available_for_home_booking;
            $customJson['firebase_chat_token'] = $item->firebase_chat_token;
            $customJson['fcm_token'] = $item->fcm_token;
            $customJson['is_linked_with_business'] = $item->getIsProviderLinkedWithBusiness();

            $files = $workProfile->getFiles();
            $response = [];
            foreach ($files as $key => $file) {
                $response[] = $file->jsonResponse();
            }
            $customJson['work_profile_image'] = @$response[0]['file'];
            $customJson['work_profile_images'] = @$response;
            $finalJson[] = $customJson;
        }

        $data['next_page'] = ($query->nextPageUrl()) ? true : false;
        $data['providers'] = $finalJson;
        $data['current_page'] = $query->currentPage();
        $data['per_page'] = $query->perPage();
        $data['links'] = $query->linkCollection();
        $data['total'] = $query->total();
        $data['total_pages'] = (int) ceil($query->total() / $query->perPage());
        return $data;
    }

    public function getRating()
    {
        if ($this->role_id == SERVICE_PROVIDER_USER_TYPE) {
            $rating = DB::table('ratings')->where('service_provider_id', $this->id)
                ->select(DB::raw('avg(rating) as average_rating'))
                ->value('average_rating');
        } else {
            $rating = DB::table('ratings')->where('user_id', $this->id)
                ->select(DB::raw('avg(rating) as average_rating'))
                ->whereNull('booking_service_id')
                ->value('average_rating');
        }

        return number_format($rating, 1);
    }

    public static function getRecommendedProviders($request)
    {
        $user = Auth::user();
        $customJson = [];
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 20;
        $nearby_radius = ! empty(settings('provider_nearby_radius')) ? settings('provider_nearby_radius') : 100;
        $customerLatitude = $request->latitude;
        $customerLongitude = $request->longitude;
        $follows = Follow::where('follow_by', $user->id)->pluck('user_id')->toArray();

        $query = self::where([
            'users.role_id' => SERVICE_PROVIDER_USER_TYPE,
            'users.profile_verified' => ACTIVE_STATUS
        ])->leftJoin('ratings', 'ratings.service_provider_id', '=', 'users.id')
            ->join("addresses", "addresses.user_id", "=", "users.id")
            ->join("work_profiles", "work_profiles.user_id", "=", "users.id")
            ->whereNotNull("work_profiles.user_id")
            ->whereNull('users.deleted_at')
            ->whereNotIn('users.id', $follows)
            ->select('users.*', DB::raw(sprintf('(6371 * acos(cos(radians(%1$.7f)) * cos(radians(addresses.latitude)) * cos(radians(addresses.longitude) - radians(%2$.7f)) + sin(radians(%1$.7f)) * sin(radians(addresses.latitude)))) AS distance', $customerLatitude, $customerLongitude)))
            ->having('distance', '<', $nearby_radius)
            ->groupBy("id");
        if (! empty($request->keyword)) {
            $query->where(function ($query) use ($request) {
                $query->where('users.first_name', 'like', "%{$request->keyword}%");
                $query->orWhere('users.last_name', 'like', "%{$request->keyword}%");
            });
        }

        if ($request->rating_sort_by == SORT_BY_RATING_DESC) {
            $query = $query->orderBy('ratings.rating', 'desc');
        }

        if ($request->rating_sort_by == SORT_BY_RATING_ASC) {
            $query = $query->orderBy('ratings.rating', 'asc');
        }

        if (! empty($request->keyword)) {
            $query->where(function ($query) use ($request) {
                $query->where('users.first_name', 'like', "%{$request->keyword}%");
                $query->orWhere('users.last_name', 'like', "%{$request->keyword}%");
            });
        }

        $reportedProviders = ReportUser::where('reported_by', $user->id)->pluck('report_to')->toArray();

        if (! empty($reportedProviders) && is_array($reportedProviders) && ! empty(count($reportedProviders))) {
            $query = $query->whereNotIn('users.id', $reportedProviders);
        }

        $query = $query->orderBy('distance', 'asc')->paginate($page_limit);
        $items = $query->items();
        $finalJson = [];
        foreach ($items as $key => $item) {
            $workProfile = $item->WorkProfile;
            $address = $item->providerAddress;
            $customJson['user_id'] = $item->id;
            $customJson['full_name'] = $item->getFullName();
            $customJson['first_name'] = $item->first_name;
            $customJson['last_name'] = $item->last_name;
            $customJson['profile_file'] = $item->profile_file;
            $customJson['business_name'] = $workProfile->business_name;
            $customJson['tagline_for_business'] = $workProfile->tagline_for_business;
            $customJson['account_type'] = $workProfile->account_type;
            $customJson['address'] = @$address->address1;
            $customJson['latitude'] = @$address->latitude;
            $customJson['longitude'] = @$address->longitude;
            $customJson['city'] = @$address->city;
            $customJson['street'] = @$address->street;
            $customJson['zip_code'] = @$address->zip_code;
            $customJson['rating'] = @$item->getRating();
            $customJson['total_distance'] = @$item->distance;
            $customJson['main_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName(true) : '';
            $customJson['all_category_name'] = @! empty($workProfile) ? $workProfile->getServiceCategoryName(true) : '';
            $customJson['is_follow'] = $item->isFollow();
            $customJson['is_home_services'] = @$item->isHomeServices();
            $customJson['available_for_live_booking'] = $item->available_for_live_booking;
            $customJson['available_for_home_booking'] = $item->available_for_home_booking;
            $customJson['firebase_chat_token'] = $item->firebase_chat_token;
            $customJson['fcm_token'] = $item->fcm_token;
            $customJson['is_linked_with_business'] = $item->getIsProviderLinkedWithBusiness();

            $files = $workProfile->getFiles();
            $response = [];
            foreach ($files as $key => $file) {
                $response[] = $file->jsonResponse();
            }

            $customJson['work_profile_image'] = @$file->file;
            $customJson['work_profile_images'] = @$response;
            $finalJson[] = $customJson;
        }

        $data['next_page'] = ($query->nextPageUrl()) ? true : false;
        $data['providers'] = $finalJson;
        $data['current_page'] = $query->currentPage();
        $data['per_page'] = $query->perPage();
        $data['links'] = $query->linkCollection();
        $data['total'] = $query->total();
        $data['total_pages'] = (int) ceil($query->total() / $query->perPage());
        return $data;
    }

    public function isFollow()
    {
        return (int) Follow::where([
            'status' => ACTIVE_STATUS,
            'user_id' => $this->id,
            'follow_by' => Auth::id()
        ])->exists();
    }

    public static function getServiceProvider($id)
    {
        $provider = self::where('id', $id)->where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'profile_verified' => ACTIVE_STATUS
        ])->first();
        return $provider;
    }

    public function sendOtpVerificationSms()
    {
        $sid = setting('twilio_sid'); // Your Account SID from www.twilio.com/console
        $token = setting('twilio_auth_token'); // Your Auth Token from www.twilio.com/console
        $phone_code =  preg_replace('/^(\d+)$/',"+$1",$this->phone_code);
        $client = new Client($sid, $token);
        $message = "DO NOT SHARE: Your Just Say What Account Verification OTP is ".$this->otp;
        $message = $client->messages->create(
            $phone_code.$this->phone_number, // Text this number
            [
                'from' => '+16292380875', // From a valid Twilio number
                'body' => $message
            ]
            );
        
        return $message->sid;
    }
    
}
