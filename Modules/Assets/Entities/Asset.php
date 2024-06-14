<?php

namespace Modules\Assets\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'purchase_date',
        'supported_date',
        'quantity',
        'serial_code',
        'assets_unit',
        'purchase_cost',
        'asset_image',
        'description',
        'branch',
        'warranty_period',
        'created_by',
        'workspace_id',
    ];

    protected static function newFactory()
    {
        return \Modules\Assets\Database\factories\AssetFactory::new();
    }
}
