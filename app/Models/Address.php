<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_code
 * @property string $iso_code
 * @property string $phone_number
 * @property string $address1
 * @property string $address2
 * @property string $latitude
 * @property string $longitude
 * @property string $street
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $zip_code
 * @property boolean $default_address
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property Booking[] $bookings
 */
class Address extends Model
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
        'first_name',
        'last_name',
        'phone_code',
        'iso_code',
        'phone_number',
        'address1',
        'address2',
        'latitude',
        'longitude',
        'street',
        'city',
        'state',
        'country',
        'zip_code',
        'default_address',
        'created_at',
        'updated_at',
        'deleted_at'
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany('App\Models\Booking');
    }

    public function getAllAddress()
    {
        return self::join('users', 'addresses.user_id', '=', 'users.id')->orderBy('addresses.id', 'desc')
            ->where('users.role_id', NORMAL_USER_TYPE)
            ->select('users.full_name', 'users.role_id', 'addresses.*')
            ->groupBy('addresses.id')
            ->get();
    }

    public function jsonResponse()
    {
        $json['id'] = $this->id;
        $json['address1'] = $this->address1;
        $json['latitude'] = $this->latitude;
        $json['longitude'] = $this->longitude;
        $json['street'] = $this->street;
        $json['city'] = $this->city;
        $json['street'] = $this->street;
        $json['zip_code'] = $this->zip_code;
        $json['user_id'] = $this->user_id;
        $json['first_name'] = $this->first_name;
        $json['phone_code'] = $this->phone_code;
        $json['phone_number'] = $this->phone_number;
        $json['country'] = $this->country;
        $json['default_address'] = $this->default_address;

        return $json;
    }
}
