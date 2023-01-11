<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property boolean $day
 * @property string $start_time
 * @property string $end_time
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 */
class ProviderTiming extends Model
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
        'day',
        'start_time',
        'end_time',
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

    public static function getProviderTiming($id = null, $date = null)
    {
        $json = $booked_slots = $data = [];
        $sunday = $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = [];
        if (! empty($id)) {
            $timings = self::where('user_id', $id)->whereBetween('day', [
                0,
                6
            ])
                ->orderBy('day', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();
        } else {
            $timings = self::where('user_id', Auth::id())->whereBetween('day', [
                0,
                6
            ])
                ->orderBy('day', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();
        }

        $checkEmptyBookings = Booking::whereNull('user_booking_date_time')->exists();
        if (! empty($checkEmptyBookings)) {
            $bookings = Booking::whereNull('user_booking_date_time')->get();
            foreach ($bookings as $booking) {
                $booking->user_booking_date_time = changeTimeZone($booking->booking_date_time);
                $booking->save();
            }
        }

        $usedDate = ! empty($date) ? $date : date("Y-m-d");
        $bookings = Booking::where([
            'service_provider_id' => ! empty($id) ? $id : Auth::id()
        ])->whereDate('user_booking_date_time', $usedDate)
            ->whereIn('status', [
            BOOKING_ACCEPT,
            BOOKING_IN_PROGRESS,
            BOOKING_PENDING,
            BOOKING_ARRIVED
        ])
            ->orderBy('booking_date_time', 'asc')
            ->get();

        if (! $bookings->isEmpty()) {
            foreach ($bookings as $booking) {
                $booking->booking_date_time = changeTimeZone($booking->booking_date_time);
                $totalTimes = $booking->getServicesTotalTime();
                $booked_slots[] = date("H:i:s", strtotime($booking->booking_date_time)) . ' to ' . date('H:i:s', strtotime("+$totalTimes minutes", strtotime($booking->booking_date_time)));
            }
        } else {
            $booked_slots = [];
        }
        $booked_slots = array_values(array_unique($booked_slots));

        if (! empty($timings->count())) {

            foreach ($timings as $timing) {

                $day = ($timing->day == 0) ? 7 : $timing->day;

                switch ($day) {
                    case $day == 7:
                        $sunday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $sunday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $sunday['time'] = [];
                        }
                        break;
                    case $day == MONDAY:

                        $monday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $monday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $monday['time'] = [];
                        }
                        break;
                    case $day == TUESDAY:

                        $tuesday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $tuesday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $tuesday['time'] = [];
                        }
                        break;
                    case $day == WEDNESDAY:

                        $wednesday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $wednesday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $wednesday['time'] = [];
                        }
                        break;
                    case $day == THURSDAY:

                        $thursday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $thursday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $thursday['time'] = [];
                        }
                        break;
                    case $day == FRIDAY:

                        $friday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $friday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $friday['time'] = [];
                        }
                        break;
                    case $day == SATURDAY:

                        $saturday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $saturday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $saturday['time'] = [];
                        }
                        break;
                }
            }
        }

        if (empty(count($sunday))) {
            $sunday = (object) [];
        }
        if (empty(count($monday))) {
            $monday = (object) [];
        }
        if (empty(count($tuesday))) {
            $tuesday = (object) [];
        }
        if (empty(count($wednesday))) {
            $wednesday = (object) [];
        }
        if (empty(count($thursday))) {
            $thursday = (object) [];
        }
        if (empty(count($friday))) {
            $friday = (object) [];
        }
        if (empty(count($saturday))) {
            $saturday = (object) [];
        }

        array_push($json, $sunday, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday);

        if (! empty($date)) {
            $data['provider_timings'] = $json;
            $data['booked_slots'] = $booked_slots;
            return $data;
        }
        return $json;
    }

    public static function getIndividualProviderTiming($id)
    {
        $provider = User::find($id);
        $json = $data = [];
        $sunday = $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = [];
        $timings = self::where('user_id', $id)->whereBetween('day', [
            0,
            6
        ])
            ->orderBy('day', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        if (! empty($timings->count())) {

            foreach ($timings as $timing) {
                $day = ($timing->day == 0) ? 7 : $timing->day;
                switch ($day) {
                    case $day == 7:
                        $sunday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $sunday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $sunday['time'] = [];
                        }
                        break;
                    case $day == MONDAY:

                        $monday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $monday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $monday['time'] = [];
                        }
                        break;
                    case $day == TUESDAY:

                        $tuesday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $tuesday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $tuesday['time'] = [];
                        }
                        break;
                    case $day == WEDNESDAY:

                        $wednesday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $wednesday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $wednesday['time'] = [];
                        }
                        break;
                    case $day == THURSDAY:

                        $thursday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $thursday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $thursday['time'] = [];
                        }
                        break;
                    case $day == FRIDAY:

                        $friday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $friday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $friday['time'] = [];
                        }
                        break;
                    case $day == SATURDAY:

                        $saturday['full_day'] = $timing->off_day_type;
                        if (! empty($timing->start_time)) {
                            $saturday['time'][] = $timing->start_time . " to " . $timing->end_time;
                        } else {
                            $saturday['time'] = [];
                        }
                        break;
                }
            }
        }

        if (empty(count($sunday))) {
            $sunday = (object) [];
        }
        if (empty(count($monday))) {
            $monday = (object) [];
        }
        if (empty(count($tuesday))) {
            $tuesday = (object) [];
        }
        if (empty(count($wednesday))) {
            $wednesday = (object) [];
        }
        if (empty(count($thursday))) {
            $thursday = (object) [];
        }
        if (empty(count($friday))) {
            $friday = (object) [];
        }
        if (empty(count($saturday))) {
            $saturday = (object) [];
        }

        array_push($json, $sunday, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday);
        $data['service_provider'] = $provider->minimizeJsonResponse();
        $data['provider_timings'] = $json;
        return $data;
    }

    public static function checkAvailability($request)
    {
        $providerId = $request->provider_id;
        $startTime = date('H:i:s', strtotime($request->booking_date_time));
        $bookingDay = date('w', strtotime($request->booking_date_time));
        $minutes = Service::whereIn('id', $request->booked_services)->sum('time');

        $endTime = date("H:i:s", strtotime("+$minutes minutes"));
        $Availability = self::where([
            'user_id' => $providerId,
            'day' => $bookingDay
        ])->first();
        if (! empty($Availability)) {
            if ($Availability->off_day_type == OPEN_FULL_DAY) {
                return ACTIVE_STATUS;
            }

            $Availability = self::where(function ($query) use ($startTime, $endTime, $providerId, $bookingDay) {
                $query->where(function ($q) use ($startTime, $endTime, $providerId, $bookingDay) {
                    $q->where('user_id', $providerId)
                        ->where('day', $bookingDay)
                        ->where('start_time', '>=', $startTime)
                        ->where('start_time', '<', $endTime);
                })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                        ->where('end_time', '>', $endTime);
                });
            })->exists();
            if (! empty($Availability)) {
                return ACTIVE_STATUS;
            }
        }
        return INACTIVE_STATUS;

    }
    
    
}
