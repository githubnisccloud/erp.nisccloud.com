<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class testimonial extends Model
{
    use HasFactory;
    private static $cardtestimonialData = null;
    private static $cardflagTestimonial = false;

    protected $fillable = [
        'business_id',
        'content',
        'is_enabled',
        'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\TestimonialFactory::new();
    }

    public static function cardTestimonialData($id)
    {
        if(self::$cardtestimonialData == null) {
            if(self::$cardflagTestimonial === false){
                $testDetail=testimonial::where('business_id', $id)->first();
                self::$cardtestimonialData = $testDetail; 
                self::$cardflagTestimonial =  true;
            }       
        }
        return self::$cardtestimonialData;

    }
}
