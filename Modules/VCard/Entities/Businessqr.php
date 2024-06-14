<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Businessqr extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'foreground_color',
        'background_color',
        'radius',
        'qr_type',
        'qr_text',
        'qr_text_color',
        'image',
        'size',
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\BusinessqrFactory::new();
    }
}
