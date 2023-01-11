<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $device_token
 * @property string $device_type
 * @property string $unique_device_id
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class UserSession extends Model
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
        'device_token',
        'device_type',
        'unique_device_id',
        'created_at',
        'updated_at'
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function addSession($request)
    {
        if (empty($request->user_id)) {
            return false;
        }
        $UserSession = UserSession::where([
            'user_id' => $request->user_id
        ])->first();
        if (! $UserSession) {
            $UserSession = new UserSession();
        }
        $UserSession->user_id = $request->user_id;
        $UserSession->device_token = $request->device_token;
        $UserSession->unique_device_id = $request->unique_device_id;
        $UserSession->device_type = $request->device_type;
        return $UserSession->save();
    }

    public static function removeSession($request)
    {
        return UserSession::where([
            'user_id' => $request->user_id
        ])->delete();
    }
}
