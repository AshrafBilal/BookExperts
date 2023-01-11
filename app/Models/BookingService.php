<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property integer $id
 * @property integer $booking_id
 * @property integer $service_provider_id
 * @property integer $user_id
 * @property float $status
 * @property boolean $booking_type
 * @property int $total_quanity
 * @property float $total_amount
 * @property string $service_started_at
 * @property string $service_completed_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Booking $booking
 * @property User $user
 * @property User $user
 */
class BookingService extends Model
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
        'booking_id',
        'service_provider_id',
        'user_id',
        'status',
        'booking_type',
        'total_quanity',
        'total_amount',
        'service_started_at',
        'service_completed_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'booking_date_time',
        'service_id',
        'business_id'
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking()
    {
        return $this->belongsTo('App\Models\Booking');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceProvider()
    {
        return $this->belongsTo('App\Models\User', 'service_provider_id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'booking_service_id')->where('created_by', Auth::id());
    }

    public function bookedService()
    {
        return $this->belongsTo('App\Models\Service', 'service_id')->withTrashed();
    }

    public function jsonResponse()
    {
        $json = [];
        $json['id'] = $this->id;
        $json['booking_id'] = $this->booking_id;
        $json['service_provider_id'] = $this->service_provider_id;
        $json['service_id'] = @$this->service_id;
        $json['service_name'] = @$this->bookedService->name;
        $json['user_id'] = $this->user_id;
        $json['status'] = $this->status;
        $json['booking_type'] = $this->booking_type;
        $json['total_quanity'] = $this->total_quanity;
        $json['total_amount'] = custom_number_format($this->total_amount);
        $json['service_started_at'] = $this->service_started_at;
        $json['service_completed_at'] = $this->service_completed_at;
        $json['booking_date_time'] = $this->booking_date_time;
        $json['price_per_unit'] = $this->price_per_unit;
        $json['is_rated_by_customer'] = $this->checkServiceIsRateByCustomer();
        $json['rating'] = $this->getRateByCustomer();

        return $json;
    }

    public function checkServiceIsRateByCustomer()
    {
        $user = Auth::user();
        if ($user->role_id == NORMAL_USER_TYPE) {
            return (int) Rating::where([
                'booking_id' => $this->booking_id,
                'booking_service_id' => $this->id,
                'user_id' => $user->id
            ])->exists();
        }
        return (int) Rating::where([
            'booking_id' => $this->booking_id,
            'service_provider_id' => $user->id,
            'booking_service_id' => $this->id
        ])->exists();
    }

    public function getRateByCustomer()
    {
        $user = Auth::user();
        if ($user->role_id == NORMAL_USER_TYPE) {
            $rating = Rating::where([
                'booking_id' => $this->booking_id,
                'booking_service_id' => $this->id,
                'user_id' => $user->id
            ])->first();
        } else {
            $rating = Rating::where([
                'booking_id' => $this->booking_id,
                'service_provider_id' => $user->id,
                'booking_service_id' => $this->id
            ])->first();
        }
        if (! empty($rating)) {
            return $rating->jsonResponse();
        }
        return (object) [];
    }

    public function checkServiceIsRateByProvider()
    {
        return (int) Rating::where([
            'booking_id' => $this->booking_id,
            'service_provider_id' => $this->service_provider_id,
            'created_by' => $this->service_provider_id
        ])->exists();
    }

    public function updateBookingStatus($status)
    {
        if ($status == BOOKING_IN_PROGRESS) {
            $this->service_started_at = date("Y-m-d H:i:s");
        }
        if ($status == BOOKING_COMPLETE) {
            $this->service_completed_at = date("Y-m-d H:i:s");
        }
        $this->status = $status;
        $this->save();
    }
}
