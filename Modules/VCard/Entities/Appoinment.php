<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class appoinment extends Model
{
    use HasFactory;

    private static $cardappointmentData = null;
    private static $cardflagApp = false;
    protected $fillable = [
        'business_id',
        'content',
        'is_enabled',
        'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\AppoinmentFactory::new();
    }

    public static function cardAppointmentData($id)
    {
        if (self::$cardappointmentData == null) {
            if (self::$cardflagApp === false) {
                $appointmentDetail = appoinment::where('business_id', $id)->first();
                self::$cardappointmentData = $appointmentDetail;
                self::$cardflagApp = true;
            }
        }
        return self::$cardappointmentData;

    }
}
