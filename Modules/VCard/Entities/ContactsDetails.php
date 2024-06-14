<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactsDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'email',
        'phone',
        'message',
        'created_by',
        'workspace'
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\ContactsDetailsFactory::new();
    }
}
