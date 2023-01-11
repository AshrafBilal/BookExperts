<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property SubServiceCategory[] $subServiceCategories
 */
class ServiceCategory extends Model
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
        'name',
        'description',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
        'deleted_at' => 'datetime:Y-m-d h:i:s'
    ];

    public function delete()
    {
        SubServiceCategory::where([
            'service_category_id' => $this->id
        ])->delete();
        return parent::delete();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subServiceCategories()
    {
        return $this->hasMany('App\Models\SubServiceCategory');
    }

    public function getAllCategories()
    {
        return self::latest()->get();
    }

    public static function getCategoriesDropdownArr()
    {
        return self::pluck('name', 'id')->toArray();
    }

    public function saveNewCategory($inputArr = array())
    {
        if (count($inputArr) > 0) {

            // Upload category image
            $categoryImageUrl = null;
            if (isset($inputArr['image']) && is_file($inputArr['image'])) {
                $image = $inputArr['image'];
                $categoryImageUrl = $this->uploadCategoryImage($image);
            }

            $inputArr['image'] = $categoryImageUrl;
            return self::create($inputArr);
        }
    }

    public function updateCategory($inputArr = array())
    {
        if (count($inputArr) > 0) {

            // update category image
            $oldImage = '';
            if (isset($inputArr['image']) && is_file($inputArr['image'])) {
                $image = $inputArr['image'];
                $categoryImageUrl = $this->uploadCategoryImage($image);

                if ($categoryImageUrl) {
                    $oldImage = $inputArr['old_image'];
                }
            } else {
                $categoryImageUrl = $inputArr['old_image'];
            }

            unset($inputArr['old_image']);
            $inputArr['image'] = $categoryImageUrl;
            $hasUpdated = self::where('id', $this->id)->update($inputArr);

            if ($hasUpdated) {
                // delete old images
                if ($oldImage) {
                    $oldImageName = basename($oldImage);
                    Storage::disk('category-images')->delete($oldImageName);
                }
                return true;
            }
            return false;
        }
        return false;
    }

    public function getCategoryById($id)
    {
        return self::where('id', $id)->first();
    }

    public function uploadCategoryImage($image)
    {
        $fileName = time() . '.' . $image->getClientOriginalExtension();
        Storage::disk('category-images')->putFileAs('/', $image, $fileName);
        return Storage::disk('category-images')->url($fileName);
    }

    public function getCategorySearchListingResponse($keyword, $delivery)
    {
        $categories = self::where('name', 'like', "%{$keyword}%")->get();
        $returnArrData = array();
        foreach ($categories as $category) {
            $returnArr = [
                'category_id' => $category->id,
                'name' => $category->name,
                // 'price' => number_format($product->price, 2),
                'image' => $category->image,
                'delivery' => $delivery
            ];
            array_push($returnArrData, $returnArr);
        }
        return $returnArrData;
    }

    public function jsonResponse()
    {
        $json['id'] = $this->id;
        $json['name'] = $this->name;
        $json['category_type'] = $this->category_type;
        $subServiceCategories = $this->subServiceCategories;

        $response = [];

        foreach ($subServiceCategories as $key => $subServiceCategory) {
            $response[] = $subServiceCategory->jsonResponse();
        }
        $json['sub_service_category'] = $response;


        return $json;
    }
}
