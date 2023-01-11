<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 *
 * @property integer $user_id
 * @property integer $service_provider_id
 * @property integer $booking_id
 * @property integer $booking_service_id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Booking $booking
 * @property BookingService $bookingService
 * @property User $user
 * @property User $user
 * @property User $created_by
 */
class BookingStatusHistory extends Model
{

    /**
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'service_provider_id',
        'booking_id',
        'booking_service_id',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by'
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
    public function bookingService()
    {
        return $this->belongsTo('App\Models\BookingService');
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

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public static function saveStatusHistory($booking, $status)
    {
        $exist = self::where([
            'booking_id' => $booking->id,
            'service_provider_id' => $booking->service_provider_id,
            'user_id' => $booking->user_id,
            'status' => $status
        ])->exists();
        if (empty($exist)) {
            $model = new self();
            $model->booking_id = $booking->id;
            $model->user_id = $booking->user_id;
            $model->service_provider_id = $booking->service_provider_id;
            $model->status = $status;
            $model->created_by = Auth::id();
            $model->save();
            BookingService::where([
                'booking_id' => $booking->id,
                'service_provider_id' => $booking->service_provider_id,
                'user_id' => $booking->user_id
            ])->update([
                'status' => $status
            ]);
            BookingService::where([
                'booking_id' => $booking->id,
                'service_provider_id' => $booking->service_provider_id,
                'user_id' => $booking->user_id,
                'status' => BOOKING_IN_PROGRESS
            ])->update([
                'service_started_at' => date("Y-m-d H:i:s")
            ]);
            BookingService::where([
                'booking_id' => $booking->id,
                'service_provider_id' => $booking->service_provider_id,
                'user_id' => $booking->user_id,
                'status' => BOOKING_COMPLETE
            ])->update([
                'service_completed_at' => date("Y-m-d H:i:s")
            ]);
            return true;
        }
        return false;
    }
}
