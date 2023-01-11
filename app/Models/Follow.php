<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $follow_by
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property User $user
 */
class Follow extends Model
{

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'follow_by',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function jsonResponse()
    {
        $json['id'] = $this->id;
        $json['user_id'] = $this->user_id;
        $json['follow_by'] = $this->follow_by;
        $json['status'] = $this->status;
        $json['user_id'] = $this->user_id;
        return $json;
    }

    public static function getFollowingList($request)
    {
        $page_no = ! empty($request->query('page')) ? $request->query('page') : 1;
        $user = Auth::user();
        $customJson = [];
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 20;
        $follows = Follow::where('follow_by', $user->id)->pluck('user_id')->toArray();

        $query = User::whereIn('id', $follows)->where([
            'role_id' => SERVICE_PROVIDER_USER_TYPE,
            'profile_verified' => ACTIVE_STATUS
        ]);
        $query = $query->paginate($page_limit);
        $items = $query->items();
        $finalJson = [];
        foreach ($items as $key => $item) {
            $workProfile = $item->WorkProfile;
            $customJson['user_id'] = $item->id;
            $customJson['full_name'] = $item->getFullName();
            $customJson['profile_file'] = $item->profile_file;
            $customJson['business_name'] = $workProfile->business_name;
            $customJson['main_category_name'] = @$workProfile->serviceCategory->name;
            $customJson['is_follow'] = $item->isFollow();
            $customJson['account_type'] = $workProfile->account_type;
            $customJson['available_for_live_booking'] = $item->available_for_live_booking;

            $finalJson[] = $customJson;
        }
        $data['total_follow'] = $query->total();
        $data['next_page'] = ($query->nextPageUrl()) ? true : false;
        $data['providers'] = $finalJson;
        $data['current_page'] = $query->currentPage();
        $data['per_page'] = $query->perPage();
        $data['links'] = $query->linkCollection();
        $data['total'] = $query->total();
        $data['total_pages'] = (int) ceil($query->total() / $query->perPage());
        return $data;
    }

    public static function providerFollowingList($request)
    {
        $page_no = ! empty($request->query('page')) ? $request->query('page') : 1;
        $user = Auth::user();
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 20;
        $follows = Follow::where('user_id', $user->id)->pluck('follow_by')->toArray();

        $query = User::whereIn('id', $follows)->where([
            'role_id' => NORMAL_USER_TYPE
        ]);
        $query = $query->paginate($page_limit);
        $items = $query->items();
        $finalJson = [];
        foreach ($items as $key => $item) {
            $finalJson[] = $item->jsonResponse();
        }
        $data['total_follow'] = $query->total();
        $data['next_page'] = ($query->nextPageUrl()) ? true : false;
        $data['providers'] = $finalJson;
        $data['current_page'] = $query->currentPage();
        $data['per_page'] = $query->perPage();
        $data['links'] = $query->linkCollection();
        $data['total'] = $query->total();
        $data['total_pages'] = (int) ceil($query->total() / $query->perPage());
        return $data;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function followBy()
    {
        return $this->belongsTo('App\Models\User', 'follow_by');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
