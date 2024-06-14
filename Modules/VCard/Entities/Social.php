<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class social extends Model
{
    use HasFactory;

    private static $cardsocialData = null;
    private static $cardflagSocial = false;
    protected $fillable = [
        'business_id',
        'content',
        'is_enabled',
        'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\SocialFactory::new();
    }

    public static function cardSocialData($id)
    {
        if(self::$cardsocialData == null) {
            if(self::$cardflagSocial === false){
                $socialDetail=social::where('business_id', $id)->first();
                self::$cardsocialData = $socialDetail; 
                self::$cardflagSocial =  true;
            }       
        }
        return self::$cardsocialData;

    }
}
