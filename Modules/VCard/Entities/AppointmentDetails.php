<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\VCard\Entities\Business;

class AppointmentDetails extends Model
{
    use HasFactory;

    private static $appointmentBusinessData=null;
    private static $appBusinessname=null;
    protected $fillable = [
        'business_id',
        'name',
        'email',
        'phone',
        'date',
        'time',
        'status',
        'note',
        'created_by',
        'workspace'
    ];

    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\AppointmentDetailsFactory::new();
    }

    public function getBussinessName(){
        if(self::$appBusinessname==null)
        {
            $business = Business::find($this->business_id);
            if(!empty($business)){
                self::$appBusinessname=$business->title;
            }else{
                return ' - ';
            }
            
        }
        return self::$appBusinessname;
    }

    public static function getBusinessData($id)
    {
        if(self::$appointmentBusinessData==null)
        {
            $business=Business::where('id', $id)->pluck('title')->first();
            self::$appointmentBusinessData=$business;
        }
        return self::$appointmentBusinessData;
    }
}