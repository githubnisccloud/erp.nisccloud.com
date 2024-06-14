<?php

namespace Modules\VCard\Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class PermissionTableSeeder extends Seeder
{
     public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');
        $module = 'VCard';

        $permissions  = [
            'vcard manage',
            'vcard dashboard manage',
            'business manage',
            'business create',
            'business edit',
            'business delete',
            'business theme settings',
            'business custom settings',
            'business block settings',
            'business SEO settings',
            'business PWA settings',
            'business pixel settings',
            'card appointment manage',
            'card appointment add note',
            'card appointment delete',
            'card appointment calendar',
            'card contact manage',
            'card contact add note',
            'card contact delete'
        ];

        $company_role = Role::where('name','company')->first();
        foreach ($permissions as $key => $value)
        {
            $table = Permission::where('name',$value)->where('module',$module)->exists();
            if(!$table)
            {
                $permission = Permission::create(
                    [
                        'name' => $value,
                        'guard_name' => 'web',
                        'module' => $module,
                        'created_by' => 0,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]
                );
                if(!$company_role->hasPermission($value))
                {
                    $company_role->givePermission($permission);
                }
            }
        }
    }
}
