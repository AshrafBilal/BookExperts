<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property integer $id
 * @property integer $service_id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property int $time
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property SubServiceCategory $subServiceCategory
 * @property Rating[] $ratings
 */
class Service extends Model
{

    use SoftDeletes;

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
        'service_id',
        'name',
        'description',
        'price',
        'time',
        'created_at',
        'updated_at',
        'deleted_at',
        'sub_service_category_id',
        'service_provider_id',
        'service_image',
        'home_service_price'
    ];

    protected $appends = [
        'price'
    ];

    public function getPriceAttribute()
    {
        $loginUser = Auth::user();

        if (! empty($loginUser) && $loginUser->role_id == SERVICE_PROVIDER_USER_TYPE) {
            if ($loginUser->available_for_home_booking == ACTIVE_STATUS) {
                if (! empty($this->attributes['home_service_price'])) {
                    return $this->attributes['home_service_price'];
                }
                return $this->attributes['price'];
            }
        }

        return $this->attributes['price'];
    }

    public function getServicePrice($visit_type = null)
    {
        if ($visit_type == BOOKING_TYPE_VISIT_HOMES) {
            if (empty($this->home_service_price)) {
                self::where([
                    'id' => $this->id
                ])->update([
                    'home_service_price' => $this->price
                ]);
            }
            return ! empty($this->home_service_price) ? $this->home_service_price : $this->price;
        } else {
            return $this->price;
        }
    }

    public function delete()
    {
        $this->unlinkFile();
        return parent::delete();
    }

    public function unlinkFile()
    {
        $service_image = basename($this->service_image);
        if (! empty($service_image)) {
            Storage::disk('images')->delete($service_image);
        }
        return true;
    }

    public function jsonResponse($visit_type = null)
    {
        $user = Auth::user();
        if (! empty($user) && $user->role_id == SERVICE_PROVIDER_USER_TYPE) {
            $visit_type = $user->available_for_home_booking;
        }
        $json = [];
        $json['id'] = $this->id;
        $json['name'] = $this->name;
        $json['service_image'] = $this->service_image;
        $json['description'] = $this->description;

        if ($visit_type == BOOKING_TYPE_VISIT_HOMES) {
            if (! empty($this->home_service_price)) {
                $json['price'] = custom_number_format($this->home_service_price);
            } else {
                $json['price'] = custom_number_format($this->price);
            }
        } elseif ($visit_type == BOOKING_TYPE_ONLY_AT_WORK_PLACE) {
            $json['price'] = custom_number_format($this->price);
        } else {
            $json['price'] = custom_number_format($this->price);
        }
        $json['home_service_price'] = custom_number_format($this->home_service_price);
        $json['time'] = $this->time;
        $json['time_format'] = $this->getTimeFormat();
        $json['sub_service_category_id'] = $this->sub_service_category_id;
        $json['sub_service_category_name'] = @$this->subServiceCategory->name;
        $json['service_category_id'] = $this->service_category_id;
        $json['service_category_name'] = @$this->mainService->name;
        $json['service_provider_id'] = $this->service_provider_id;
        $json['service_provider_name'] = @$this->serviceProvider->full_name;
        $json['service_provider_profile_image'] = asset('admin/images/default_image.png');
        if (! empty($this->serviceProvider->profile_file)) {
            $json['service_provider_profile_image'] = @$this->serviceProvider->profile_file;
        }
        $json['service_visit'] = $this->service_visit;
        $json['created_at'] = $this->created_at;
        $json['updated_at'] = $this->updated_at;

        return $json;
    }

    function getTimeFormat($format = '%02d hrs %02d mins')
    {
        $time = $this->time;
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        if (! empty($hours)) {
            return sprintf($format, $hours, $minutes);
        } else {
            $format = "%02d mins";
            return sprintf($format, $minutes);
        }
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subServiceCategory()
    {
        return $this->belongsTo('App\Models\SubServiceCategory', 'sub_service_category_id');
    }

    public function mainService()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany('App\Models\Rating');
    }

    public function getAllServices()
    {
        return self::latest()->get();
    }

    public function getServiceType()
    {
        $list = [
            BOOKING_TYPE_VISIT_HOMES => 'Visit Homes ',
            BOOKING_TYPE_ONLY_AT_WORK_PLACE => 'Only at work place'
        ];
        return ! empty($list[$this->service_visit]) ? $list[$this->service_visit] : null;
    }
}
