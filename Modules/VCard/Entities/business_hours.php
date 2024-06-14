<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class business_hours extends Model
{
    use HasFactory;
    private static $cardBusinessHourData = null;
    private static $cardflaghour = false;
    protected $fillable = [
        'business_id',
        'content',
        'is_enabled',
        'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\BusinessHoursFactory::new();
    }

    public static $days = [
        'sun' => 'Sunday',
        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thu' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
    ];

    public static function cardBusinessHour($id)
    {
        if (self::$cardBusinessHourData == null) {
            if (self::$cardflaghour === false) {
                $business_hours = business_hours::where('business_id', $id)->first();
                self::$cardBusinessHourData = $business_hours;
                self::$cardflaghour = true;
            }
        }
        return self::$cardBusinessHourData;
    }

}
