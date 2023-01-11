<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property integer $specialist_id
 * @property integer $user_id
 * @property string $title
 * @property int $state_id
 * @property int $type_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property User $user
 */
class Specialist extends Model
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
        'specialist_id',
        'user_id',
        'title',
        'state_id',
        'type_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

   
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User','specialist_id','id');
    }

    public function jsonResponse(){

        $json['id'] = $this->id;
        $json['work_profile_id'] = $this->work_profile_id;
        $json['specialist_id'] = $this->specialist_id;
        $json['created_at'] = $this->created_at->toDateTimeString();
        $json['updated_at'] = $this->updated_at->toDateTimeString();
        $json['user_id'] = $this->user_id;

        $user = $this->user;

        if(!empty($user))
            $json['user'] = $user->jsonResponse();
        
        return $json;

    }
}
