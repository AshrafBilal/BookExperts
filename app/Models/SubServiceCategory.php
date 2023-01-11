<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property integer $id
 * @property integer $service_category_id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property ServiceCategory $serviceCategory
 * @property Service[] $services
 */
class SubServiceCategory extends Model
{
    use SoftDeletes;

    const STATUS_PENDING = 0;

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
        'service_category_id',
        'name',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
        'file_path'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
        'deleted_at' => 'datetime:Y-m-d h:i:s'
    ];

    public function delete()
    {
        $this->unlinkFiles();
        return parent::delete();
    }

    public function unlinkFiles()
    {
        $file = basename($this->file_path);
        if (! empty($file)) {
            @Storage::disk('images')->delete($file);
        }
        return true;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceCategory()
    {
        return $this->belongsTo('App\Models\ServiceCategory');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services()
    {
        return $this->hasMany('App\Models\Service', 'service_id');
    }

    public function getAllCategories()
    {
        return self::latest()->get();
    }

    public function jsonResponse()
    {
        $json['id'] = $this->id;
        $json['main_category_name'] = @$this->serviceCategory->name;
        $json['name'] = $this->name;
        $json['file_path'] = $this->getFilePath();
        $json['service_category_id'] = $this->service_category_id;

        return $json;
    }

    public function getFilePath()
    {
        $exists = Storage::disk('images')->exists(basename($this->file_path));
        if ($exists) {
            return $this->file_path;
        } else {
            return asset('admin/images/default_menu.jpeg');
        }
    }
}
