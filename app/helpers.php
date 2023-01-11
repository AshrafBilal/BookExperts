 <?php
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use App\Models\File;
use App\Models\ServiceCategory;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

const REGISTERED_USING_EMAIL = 0;

const REGISTERED_USING_PHONE = 1;

const OTP_NOT_VERIFIED = 0;

const OTP_VERIFIED = 1;

const ACCOUNT_TYPE_INDIVIDUAL = 1;

const ACCOUNT_TYPE_BUSINESS = 2;

const CATEGORY_TYPE_NORMAL = 1;

const CATEGORY_TYPE_OTHER = 2;

if (! function_exists('totalPendingAccountHomeServices')) {

    function totalPendingAccountHomeServices()
    {
        return User::whereNotNull([
            'profile_identity_video',
            'bank_statement'
        ])->where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'profile_identity_video_status' => INACTIVE_STATUS,
            'bank_statement_file_status' => INACTIVE_STATUS
        ])->count();
    }
}

if (! function_exists('totalApprovedAccountHomeServices')) {

    function totalApprovedAccountHomeServices()
    {
        return User::whereNotNull([
            'profile_identity_video',
            'bank_statement'
        ])->where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'profile_identity_video_status' => ACTIVE_STATUS,
            'bank_statement_file_status' => ACTIVE_STATUS
        ])->count();
    }
}

if (! function_exists('totalIndividualServiceProviders')) {

    function totalIndividualServiceProviders()
    {
        return User::join('work_profiles', 'work_profiles.user_id', '=', 'users.id')->where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'work_profiles.account_type' =>INDIVIDUAL_PROFILE,
            'profile_identity_video_status' => ACTIVE_STATUS,
            'bank_statement_file_status' => ACTIVE_STATUS
        ])->count();
    }
}

if (! function_exists('totalBusinessServiceProviders')) {

    function totalBusinessServiceProviders()
    {
        return User::join('work_profiles', 'work_profiles.user_id', '=', 'users.id')->where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'work_profiles.account_type' => BUSINESS_PROFILE,
            'profile_identity_video_status' => ACTIVE_STATUS,
            'bank_statement_file_status' => ACTIVE_STATUS
        ])->count();
    }
}

if (! function_exists('getOrderNotificationType')) {

    function getOrderNotificationType($status)
    {
        $notificationType = NOTIFICATION_TYPE_DEFAULT;

        if ($status == BOOKING_PENDING) {
            return NOTIFICATION_TYPE_BOOKING_PENDING;
        }

        switch ($status) {
            case $status == BOOKING_PENDING:
                $notificationType = NOTIFICATION_TYPE_BOOKING_PENDING;
                break;
            case $status == BOOKING_ACCEPT:
                $notificationType = NOTIFICATION_TYPE_BOOKING_ACCEPT;
                break;
            case $status == BOOKING_REJECT:
                $notificationType = NOTIFICATION_TYPE_BOOKING_REJECT;
                break;
            case $status == BOOKING_IN_PROGRESS:
                $notificationType = NOTIFICATION_TYPE_BOOKING_IN_PROGRESS;
                break;
            case $status == BOOKING_COMPLETE:
                $notificationType = NOTIFICATION_TYPE_BOOKING_COMPLETE;
                break;
            case $status == BOOKING_CANCEL:
                $notificationType = NOTIFICATION_TYPE_BOOKING_CANCEL;
                break;
            case $status == BOOKING_ARRIVED:
                $notificationType = NOTIFICATION_TYPE_BOOKING_ARRIVED;
                break;
            default:
                $notificationType = NOTIFICATION_TYPE_DEFAULT;
                break;
        }

        return $notificationType;
    }
}

if (! function_exists('getOrderStatusMessage')) {

    function getOrderStatusMessage($status, $role_id = null)
    {
        $user = Auth::user();
        $role_id = ! empty($role_id) ? $role_id : $user->role_id;
        $message = "You have received new booking request.";
        if ($status == BOOKING_PENDING) {
            return "You have received new booking request.";
        }
        switch ($status) {
            case $status == BOOKING_PENDING:
                if ($role_id == NORMAL_USER_TYPE) {
                    $message = "You have created a  new booking request.";
                } else {
                    $message = "You have received new booking request.";
                }
                break;
            case $status == BOOKING_ACCEPT:
                if ($role_id == NORMAL_USER_TYPE) {
                    $message = "Your booking request accepted successfully.";
                } else {
                    $message = "You have accepted  booking request successfully.";
                }
                break;
            case $status == BOOKING_REJECT:
                if ($role_id == NORMAL_USER_TYPE) {
                    $message = "Your Booking request rejected by Service Provider.";
                } else {
                    $message = "You have rejected booking request successfully.";
                }
                break;
            case $status == BOOKING_IN_PROGRESS:
                if ($role_id == NORMAL_USER_TYPE) {
                    $message = "Your booking request in progress successfully.";
                } else {
                    $message = "You have  in progress booking request successfully.";
                }
                break;
            case $status == BOOKING_COMPLETE:
                if ($role_id == NORMAL_USER_TYPE) {
                    $message = "Your booking request marked completed successfully.";
                } else {
                    $message = "You have  mark complete booking request successfully.";
                }
                break;
            case $status == BOOKING_CANCEL:
                if ($role_id == NORMAL_USER_TYPE) {
                    $message = "Booking cancelled successfully.";
                } else {
                    $message = "You have  canceled booking request successfully.";
                }
                break;
            case $status == BOOKING_ARRIVED:
                if ($role_id == NORMAL_USER_TYPE) {
                    $message = "Service provider ready to start service.";
                } else {
                    $message = "You have  start booking request successfully.";
                }
                break;
            default:
                $message = "Your booking request status updated.";
                break;
        }

        return $message;
    }
}

if (! function_exists('getNotificationStatus')) {

    function getNotificationStatus($attribute, $status)
    {
        switch ($attribute) {
            case $attribute == 'profile_identity_file_status' && $status == PROFILE_VERIFICATION_APPROVED:
                $notification_type = NOTIFICATION_TYPE_APPROVED_PROFILE_IDENTITY_DOCUMENT;
                break;
            case $attribute == 'profile_identity_file_status' && $status == PROFILE_VERIFICATION_REJECT:
                $notification_type = NOTIFICATION_TYPE_REJECTED_PROFILE_IDENTITY_DOCUMENT;
                break;
            case $attribute == 'profile_identity_video_status' && $status == PROFILE_VERIFICATION_APPROVED:
                $notification_type = NOTIFICATION_TYPE_APPROVED_PROFILE_IDENTITY_VIDEO;
                break;
            case $attribute == 'profile_identity_video_status' && $status == PROFILE_VERIFICATION_REJECT:
                $notification_type = NOTIFICATION_TYPE_REJECTED_PROFILE_IDENTITY_VIDEO;
                break;
            case $attribute == 'bank_statement_file_status' && $status == PROFILE_VERIFICATION_APPROVED:
                $notification_type = NOTIFICATION_TYPE_APPROVED_BANK_STATEMENT;
                break;
            case $attribute == 'bank_statement_file_status' && $status == PROFILE_VERIFICATION_REJECT:
                $notification_type = NOTIFICATION_TYPE_REJECTED_BANK_STATEMENT;
                break;
            case $attribute == 'profile_verified' && $status == PROFILE_VERIFICATION_APPROVED:
                $notification_type = NOTIFICATION_TYPE_APPROVED_PERSONAL_PROFILE;
                break;
            case $attribute == 'profile_verified' && $status == PROFILE_VERIFICATION_REJECT:
                $notification_type = NOTIFICATION_TYPE_REJECTED_PERSONAL_PROFILE;
                break;
            default:
                $notification_type = NOTIFICATION_TYPE_DEFAULT;
                break;
        }

        return $notification_type;
    }
}

if (! function_exists('getNotificationMessage')) {

    function getNotificationMessage($type)
    {
        $user = Auth::user();
        $fullName = @$user->full_name;
        $fullName = @ucwords($fullName);
        switch ($type) {
            case $type == NOTIFICATION_TYPE_ACCOUNT_ACTIVE:
                $notificationTitle = "Your account has been approved by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_ACCOUNT_DEACTIVATED:
                $notificationTitle = "Your account has been deactivated by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_APPROVE_WORK_PROFILE:
                $notificationTitle = "Your work profile has been approved by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_REJECT_WORK_PROFILE:
                $notificationTitle = "Your work profile has been rejected by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_APPROVED_PROFILE_IDENTITY_DOCUMENT:
                $notificationTitle = "Your Profile Identity Document has been approved by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_REJECTED_PROFILE_IDENTITY_DOCUMENT:
                $notificationTitle = "Your Profile Identity Document has been rejected by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_APPROVED_PROFILE_IDENTITY_VIDEO:
                $notificationTitle = "Your Profile Identity Video has been approved by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_REJECTED_PROFILE_IDENTITY_VIDEO:
                $notificationTitle = "Your Profile Identity Video has been rejected by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_APPROVED_BANK_STATEMENT:
                $notificationTitle = "Your Bank Statement has been approved by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_REJECTED_BANK_STATEMENT:
                $notificationTitle = "Your Bank Statement has been rejected by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_APPROVED_PERSONAL_PROFILE:
                $notificationTitle = "Your Personal Profile has been approved by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_REJECTED_PERSONAL_PROFILE:
                $notificationTitle = "Your Personal Profile has been rejected by the administrator successfully.";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_PENDING:
                $notificationTitle = "You have received new booking request.";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_ACCEPT:
                $notificationTitle = "Your booking request accepted successfully.";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_REJECT:
                $notificationTitle = "Booking rejected successfully";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_IN_PROGRESS:
                $notificationTitle = "Your booking request in progress successfully.";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_COMPLETE:
                $notificationTitle = "Your booking request marked completed successfully.";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_CANCEL:
                $notificationTitle = "Booking cancelled successfully.";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_ARRIVED:
                $notificationTitle = "Service provider ready to start service.";
                break;
            case $type == NOTIFICATION_TYPE_SPECIALIST_ADD_BY_BUSINESS:
                $notificationTitle = "The business added you as a new Specialist, please accept or reject the business request.";
                break;
            case $type == NOTIFICATION_TYPE_SPECIALIST_ACCEPT_BUSINESS_REQUEST:
                $notificationTitle = "The specialist accepted your request successfully.";
                break;
            case $type == NOTIFICATION_TYPE_SPECIALIST_REJECTED_BUSINESS_REQUEST:
                $notificationTitle = "The specialist rejected your request successfully.";
                break;
            case $type == NOTIFICATION_TYPE_BUSINESS_REMOVE_SPECIALIST:
                $notificationTitle = "Business removed you from the Specialist list successfully.";
                break;
            case $type == NOTIFICATION_TYPE_SPECIALIST_LEAVE_BUSINESS:
                $notificationTitle = "Service provider left your business successfully.";
                break;
            case $type == NOTIFICATION_TYPE_RATE_SERVICE:
                $notificationTitle = "How was your order from " . $fullName . " ? Tap to rate and review.";
                break;
            case $type == NOTIFICATION_TYPE_RATE_CUSTOMER:
                $notificationTitle = "How was your experience with customer " . $fullName . " ? Tap to rate and review.";
                break;
            case $type == NOTIFICATION_TYPE_CUSTOMER_LIKE_POST:
                $notificationTitle = $fullName . " like your post successfully.";
                break;
            case $type == NOTIFICATION_TYPE_FOLLOW_PROVIDER:
                $notificationTitle = $fullName . " started to follow you successfully.";
                break;
            case $type == NOTIFICATION_TYPE_CUSTOMER_LIKE_COMMENT:
                $notificationTitle = $fullName . " Like your comment.";
                break;
            default:
                $notificationTitle = "New Notification";
                break;
        }
        return $notificationTitle;
    }
}

if (! function_exists('getNotificationTitle')) {

    function getNotificationTitle($type)
    {
        $user = Auth::user();
        if (! empty($user->role_id)) {
            $role = ($user->role_id == NORMAL_USER_TYPE) ? 'Customer' : 'Service Provider';
        } else {
            $role = "Service Provider";
        }
        switch ($type) {
            case $type == NOTIFICATION_TYPE_ACCOUNT_ACTIVE:
                $notificationTitle = "Account Activated";
                break;
            case $type == NOTIFICATION_TYPE_ACCOUNT_DEACTIVATED:
                $notificationTitle = "Account Deactivated";
                break;
            case $type == NOTIFICATION_TYPE_APPROVE_WORK_PROFILE:
                $notificationTitle = "Work Profile Approved";
                break;
            case $type == NOTIFICATION_TYPE_REJECT_WORK_PROFILE:
                $notificationTitle = "Work Profile Rejected";
                break;
            case $type == NOTIFICATION_TYPE_APPROVED_PROFILE_IDENTITY_DOCUMENT:
                $notificationTitle = "Profile Identity Video Approved";
                break;
            case $type == NOTIFICATION_TYPE_REJECTED_PROFILE_IDENTITY_DOCUMENT:
                $notificationTitle = "Profile Identity Video Rejected";
                break;
            case $type == NOTIFICATION_TYPE_APPROVED_PROFILE_IDENTITY_VIDEO:
                $notificationTitle = "Profile Identity Video Approved";
                break;
            case $type == NOTIFICATION_TYPE_REJECTED_PROFILE_IDENTITY_VIDEO:
                $notificationTitle = "Profile Identity Video Rejected";
                break;
            case $type == NOTIFICATION_TYPE_APPROVED_BANK_STATEMENT:
                $notificationTitle = "Bank Statement Approved";
                break;
            case $type == NOTIFICATION_TYPE_REJECTED_BANK_STATEMENT:
                $notificationTitle = "Bank Statement Rejected";
                break;
            case $type == NOTIFICATION_TYPE_APPROVED_PERSONAL_PROFILE:
                $notificationTitle = "Personal Profile Approved";
                break;
            case $type == NOTIFICATION_TYPE_REJECTED_PERSONAL_PROFILE:
                $notificationTitle = "Personal Profile Rejected";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_PENDING:
                $notificationTitle = "New order request";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_ACCEPT:
                $notificationTitle = "Your order accepted";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_CANCEL:
                $notificationTitle = "Your order cancelled";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_REJECT:
                $notificationTitle = "Your order rejected";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_ARRIVED:
                $notificationTitle = "Customer arrived";
                break;
            case $type == NOTIFICATION_TYPE_BOOKING_COMPLETE:
                $notificationTitle = "Your order complete";
                break;
            case $type == NOTIFICATION_TYPE_SPECIALIST_ADD_BY_BUSINESS:
                $notificationTitle = "Business added as you new Specialist";
                break;
            case $type == NOTIFICATION_TYPE_SPECIALIST_ACCEPT_BUSINESS_REQUEST:
                $notificationTitle = "Specialist accept your request";
                break;
            case $type == NOTIFICATION_TYPE_SPECIALIST_REJECTED_BUSINESS_REQUEST:
                $notificationTitle = "Specialist reject your request";
                break;
            case $type == NOTIFICATION_TYPE_BUSINESS_REMOVE_SPECIALIST:
                $notificationTitle = "Business removed you form Specialist";
                break;
            case $type == NOTIFICATION_TYPE_SPECIALIST_LEAVE_BUSINESS:
                $notificationTitle = "Service provider leave your business";
                break;
            case $type == NOTIFICATION_TYPE_RATE_SERVICE:
                $notificationTitle = "Rate your experience";
                break;
            case $type == NOTIFICATION_TYPE_RATE_CUSTOMER:
                $notificationTitle = "Rate your experience";
                break;
            case $type == NOTIFICATION_TYPE_CUSTOMER_LIKE_POST:
                $notificationTitle = "Customer like your post";
                break;
            case $type == NOTIFICATION_TYPE_FOLLOW_PROVIDER:
                $notificationTitle = "Customer started to follow";
                break;
            case $type == NOTIFICATION_TYPE_CUSTOMER_LIKE_COMMENT:
                $notificationTitle = $role . " Like your comment.";
                break;
            case $type == NOTIFICATION_TYPE_CUSTOMER_COMMENT_ON_POST:
                $notificationTitle = @$user->full_name . " commented on your post.";
                break;
            case $type == NOTIFICATION_TYPE_REPORT_POST_DELETED_BY_ADMIN:
                $notificationTitle = " Your post deleted by admin";
                break;
            default:
                $notificationTitle = "New Notification";
                break;
        }
        return $notificationTitle;
    }
}
if (! function_exists('custom_number_format')) {

    function custom_number_format($number)
    {
        return number_format($number, 2, '.', '');
    }
}
if (! function_exists('changeTimeZone')) {

    function changeTimeZone($dateString, $AMPM = false, $timeZoneSource = 'UTC', $onlyTime = false)
    {
        if (empty($timeZoneSource)) {
            $timeZoneSource = date_default_timezone_get();
        }
        $timeZoneTarget = @Auth::user()->time_zone;

        if (empty($timeZoneTarget)) {
            $timeZoneTarget = date_default_timezone_get();
        }

        $dt = new DateTime($dateString, new DateTimeZone($timeZoneSource));
        $dt->setTimezone(new DateTimeZone($timeZoneTarget));
        if (! empty($onlyTime)) {
            return $dt->format("h:i A");
        } elseif (empty($AMPM)) {
            return $dt->format("Y-m-d H:i:s");
        } else {
            return $dt->format("F d h:i A");
        }
    }
}

function uniqueId()
{
    return mt_rand(100000, 999999);
}

function in_array_all($needles, $haystack)
{
    return empty(array_diff($needles, $haystack));
}

function saveUploadedFile($file, $folder = "images")
{
    $fileName = rand() . '_' . time() . '.' . $file->getClientOriginalExtension();
    Storage::disk($folder)->putFileAs('/', $file, $fileName);
    return Storage::disk($folder)->url($fileName);
}

function saveMultipleFiles($request, $model, $folder = "images")
{
    foreach ($request->file('file') as $image) {

        $file = new File();
        $mime = $image->getMimeType();
        $type = FILE_TYPE_OTHER;
        $filePath = '/public/upload/pdf';
        if (strstr($mime, "video/")) {
            $type = FILE_TYPE_VIDEO;
            $filePath = '/public/upload/videos';
        } else if (strstr($mime, "image/")) {
            $type = FILE_TYPE_IMAGE;
            $filePath = '/public/upload/images';
        } else if (strstr($mime, "application/pdf")) {
            $type = FILE_TYPE_PDF;
            $filePath = '/public/upload/pdf';
        } else if (strstr($mime, "audio/")) {
            $type = FILE_TYPE_AUDIO;
            $filePath = '/public/upload/audios';
        }
        $fileName = basename($image->getClientOriginalName());
        $file->original_name = $fileName;
        $fileName = rand() . '_' . time() . '.' . $image->getClientOriginalExtension();
        $file->model_id = $model->id;
        $file->model_type = get_class($model);
        $file->type_id = $type;
        $file->user_id = $model->user_id;
        Storage::disk($folder)->putFileAs('/', $image, $fileName);
        $file->file = Storage::disk($folder)->url($fileName);
        $file->save();
    }
}

if (! function_exists('datatables')) {

    /**
     * Helper to make a new DataTable instance from source.
     * Or return the factory if source is not set.
     *
     * @param mixed $source
     * @return \Yajra\DataTables\DataTableAbstract|\Yajra\DataTables\DataTables
     */
    function datatables($source = null)
    {
        if (is_null($source)) {
            return app('datatables');
        }

        return app('datatables')->make($source);
    }
}

if (! function_exists('getCountryCode')) {

    function getCountryCode($code)
    {
        $countryArray = array(
            'AD' => array(
                'name' => 'ANDORRA',
                'code' => '376'
            ),
            'AE' => array(
                'name' => 'UNITED ARAB EMIRATES',
                'code' => '971'
            ),
            'AF' => array(
                'name' => 'AFGHANISTAN',
                'code' => '93'
            ),
            'AG' => array(
                'name' => 'ANTIGUA AND BARBUDA',
                'code' => '1268'
            ),
            'AI' => array(
                'name' => 'ANGUILLA',
                'code' => '1264'
            ),
            'AL' => array(
                'name' => 'ALBANIA',
                'code' => '355'
            ),
            'AM' => array(
                'name' => 'ARMENIA',
                'code' => '374'
            ),
            'AN' => array(
                'name' => 'NETHERLANDS ANTILLES',
                'code' => '599'
            ),
            'AO' => array(
                'name' => 'ANGOLA',
                'code' => '244'
            ),
            'AQ' => array(
                'name' => 'ANTARCTICA',
                'code' => '672'
            ),
            'AR' => array(
                'name' => 'ARGENTINA',
                'code' => '54'
            ),
            'AS' => array(
                'name' => 'AMERICAN SAMOA',
                'code' => '1684'
            ),
            'AT' => array(
                'name' => 'AUSTRIA',
                'code' => '43'
            ),
            'AU' => array(
                'name' => 'AUSTRALIA',
                'code' => '61'
            ),
            'AW' => array(
                'name' => 'ARUBA',
                'code' => '297'
            ),
            'AZ' => array(
                'name' => 'AZERBAIJAN',
                'code' => '994'
            ),
            'BA' => array(
                'name' => 'BOSNIA AND HERZEGOVINA',
                'code' => '387'
            ),
            'BB' => array(
                'name' => 'BARBADOS',
                'code' => '1246'
            ),
            'BD' => array(
                'name' => 'BANGLADESH',
                'code' => '880'
            ),
            'BE' => array(
                'name' => 'BELGIUM',
                'code' => '32'
            ),
            'BF' => array(
                'name' => 'BURKINA FASO',
                'code' => '226'
            ),
            'BG' => array(
                'name' => 'BULGARIA',
                'code' => '359'
            ),
            'BH' => array(
                'name' => 'BAHRAIN',
                'code' => '973'
            ),
            'BI' => array(
                'name' => 'BURUNDI',
                'code' => '257'
            ),
            'BJ' => array(
                'name' => 'BENIN',
                'code' => '229'
            ),
            'BL' => array(
                'name' => 'SAINT BARTHELEMY',
                'code' => '590'
            ),
            'BM' => array(
                'name' => 'BERMUDA',
                'code' => '1441'
            ),
            'BN' => array(
                'name' => 'BRUNEI DARUSSALAM',
                'code' => '673'
            ),
            'BO' => array(
                'name' => 'BOLIVIA',
                'code' => '591'
            ),
            'BR' => array(
                'name' => 'BRAZIL',
                'code' => '55'
            ),
            'BS' => array(
                'name' => 'BAHAMAS',
                'code' => '1242'
            ),
            'BT' => array(
                'name' => 'BHUTAN',
                'code' => '975'
            ),
            'BW' => array(
                'name' => 'BOTSWANA',
                'code' => '267'
            ),
            'BY' => array(
                'name' => 'BELARUS',
                'code' => '375'
            ),
            'BZ' => array(
                'name' => 'BELIZE',
                'code' => '501'
            ),
           /*  'CA' => array(
                'name' => 'CANADA',
                'code' => '1'
            ), */
            'CC' => array(
                'name' => 'COCOS (KEELING) ISLANDS',
                'code' => '61'
            ),
            'CD' => array(
                'name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
                'code' => '243'
            ),
            'CF' => array(
                'name' => 'CENTRAL AFRICAN REPUBLIC',
                'code' => '236'
            ),
            'CG' => array(
                'name' => 'CONGO',
                'code' => '242'
            ),
            'CH' => array(
                'name' => 'SWITZERLAND',
                'code' => '41'
            ),
            'CI' => array(
                'name' => 'COTE D IVOIRE',
                'code' => '225'
            ),
            'CK' => array(
                'name' => 'COOK ISLANDS',
                'code' => '682'
            ),
            'CL' => array(
                'name' => 'CHILE',
                'code' => '56'
            ),
            'CM' => array(
                'name' => 'CAMEROON',
                'code' => '237'
            ),
            'CN' => array(
                'name' => 'CHINA',
                'code' => '86'
            ),
            'CO' => array(
                'name' => 'COLOMBIA',
                'code' => '57'
            ),
            'CR' => array(
                'name' => 'COSTA RICA',
                'code' => '506'
            ),
            'CU' => array(
                'name' => 'CUBA',
                'code' => '53'
            ),
            'CV' => array(
                'name' => 'CAPE VERDE',
                'code' => '238'
            ),
            'CX' => array(
                'name' => 'CHRISTMAS ISLAND',
                'code' => '61'
            ),
            'CY' => array(
                'name' => 'CYPRUS',
                'code' => '357'
            ),
            'CZ' => array(
                'name' => 'CZECH REPUBLIC',
                'code' => '420'
            ),
            'DE' => array(
                'name' => 'GERMANY',
                'code' => '49'
            ),
            'DJ' => array(
                'name' => 'DJIBOUTI',
                'code' => '253'
            ),
            'DK' => array(
                'name' => 'DENMARK',
                'code' => '45'
            ),
            'DM' => array(
                'name' => 'DOMINICA',
                'code' => '1767'
            ),
            'DO' => array(
                'name' => 'DOMINICAN REPUBLIC',
                'code' => '1809'
            ),
            'DZ' => array(
                'name' => 'ALGERIA',
                'code' => '213'
            ),
            'EC' => array(
                'name' => 'ECUADOR',
                'code' => '593'
            ),
            'EE' => array(
                'name' => 'ESTONIA',
                'code' => '372'
            ),
            'EG' => array(
                'name' => 'EGYPT',
                'code' => '20'
            ),
            'ER' => array(
                'name' => 'ERITREA',
                'code' => '291'
            ),
            'ES' => array(
                'name' => 'SPAIN',
                'code' => '34'
            ),
            'ET' => array(
                'name' => 'ETHIOPIA',
                'code' => '251'
            ),
            'FI' => array(
                'name' => 'FINLAND',
                'code' => '358'
            ),
            'FJ' => array(
                'name' => 'FIJI',
                'code' => '679'
            ),
            'FK' => array(
                'name' => 'FALKLAND ISLANDS (MALVINAS)',
                'code' => '500'
            ),
            'FM' => array(
                'name' => 'MICRONESIA, FEDERATED STATES OF',
                'code' => '691'
            ),
            'FO' => array(
                'name' => 'FAROE ISLANDS',
                'code' => '298'
            ),
            'FR' => array(
                'name' => 'FRANCE',
                'code' => '33'
            ),
            'GA' => array(
                'name' => 'GABON',
                'code' => '241'
            ),
            'GB' => array(
                'name' => 'UNITED KINGDOM',
                'code' => '44'
            ),
            'GD' => array(
                'name' => 'GRENADA',
                'code' => '1473'
            ),
            'GE' => array(
                'name' => 'GEORGIA',
                'code' => '995'
            ),
            'GH' => array(
                'name' => 'GHANA',
                'code' => '233'
            ),
            'GI' => array(
                'name' => 'GIBRALTAR',
                'code' => '350'
            ),
            'GL' => array(
                'name' => 'GREENLAND',
                'code' => '299'
            ),
            'GM' => array(
                'name' => 'GAMBIA',
                'code' => '220'
            ),
            'GN' => array(
                'name' => 'GUINEA',
                'code' => '224'
            ),
            'GQ' => array(
                'name' => 'EQUATORIAL GUINEA',
                'code' => '240'
            ),
            'GR' => array(
                'name' => 'GREECE',
                'code' => '30'
            ),
            'GT' => array(
                'name' => 'GUATEMALA',
                'code' => '502'
            ),
            'GU' => array(
                'name' => 'GUAM',
                'code' => '1671'
            ),
            'GW' => array(
                'name' => 'GUINEA-BISSAU',
                'code' => '245'
            ),
            'GY' => array(
                'name' => 'GUYANA',
                'code' => '592'
            ),
            'HK' => array(
                'name' => 'HONG KONG',
                'code' => '852'
            ),
            'HN' => array(
                'name' => 'HONDURAS',
                'code' => '504'
            ),
            'HR' => array(
                'name' => 'CROATIA',
                'code' => '385'
            ),
            'HT' => array(
                'name' => 'HAITI',
                'code' => '509'
            ),
            'HU' => array(
                'name' => 'HUNGARY',
                'code' => '36'
            ),
            'ID' => array(
                'name' => 'INDONESIA',
                'code' => '62'
            ),
            'IE' => array(
                'name' => 'IRELAND',
                'code' => '353'
            ),
            'IL' => array(
                'name' => 'ISRAEL',
                'code' => '972'
            ),
            'IM' => array(
                'name' => 'ISLE OF MAN',
                'code' => '44'
            ),
            'IN' => array(
                'name' => 'INDIA',
                'code' => '91'
            ),
            'IQ' => array(
                'name' => 'IRAQ',
                'code' => '964'
            ),
            'IR' => array(
                'name' => 'IRAN, ISLAMIC REPUBLIC OF',
                'code' => '98'
            ),
            'IS' => array(
                'name' => 'ICELAND',
                'code' => '354'
            ),
            'IT' => array(
                'name' => 'ITALY',
                'code' => '39'
            ),
            'JM' => array(
                'name' => 'JAMAICA',
                'code' => '1876'
            ),
            'JO' => array(
                'name' => 'JORDAN',
                'code' => '962'
            ),
            'JP' => array(
                'name' => 'JAPAN',
                'code' => '81'
            ),
            'KE' => array(
                'name' => 'KENYA',
                'code' => '254'
            ),
            'KG' => array(
                'name' => 'KYRGYZSTAN',
                'code' => '996'
            ),
            'KH' => array(
                'name' => 'CAMBODIA',
                'code' => '855'
            ),
            'KI' => array(
                'name' => 'KIRIBATI',
                'code' => '686'
            ),
            'KM' => array(
                'name' => 'COMOROS',
                'code' => '269'
            ),
            'KN' => array(
                'name' => 'SAINT KITTS AND NEVIS',
                'code' => '1869'
            ),
            'KP' => array(
                'name' => 'KOREA DEMOCRATIC PEOPLES REPUBLIC OF',
                'code' => '850'
            ),
            'KR' => array(
                'name' => 'KOREA REPUBLIC OF',
                'code' => '82'
            ),
            'KW' => array(
                'name' => 'KUWAIT',
                'code' => '965'
            ),
            'KY' => array(
                'name' => 'CAYMAN ISLANDS',
                'code' => '1345'
            ),
            'KZ' => array(
                'name' => 'KAZAKSTAN',
                'code' => '7'
            ),
            'LA' => array(
                'name' => 'LAO PEOPLES DEMOCRATIC REPUBLIC',
                'code' => '856'
            ),
            'LB' => array(
                'name' => 'LEBANON',
                'code' => '961'
            ),
            'LC' => array(
                'name' => 'SAINT LUCIA',
                'code' => '1758'
            ),
            'LI' => array(
                'name' => 'LIECHTENSTEIN',
                'code' => '423'
            ),
            'LK' => array(
                'name' => 'SRI LANKA',
                'code' => '94'
            ),
            'LR' => array(
                'name' => 'LIBERIA',
                'code' => '231'
            ),
            'LS' => array(
                'name' => 'LESOTHO',
                'code' => '266'
            ),
            'LT' => array(
                'name' => 'LITHUANIA',
                'code' => '370'
            ),
            'LU' => array(
                'name' => 'LUXEMBOURG',
                'code' => '352'
            ),
            'LV' => array(
                'name' => 'LATVIA',
                'code' => '371'
            ),
            'LY' => array(
                'name' => 'LIBYAN ARAB JAMAHIRIYA',
                'code' => '218'
            ),
            'MA' => array(
                'name' => 'MOROCCO',
                'code' => '212'
            ),
            'MC' => array(
                'name' => 'MONACO',
                'code' => '377'
            ),
            'MD' => array(
                'name' => 'MOLDOVA, REPUBLIC OF',
                'code' => '373'
            ),
            'ME' => array(
                'name' => 'MONTENEGRO',
                'code' => '382'
            ),
            'MF' => array(
                'name' => 'SAINT MARTIN',
                'code' => '1599'
            ),
            'MG' => array(
                'name' => 'MADAGASCAR',
                'code' => '261'
            ),
            'MH' => array(
                'name' => 'MARSHALL ISLANDS',
                'code' => '692'
            ),
            'MK' => array(
                'name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
                'code' => '389'
            ),
            'ML' => array(
                'name' => 'MALI',
                'code' => '223'
            ),
            'MM' => array(
                'name' => 'MYANMAR',
                'code' => '95'
            ),
            'MN' => array(
                'name' => 'MONGOLIA',
                'code' => '976'
            ),
            'MO' => array(
                'name' => 'MACAU',
                'code' => '853'
            ),
            'MP' => array(
                'name' => 'NORTHERN MARIANA ISLANDS',
                'code' => '1670'
            ),
            'MR' => array(
                'name' => 'MAURITANIA',
                'code' => '222'
            ),
            'MS' => array(
                'name' => 'MONTSERRAT',
                'code' => '1664'
            ),
            'MT' => array(
                'name' => 'MALTA',
                'code' => '356'
            ),
            'MU' => array(
                'name' => 'MAURITIUS',
                'code' => '230'
            ),
            'MV' => array(
                'name' => 'MALDIVES',
                'code' => '960'
            ),
            'MW' => array(
                'name' => 'MALAWI',
                'code' => '265'
            ),
            'MX' => array(
                'name' => 'MEXICO',
                'code' => '52'
            ),
            'MY' => array(
                'name' => 'MALAYSIA',
                'code' => '60'
            ),
            'MZ' => array(
                'name' => 'MOZAMBIQUE',
                'code' => '258'
            ),
            'NA' => array(
                'name' => 'NAMIBIA',
                'code' => '264'
            ),
            'NC' => array(
                'name' => 'NEW CALEDONIA',
                'code' => '687'
            ),
            'NE' => array(
                'name' => 'NIGER',
                'code' => '227'
            ),
            'NG' => array(
                'name' => 'NIGERIA',
                'code' => '234'
            ),
            'NI' => array(
                'name' => 'NICARAGUA',
                'code' => '505'
            ),
            'NL' => array(
                'name' => 'NETHERLANDS',
                'code' => '31'
            ),
            'NO' => array(
                'name' => 'NORWAY',
                'code' => '47'
            ),
            'NP' => array(
                'name' => 'NEPAL',
                'code' => '977'
            ),
            'NR' => array(
                'name' => 'NAURU',
                'code' => '674'
            ),
            'NU' => array(
                'name' => 'NIUE',
                'code' => '683'
            ),
            'NZ' => array(
                'name' => 'NEW ZEALAND',
                'code' => '64'
            ),
            'OM' => array(
                'name' => 'OMAN',
                'code' => '968'
            ),
            'PA' => array(
                'name' => 'PANAMA',
                'code' => '507'
            ),
            'PE' => array(
                'name' => 'PERU',
                'code' => '51'
            ),
            'PF' => array(
                'name' => 'FRENCH POLYNESIA',
                'code' => '689'
            ),
            'PG' => array(
                'name' => 'PAPUA NEW GUINEA',
                'code' => '675'
            ),
            'PH' => array(
                'name' => 'PHILIPPINES',
                'code' => '63'
            ),
            'PK' => array(
                'name' => 'PAKISTAN',
                'code' => '92'
            ),
            'PL' => array(
                'name' => 'POLAND',
                'code' => '48'
            ),
            'PM' => array(
                'name' => 'SAINT PIERRE AND MIQUELON',
                'code' => '508'
            ),
            'PN' => array(
                'name' => 'PITCAIRN',
                'code' => '870'
            ),
            /* 'PR' => array(
                'name' => 'PUERTO RICO',
                'code' => '1'
            ), */
            'PT' => array(
                'name' => 'PORTUGAL',
                'code' => '351'
            ),
            'PW' => array(
                'name' => 'PALAU',
                'code' => '680'
            ),
            'PY' => array(
                'name' => 'PARAGUAY',
                'code' => '595'
            ),
            'QA' => array(
                'name' => 'QATAR',
                'code' => '974'
            ),
            'RO' => array(
                'name' => 'ROMANIA',
                'code' => '40'
            ),
            'RS' => array(
                'name' => 'SERBIA',
                'code' => '381'
            ),
            'RU' => array(
                'name' => 'RUSSIAN FEDERATION',
                'code' => '7'
            ),
            'RW' => array(
                'name' => 'RWANDA',
                'code' => '250'
            ),
            'SA' => array(
                'name' => 'SAUDI ARABIA',
                'code' => '966'
            ),
            'SB' => array(
                'name' => 'SOLOMON ISLANDS',
                'code' => '677'
            ),
            'SC' => array(
                'name' => 'SEYCHELLES',
                'code' => '248'
            ),
            'SD' => array(
                'name' => 'SUDAN',
                'code' => '249'
            ),
            'SE' => array(
                'name' => 'SWEDEN',
                'code' => '46'
            ),
            'SG' => array(
                'name' => 'SINGAPORE',
                'code' => '65'
            ),
            'SH' => array(
                'name' => 'SAINT HELENA',
                'code' => '290'
            ),
            'SI' => array(
                'name' => 'SLOVENIA',
                'code' => '386'
            ),
            'SK' => array(
                'name' => 'SLOVAKIA',
                'code' => '421'
            ),
            'SL' => array(
                'name' => 'SIERRA LEONE',
                'code' => '232'
            ),
            'SM' => array(
                'name' => 'SAN MARINO',
                'code' => '378'
            ),
            'SN' => array(
                'name' => 'SENEGAL',
                'code' => '221'
            ),
            'SO' => array(
                'name' => 'SOMALIA',
                'code' => '252'
            ),
            'SR' => array(
                'name' => 'SURINAME',
                'code' => '597'
            ),
            'ST' => array(
                'name' => 'SAO TOME AND PRINCIPE',
                'code' => '239'
            ),
            'SV' => array(
                'name' => 'EL SALVADOR',
                'code' => '503'
            ),
            'SY' => array(
                'name' => 'SYRIAN ARAB REPUBLIC',
                'code' => '963'
            ),
            'SZ' => array(
                'name' => 'SWAZILAND',
                'code' => '268'
            ),
            'TC' => array(
                'name' => 'TURKS AND CAICOS ISLANDS',
                'code' => '1649'
            ),
            'TD' => array(
                'name' => 'CHAD',
                'code' => '235'
            ),
            'TG' => array(
                'name' => 'TOGO',
                'code' => '228'
            ),
            'TH' => array(
                'name' => 'THAILAND',
                'code' => '66'
            ),
            'TJ' => array(
                'name' => 'TAJIKISTAN',
                'code' => '992'
            ),
            'TK' => array(
                'name' => 'TOKELAU',
                'code' => '690'
            ),
            'TL' => array(
                'name' => 'TIMOR-LESTE',
                'code' => '670'
            ),
            'TM' => array(
                'name' => 'TURKMENISTAN',
                'code' => '993'
            ),
            'TN' => array(
                'name' => 'TUNISIA',
                'code' => '216'
            ),
            'TO' => array(
                'name' => 'TONGA',
                'code' => '676'
            ),
            'TR' => array(
                'name' => 'TURKEY',
                'code' => '90'
            ),
            'TT' => array(
                'name' => 'TRINIDAD AND TOBAGO',
                'code' => '1868'
            ),
            'TV' => array(
                'name' => 'TUVALU',
                'code' => '688'
            ),
            'TW' => array(
                'name' => 'TAIWAN, PROVINCE OF CHINA',
                'code' => '886'
            ),
            'TZ' => array(
                'name' => 'TANZANIA, UNITED REPUBLIC OF',
                'code' => '255'
            ),
            'UA' => array(
                'name' => 'UKRAINE',
                'code' => '380'
            ),
            'UG' => array(
                'name' => 'UGANDA',
                'code' => '256'
            ),
            'US' => array(
                'name' => 'UNITED STATES',
                'code' => '1'
            ),
            'UY' => array(
                'name' => 'URUGUAY',
                'code' => '598'
            ),
            'UZ' => array(
                'name' => 'UZBEKISTAN',
                'code' => '998'
            ),
            'VA' => array(
                'name' => 'HOLY SEE (VATICAN CITY STATE)',
                'code' => '39'
            ),
            'VC' => array(
                'name' => 'SAINT VINCENT AND THE GRENADINES',
                'code' => '1784'
            ),
            'VE' => array(
                'name' => 'VENEZUELA',
                'code' => '58'
            ),
            'VG' => array(
                'name' => 'VIRGIN ISLANDS, BRITISH',
                'code' => '1284'
            ),
            'VI' => array(
                'name' => 'VIRGIN ISLANDS, U.S.',
                'code' => '1340'
            ),
            'VN' => array(
                'name' => 'VIET NAM',
                'code' => '84'
            ),
            'VU' => array(
                'name' => 'VANUATU',
                'code' => '678'
            ),
            'WF' => array(
                'name' => 'WALLIS AND FUTUNA',
                'code' => '681'
            ),
            'WS' => array(
                'name' => 'SAMOA',
                'code' => '685'
            ),
            'XK' => array(
                'name' => 'KOSOVO',
                'code' => '381'
            ),
            'YE' => array(
                'name' => 'YEMEN',
                'code' => '967'
            ),
            'YT' => array(
                'name' => 'MAYOTTE',
                'code' => '262'
            ),
            'ZA' => array(
                'name' => 'SOUTH AFRICA',
                'code' => '27'
            ),
            'ZM' => array(
                'name' => 'ZAMBIA',
                'code' => '260'
            ),
            'ZW' => array(
                'name' => 'ZIMBABWE',
                'code' => '263'
            )
        );
        foreach ($countryArray as $key => $countryCode) {
            if ($countryCode['code'] == $code) {
                return $key;
            }
        }
        return 'AUTO';
    }
}

if (! function_exists('totalInactiveUsers')) {

    function totalInactiveUsers()
    {
        return User::where([
            'active_status' => INACTIVE_STATUS
        ])->count();
    }
}
if (! function_exists('totalUsers')) {

    function totalUsers()
    {
        return User::where([
            'role_id' => NORMAL_USER_TYPE
        ])->count();
    }
}

if (! function_exists('totalServiceProviders')) {

    function totalServiceProviders()
    {
        return User::where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE
        ])->count();
    }
}

if (! function_exists('totalServiceCategories')) {

    function totalServiceCategories()
    {
        return ServiceCategory::count();
    }
}

if (! function_exists('totalPosts')) {

    function totalPosts()
    {
        return Post::count();
    }
}

if (! function_exists('isBase64Encoded')) {

    function isBase64Encoded(string $s): bool
    {
        if ((bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s) === false) {
            return false;
        }
        $decoded = base64_decode($s, true);
        if ($decoded === false) {
            return false;
        }
        $encoding = mb_detect_encoding($decoded);
        if (! in_array($encoding, [
            'UTF-8',
            'ASCII'
        ], true)) {
            return false;
        }
        return $decoded !== false && base64_encode($decoded) === $s;
    }
}

if (! function_exists('setpageTitle')) {

    function setpageTitle()
    {
        $title = '';
        $title = ucwords(last(str_replace('-', ' ', request()->segments())));
        if (is_numeric(base64_decode($title)) && isBase64Encoded($title)) {
            $title = base64_decode($title);
        }
        if (is_numeric($title)) {
            $titleKey = count(request()->segments()) - 1;
            $newTitle = request()->segments()[$titleKey];
            if (is_numeric(base64_decode($newTitle)) && isBase64Encoded($newTitle)) {
                $titleKey = count(request()->segments()) - 1;
            }
            if (! empty(Request::segment($titleKey))) {
                $title = ucwords(str_replace('-', ' ', Request::segment($titleKey)));
            }
        }
        return $title;
    }
}
if (! function_exists('UploadImage')) {

    function folder_exist($folder)
    {
        // Get canonicalized absolute pathname
        $path = realpath($folder);

        // If it exist, check if it's a directory
        return ($path !== false and is_dir($path)) ? $path : false;
    }
}
if (! function_exists('UploadImage')) {

    // UPLOAD IMAGES FOR USER
    function UploadImage($file, $destinationPath)
    {
        try {
            if (empty(folder_exist($destinationPath))) {
                $oldmask = umask(0);
                mkdir($destinationPath, 0777);
                umask($oldmask);
            }
            $imgName = $name = rand(99, 999999) . time() . "." . strtolower($file->getClientOriginalExtension());
            $ext = strtolower($file->getClientOriginalExtension());
            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
                $type = TYPE_IMAGE;
            } else {
                $type = TYPE_VIDEO;
            }
            $file->move($destinationPath, $imgName);
            $path = $destinationPath . '/' . $imgName;
            $response['status'] = true;
            $response['path'] = $path;
            $response['type'] = $type;
            return $response;
        } catch (\Execption $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage()->withInput();
            return $response;
        }
    }
}

if (! function_exists('UploadPostVideoImage')) {

    // UPLOAD IMAGES FOR USER
    function UploadPostVideoImage($file, $destinationPath = '')
    {
        try {

            $imgName = $name = rand(99, 999999) . time() . "." . strtolower($file->getClientOriginalExtension());
            $thumbnailpath = '';
            $ext = strtolower($file->getClientOriginalExtension());
            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
                $type = TYPE_IMAGE;
                $file->move($destinationPath, $imgName);
                $path = $destinationPath . '/' . $imgName;
            } else {
                $type = TYPE_VIDEO;

                $filename = rand(99, 999999) . time();
                $file_name = $filename . ".mp4";
                $thumb_name = $filename . ".png";
                $inputFile = public_path() . '/images/posts/' . $file_name;
                $thumbpath = public_path() . "/images/posts/" . $filename . ".png";

                $FFmpeg = new \FFMpeg();
                $upload_shell_video = "ffmpeg -i $file -vcodec h264 -acodec mp2 $inputFile";
                echo shell_exec($upload_shell_video);

                $shell = "ffmpeg -i " . $inputFile . " -frames:v 1 -q:v 2 -vf 'scale=480:480:force_original_aspect_ratio=increase,crop=480:480' " . $thumbpath;
                echo shell_exec($shell);
                $path = $destinationPath . '/' . $file_name;
                $thumbnailpath = $destinationPath . '/' . $thumb_name;
            }

            $response['status'] = true;
            $response['path'] = $path;
            $response['thumbpath'] = $thumbnailpath;
            $response['type'] = $type;
            return $response;
        } catch (\Execption $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage()->withInput();
            return $response;
        }
    }
}

if (! function_exists('getFileType')) {

    function getFileType($extension)
    {
        $extension = strtolower($extension);
        if ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
            return 'I';
        } else {
            return 'V';
        }
    }
}

if (! function_exists('pp')) {

    function pp($data)
    {
        echo "<pre>";
        print_r($data);
        echo "<pre>";
        exit(0);
    }
}

if (! function_exists('generateRandomOrder')) {

    function generateRandomOrder()
    {
        $number = rand(1111111, 9999999);
        $timestamp = time();
        $randomNumber = $number . $timestamp;
        return $randomNumber;
    }
}

if (! function_exists('timeAgoDifference')) {

    function timeAgoDifference($date1, $date2)
    {
        $to_time = strtotime($date2);
        $from_time = strtotime($date1);
        $final_minutes = round(abs($to_time - $from_time) / 60);
        return $final_minutes;
    }
}

if (! function_exists('emailSend')) {

    function emailSend($postData)
    {
        try {
            $postData['from_email'] = env('MAIL_FROM_ADDRESS', 'smtp@itechnolabs.tech');
            $email = Mail::send($postData['layout'], $postData, function ($message) use ($postData) {

                $message->to($postData['email'])->subject($postData['subject']);
            });

            $response['status'] = true;
            $response['message'] = "Mail sent successfully.";
            return $response;
        } catch (\Execption $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return $response;
        }
    }
}

if (! function_exists('customTime')) {

    function customTime($date)
    {
        $customDate = $date;
        $currentDate = date('Y-m-d');
        if (date('Y-m-d', strtotime($date)) == $currentDate) {
            $customDate = date('h:i a', strtotime($date));
        } else {
            $customDate = date('d M y');
        }

        return $customDate;
    }
}

if (! function_exists('getUserOnline')) {

    function getUserOnline($userid = 0)
    {
        $online_stauts = 0;
        $User = User::where('id', $userid)->first();
        if ($User) {
            if ($User->last_active) {
                $current_time = date('Y-m-d H:i:s');
                $to_time = strtotime($current_time);
                $from_time = strtotime($User->last_active);
                $minute = round(abs($to_time - $from_time) / 60);

                if ($minute < 3) {
                    $online_stauts = 1;
                }
            }
        }
        return $online_stauts;
    }
}

if (! function_exists('returnNotFoundResponse')) {

    function returnNotFoundResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 404,
            'status' => 'not found',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 404);
    }
}

if (! function_exists('returnValidationErrorResponse')) {

    function returnValidationErrorResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 422,
            'status' => 'vaidation error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 422);
    }
}

if (! function_exists('returnSuccessResponse')) {

    function returnSuccessResponse($message = '', $data = array(), $is_array = false)
    {
        $is_array = ! empty($is_array) ? [] : (object) [];
        $returnArr = [
            'statusCode' => 200,
            'status' => 'success',
            'message' => $message,
            'data' => ($data) ? ($data) : $is_array
        ];
        return response()->json($returnArr, 200);
    }
}

if (! function_exists('returnErrorResponse')) {

    function returnErrorResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 500,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 500);
    }
}

if (! function_exists('returnCustomErrorResponse')) {

    function returnCustomErrorResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 404,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 200);
    }
}

if (! function_exists('returnError301Response')) {

    function returnError301Response($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 301,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 301);
    }
}

if (! function_exists('notAuthorizedResponse')) {

    function notAuthorizedResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 401,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr);
    }
}

if (! function_exists('forbiddenResponse')) {

    function forbiddenResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 403,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 403);
    }
}




    