<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class NumberUtility extends Model
{
    /**
     * This method generates the random number
     *
     * @param int $length
     *
     * @return int
     */
    public static function getRandomNumber($length = 8): int
    {
        $intMin = (10 ** $length) / 10;
        $intMax = (10 ** $length) - 1;
        
        try {
            $randomNumber = random_int($intMin, $intMax);
        } catch (\Exception $exception) {
            Log::error('Failed to generate random number Retrying...');
            Log::debug(' Error: '.$exception->getMessage());
            $randomNumber = self::getRandomNumber($length);
        }
        return $randomNumber;
    }
}
