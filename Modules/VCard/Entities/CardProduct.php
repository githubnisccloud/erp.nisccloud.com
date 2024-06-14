<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CardProduct extends Model
{
    use HasFactory;

    private static $cardproductData = null;
    private static $cardflagProduct = false;
    protected $fillable = [
        'business_id',
        'content',
        'is_enabled',
        'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\CardProductFactory::new();
    }

    public static function cardProductData($id)
    {
        if(self::$cardproductData == null) {
            if(self::$cardflagProduct === false){
                $cardproductDetail=CardProduct::where('business_id', $id)->first();
                self::$cardproductData = $cardproductDetail; 
                self::$cardflagProduct =  true;
            }       
        }
        return self::$cardproductData;

    }
}
