<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class service extends Model
{
    use HasFactory;
    private static $cardserviceData = null;
    private static $cardflagService = false;
    protected $fillable = [
        'business_id',
        'content',
        'is_enabled',
        'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\ServiceFactory::new();
    }
    public static function cardServiceData($id)
    {
        if(self::$cardserviceData == null) {
            if(self::$cardflagService === false){
                $serviceDetail=service::where('business_id', $id)->first();
                self::$cardserviceData = $serviceDetail; 
                self::$cardflagService =  true;
            }       
        }
        return self::$cardserviceData;

    }
}
