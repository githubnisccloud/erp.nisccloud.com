<?php

namespace Modules\LMS\Database\Seeders;

use App\Models\User;
use App\Models\WorkSpace;
use Modules\LMS\Entities\Store;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DefaultStoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $super_admin = User::where('type','super admin')->first();
        if(!empty($super_admin))
        {
            $companys = User::where('type','company')->get();
            if(count($companys) > 0)
            {
                foreach ($companys as $key => $company) {
                    $workspaces = WorkSpace::where('created_by',$company->id)->get();
                    if(count($workspaces) > 0)
                    {
                        foreach($workspaces as $workspace)
                        {
                            $check = Store::where('workspace_id',$workspace->id)->where('created_by',$company->id)->first();
                            if(!$check)
                            {
                                $company_setting = getCompanyAllSetting($company->id,$workspace->id);
                                $store   = Store::create(
                                    [
                                        'created_by' => $company->id,
                                        'name' => $workspace->name,
                                        'lang' => !empty($company_setting['default_language']) ? $company_setting['default_language'] : 'en',
                                        'currency' => !empty($company_setting['currency_symbol']) ? $company_setting['currency_symbol'] : '$',
                                        'currency_code' => !empty($company_setting['currency']) ? $company_setting['currency'] : 'USD',
                                    ]
                                );
                                $store->name                 = $workspace->name;
                                $store->enable_storelink     = 'on';
                                $store->theme_dir            = 'theme1';
                                $store->store_theme          = 'yellow-style.css';
                                $store->header_name          = 'Course Certificate';
                                $store->certificate_template = 'template1';
                                $store->certificate_color    = 'b10d0d';
                                $store->certificate_gradiant = 'color-one';
                                $store->workspace_id         = $workspace->id;
                                $store->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
