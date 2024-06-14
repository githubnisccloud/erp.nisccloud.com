<?php

namespace Modules\VCard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Businessfield extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Modules\VCard\Database\factories\BusinessfieldFactory::new();
    }

    public static function AddBusinessField()
    {
        $data = [
            ['name' => 'phone', 'icon' => 'fa fa-phone'],
            ['name' => 'email', 'icon' => 'fa fa-envelope'],
            ['name' => 'address', 'icon' => 'fa fa-map-marker'],
            ['name' => 'website', 'icon' => 'fa fa-link'],
            ['name' => 'custom_field', 'icon' => 'fa fa-align-left'],
            ['name' => 'facebook', 'icon' => 'fab fa-facebook'],
            ['name' => 'twitter', 'icon' => 'fab fa-twitter'],
            ['name' => 'instagram', 'icon' => 'fab fa-instagram'],
            ['name' => 'whatsapp', 'icon' => 'fab fa-whatsapp'],
        ];
        foreach ($data as $value) {
            \DB::insert(
                'insert into businessfields (`name`,`icon`,`created_at`,`updated_at`) values (?,?,?,?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`) ',
                [$value['name'], $value['icon'], date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]
            );
        }

        return true;
    }
    public static function getFields()
    {
        $icons = [
            'Facebook',
            'Instagram',
            'LinkedIn',
            'Phone',
            'Twitter',
            'Youtube',
            'Email',
            'Behance',
            'Dribbble',
            'Figma',
            'Messenger',
            'Pinterest',
            'Tiktok',
            'Whatsapp',
            'Address',
            'Web_url'
        ];

        return $icons;
    }

}
