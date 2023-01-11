<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BookServiceController;

/**
 *
 * @property integer $id
 * @property integer $service_id
 * @property integer $service_provider_id
 * @property integer $user_id
 * @property integer $created_by
 * @property int $rating
 * @property string $review
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property Service $service
 * @property User $user
 * @property User $user
 */
class Rating extends Model
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
        'service_id',
        'service_provider_id',
        'user_id',
        'created_by',
        'rating',
        'review',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by')->withTrashed();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo('App\Models\Service', 'booking_service_id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo('App\Models\User', 'service_provider_id')->withTrashed();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public static function saveRating($request)
    {
        $rating = Rating::where([
            'booking_service_id' => $request->service_id,
            'created_by' => Auth::id()
        ])->first();
        $bookedServive = BookingService::find($request->service_id);
        if (! empty($bookedServive)) {
            $rating = ! empty($rating) ? $rating : new self();
            $rating->rating = $request->rating;
            $rating->review = $request->review;
            $rating->booking_service_id = $bookedServive->id;
            $rating->booking_id = $bookedServive->booking_id;
            $rating->service_provider_id = $bookedServive->service_provider_id;
            $rating->user_id = $bookedServive->user_id;
            $rating->created_by = Auth::id();
            return $rating->save();
        }
        return false;
    }

    public static function saveCustomerRating($request)
    {
        $rating = Rating::where([
            'booking_id' => $request->order_id,
            'created_by' => Auth::id()
        ])->first();
        $bookedServive = Booking::find($request->order_id);
        if (! empty($bookedServive)) {
            $rating = ! empty($rating) ? $rating : new self();
            $rating->rating = $request->rating;
            $rating->review = $request->review;
            $rating->booking_id = $bookedServive->id;
            $rating->service_provider_id = $bookedServive->service_provider_id;
            $rating->user_id = $bookedServive->user_id;
            $rating->created_by = Auth::id();
            return $rating->save();
        }
        return false;
    }

    public function jsonResponse()
    {
        if (! empty($this->booking_service_id)) {
            $bookService = BookingService::find($this->booking_service_id);
            $bokkedService = $bookService->bookedService;
        }
        $json = [];
        $json['id'] = $this->id;
        $json['booking_id'] = $this->booking_id;
        $json['booking_service_id'] = $this->booking_service_id;
        $json['service_provider_id'] = $this->service_provider_id;
        $json['user_id'] = $this->user_id;
        $json['rating'] = $this->rating;
        $json['review'] = $this->review;
        $createdBy = $this->createdBy;
        $json['customer_image'] = @$createdBy->profile_file;
        $json['customer_name'] = @$createdBy->getFullName();
        $json['service_name'] = @$bokkedService->name;
        $json['service_image'] = @$bokkedService->service_image;
        return $json;
    }

    public static function getReviewList($request)
    {
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 10;
        $query = self::latest()->where('service_provider_id', $request->query('provider_id'))
            ->whereNotNull('booking_service_id')
            ->paginate($page_limit);
        $items = $query->items();
        $json = [];
        foreach ($items as $item) {
            $json[] = $item->jsonResponse();
        }
        $data['next_page'] = ($query->nextPageUrl()) ? true : false;
        $data['reviews'] = $json;
        $data['current_page'] = $query->currentPage();
        $data['per_page'] = $query->perPage();
        $data['links'] = $query->linkCollection();
        $data['total'] = $query->total();
        $data['total_pages'] = (int) ceil($query->total() / $query->perPage());
        return $data;
    }
    
   
}
