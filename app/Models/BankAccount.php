<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $account_holder
 * @property integer $account_number
 * @property string $ifsc_code
 * @property string $short_code
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class BankAccount extends Model
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
        'account_holder',
        'account_number',
        'ifsc_code',
        'sort_code',
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

    public function jsonResponse()
    {
        $json['id'] = $this->id;
        $json['account_holder'] = $this->account_holder;
        $json['account_number'] = $this->account_number;
        $json['ifsc_code'] = $this->ifsc_code;
        $json['sort_code'] = $this->sort_code;
        $json['created_at'] = $this->created_at->toDateTimeString();
        $json['updated_at'] = $this->updated_at->toDateTimeString();
        $json['user_id'] = $this->user_id;
        return $json;
    }
}
