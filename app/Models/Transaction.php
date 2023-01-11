<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $booking_id
 * @property string $transaction_id
 * @property string $type
 * @property float $amount
 * @property integer $card_id
 * @property boolean $card_number
 * @property string $payment_mode
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Booking $booking
 * @property User $user
 */
class Transaction extends Model
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
        'booking_id',
        'transaction_id',
        'type',
        'amount',
        'card_id',
        'card_number',
        'payment_mode',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
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
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getAllTransactions()
    {
        return self::latest()->get();
    }

    public function getPaymentStatus()
    {
        $list = [
            PAYMENT_FAILED => 'Failed',
            PAYMENT_PENDING => 'Pending',
            PAYMENT_SUCCESS => 'Success '
        ];
        return ! empty($list[$this->status]) ? $list[$this->status] : null;
    }

    public function getPaymentType()
    {
        $list = [
            BOOKING_PAYMENT => 'Booking',
            TIP_PAYMENT => 'Tip'
        ];
        return ! empty($list[$this->type]) ? $list[$this->type] : null;
    }

    public function getPaymentMode()
    {
        $list = [
            COD_PAYMENT_METHOD => 'COD',
            ONLINE_PAYMENT_METHOD => 'Online'
        ];
        return ! empty($list[$this->payment_mode]) ? $list[$this->payment_mode] : 'Online';
    }
}
