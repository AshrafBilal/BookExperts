<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $sub_service_category_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property SubServiceCategory $subServiceCategory
 * @property User $user
 */
class ProviderOtherCategory extends Model
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
        'sub_service_category_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subServiceCategory()
    {
        return $this->belongsTo(SubServiceCategory::class, 'sub_service_category_id', 'id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    public function jsonResponse(){
        
        $json['id'] = $this->id;
        $json['user_id'] = $this->user_id;
        $json['sub_service_category_id'] = $this->sub_service_category_id;
        $json['sub_service_category_name'] = $this->subServiceCategory->name;
        return $json;
            
    }
}
