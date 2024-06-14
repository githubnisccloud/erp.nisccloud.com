<?php

namespace Modules\WhatsAppAPI\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\LMS\Events\CreateCustomPage;

class CreateCustomPageLis
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
    public function handle(CreateCustomPage $event)
    {
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Custom Page')) && company_setting('Whatsappapi New Custom Page')  == true)
        {
            $pageOption = $event->pageOption;
            if(!empty($pageOption))
            {
                $Assign_user_phone = User::where('id',$pageOption->created_by)->first();
                if(!empty($Assign_user_phone->mobile_no)){
                    $store = \Modules\LMS\Entities\Store::where('workspace_id',getActiveWorkSpace())->first();
                    $msg = __('New Custom Page '). $pageOption->name . __(' is created by ') . $store->name;

                    SendMsg::SendMsgs($Assign_user_phone->mobile_no,$msg);
                }
            }
        }
    }
}
