<?php

namespace Modules\WhatsAppAPI\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;

class CreateBlogLis
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
    public function handle($event)
    {
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Blog')) && company_setting('Whatsappapi New Blog')  == true)
        {
            $blog = $event->blog;
            if(!empty($blog))
            {
                $Assign_user_phone = User::where('id',$blog->created_by)->first();
                if(!empty($Assign_user_phone->mobile_no)){
                    $store = \Modules\LMS\Entities\Store::where('workspace_id',getActiveWorkSpace())->first();
                    $msg = __('New Blog '). $blog->title . __(' is created by ') . $store->name;

                    SendMsg::SendMsgs($Assign_user_phone->mobile_no,$msg);
                }
            }
        }
    }
}
