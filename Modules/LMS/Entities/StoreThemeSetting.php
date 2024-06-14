<?php

namespace Modules\LMS\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreThemeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'type',
        'store_id',
        'theme_name',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Modules\LMS\Database\factories\StoreThemeSettingFactory::new();
    }
}
