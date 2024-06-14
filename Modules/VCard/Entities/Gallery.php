<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gallery extends Model
{
    use HasFactory;
    private static $cardgalleryData = null;
    private static $cardflagGallery = false;

    protected $fillable = [
        'business_id',
        'content',
        'is_enabled',
        'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\GalleryFactory::new();
    }

     //Gallary Option
     public static $gallaryOption = [
        'video' => 'Video',
        'image' => 'Image',
        'custom_image_link' => 'Custom Image',
        'custom_video_link' => 'Custom Video',
    ];

    public static function cardGalleryData($id)
    {
        if(self::$cardgalleryData == null) {
            if(self::$cardflagGallery === false){
                $galleryDetail=Gallery::where('business_id', $id)->first();
                self::$cardgalleryData = $galleryDetail; 
                self::$cardflagGallery =  true;
            }       
        }
        return self::$cardgalleryData;

    }


}
