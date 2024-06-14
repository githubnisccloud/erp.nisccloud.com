<?php

namespace Modules\LMS\Listeners;

use App\Events\DefaultData;
use App\Models\WorkSpace;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\LMS\Entities\Store;

class DataDefault
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DefaultData $event)
    {
        $company_id = $event->company_id;
        $workspace_id = $event->workspace_id;
        if(empty($workspace_id))
        {
            $workspace_id = getActiveWorkSpace();
        }
        $workspace = WorkSpace::where('id',$workspace_id)->first();
        $company_settings = getCompanyAllSetting($company_id,$workspace_id);
        if(!empty($workspace))
        {
            $store   = Store::create(
                [
                    'created_by' => $company_id,
                    'name' => $workspace->name,
                    'lang' => !empty($company_settings['default_language']) ? $company_settings['default_language'] : 'en',
                    'currency' => !empty($company_settings['currency_symbol']) ? $company_settings['currency_symbol'] : '$',
                    'currency_code' => !empty($company_settings['currency']) ? $company_settings['currency'] : 'USD',
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
            $store->workspace_id         = $workspace_id;
            $store->save();
        }
    }
}
