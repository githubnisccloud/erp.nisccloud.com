<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactInfo extends Model
{
    use HasFactory;

    private static $cardcontactData = null;
    private static $cardflagContact = false;
    protected $fillable = [
        'business_id',
        'content',
        'is_enabled',
        'created_by'
    ];
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\ContactInfoFactory::new();
    }

    public static function cardContactData($id)
    {
        if (self::$cardcontactData == null) {
            if (self::$cardflagContact === false) {
                $contactDetail = ContactInfo::where('business_id', $id)->first();
                self::$cardcontactData = $contactDetail;
                self::$cardflagContact = true;
            }
        }
        return self::$cardcontactData;

    }
}
