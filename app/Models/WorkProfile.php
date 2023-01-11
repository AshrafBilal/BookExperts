<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\File;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $business_name
 * @property int $service_category_id
 * @property int $sub_service_category_id
 * @property string $tagline_for_business
 * @property string $location
 * @property string $latitude
 * @property string $longitude
 * @property string $about_business
 * @property int $account_type
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 */
class WorkProfile extends Model
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
        'business_name',
        'service_category_id',
        'sub_service_category_id',
        'tagline_for_business',
        'location',
        'latitude',
        'longitude',
        'about_business',
        'account_type',
        'created_at',
        'updated_at',
        'deleted_at',
        'company_number'
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function otherCategories()
    {
        return $this->hasMany(ProviderOtherCategory::class, 'id', 'user_id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getSubServices()
    {
        $ids = json_decode($this->sub_service_category_id, true);
        if (is_array($ids)) {
            return SubServiceCategory::whereIn('id', $ids)->get();
        }
        return [];
    }

    public function getServiceCategoryName($single = false)
    {
        $serviceCategory = @$this->serviceCategory;
        if (! empty($serviceCategory) && ! empty($serviceCategory->category_type == CATEGORY_TYPE_OTHER)) {
            $ids = json_decode($this->sub_service_category_id, true);
            if (is_array($ids)) {
                if (! empty($single)) {

                    $otherCat = SubServiceCategory::select('name')->whereIn('id', $ids)->first();
                    return @$otherCat->name;
                } else {
                    $otherCat = SubServiceCategory::whereIn('id', $ids)->pluck('name')->toArray();
                }
                $names = @implode(", ", $otherCat);
                return @$names;
            }
        }
        return @$serviceCategory->name;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function getWorkLocation()
    {
        $address = Address::where([
            'user_id' => $this->user_id
        ])->first();
        if (! empty($address)) {
            return "<strong>Address:</strong> " . $address->address1 . "<br><strong>Street:</strong> " . $address->street . "<br>" . "<strong>City:</strong> " . $address->city . "<br><strong>Postal Code:</strong> " . $address->zip_code;
        }
        return false;
    }

    public function getFiles()
    {
        return File::where([
            'model_id' => $this->id,
            'model_type' => get_class(new self())
        ])->get();
    }

    public function getSingleFile()
    {
        $file = File::where([
            'model_id' => $this->id,
            'model_type' => get_class(new self())
        ])->first();
        if (! empty($file->file)) {
            return $file->file;
        }
        return null;
    }

    public function getWorkProfileImages()
    {
        return File::select('id', 'file')->where([
            'model_id' => $this->id,
            'model_type' => get_class(new self())
        ])->get();
    }

    public function specialists()
    {
        return $this->hasMany('App\Models\Specialist', 'work_profile_id', 'id')->where('state_id', ACTIVE_STATUS);
    }

    public function getWorkProfileSpecialists()
    {
        $result = Specialist::where([
            'work_profile_id' => $this->id
        ])->get();

        if (! empty($result->count())) {
            $result;
        } else {
            return [];
        }
    }

    public function jsonResponse()
    {
        $json = [];

        $json['id'] = $this->id;
        $json['service_category_id'] = $this->service_category_id;
        $json['sub_service_category_id'] = json_decode($this->sub_service_category_id);
        $json['business_name'] = $this->business_name;
        $json['tagline_for_business'] = $this->tagline_for_business;
        $json['about_business'] = $this->about_business;
        $json['account_type'] = $this->account_type;
        $json['company_number'] = $this->company_number;
        $json['location'] = $this->location;
        $json['latitude'] = $this->latitude;
        $json['longitude'] = $this->longitude;
        $json['created_at'] = $this->created_at->toDateTimeString();
        $json['updated_at'] = $this->updated_at->toDateTimeString();
        $json['user_id'] = $this->user_id;

        $createdBy = $this->createdBy;

        if (! empty($createdBy))
            $json['created_by'] = $createdBy->jsonResponse();

        $files = $this->getFiles();

        $response = [];

        foreach ($files as $key => $file) {
            $response[] = $file->jsonResponse();
        }

        $json['files'] = $response;

        $specialists = $this->specialists;

        $response = [];

        foreach ($specialists as $key => $specialist) {
            $response[] = $specialist->jsonResponse();
        }

        $json['specialist'] = $response;

        return $json;
    }

}
