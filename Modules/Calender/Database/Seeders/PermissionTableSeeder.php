<?php

namespace Modules\Calender\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Artisan;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');

        $permission  = [
            'calander manage',
            'calander show',
        ];

        $company_role = Role::where('name','company')->first();
        $super_admin = Role::where('name','super admin')->first();
        foreach ($permission as $key => $value)
        {
            $table = Permission::where('name',$value)->where('module','Calender')->exists();
            if(!$table)
            {
                $data = Permission::create(
                    [
                        'name' => $value,
                        'guard_name' => 'web',
                        'module' => 'Calender',
                        'created_by' => 0,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]
                );
                if(!$company_role->hasPermission($value))
                {
                    $company_role->givePermission($data);
                }
                if(!$super_admin->hasPermission($value))
                {
                    $super_admin->givePermission($data);
                }
            }
        }
    }


}
