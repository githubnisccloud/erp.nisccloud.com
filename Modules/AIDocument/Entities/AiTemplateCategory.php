<?php

namespace Modules\AIDocument\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiTemplateCategory extends Model
{
    use HasFactory;

    
    protected $table='ai_template_categories';
    protected $fillable = [
    	'name',
    	'status',
    	'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\AIDocument\Database\factories\AiTemplateCategoryFactory::new();
    }
}
