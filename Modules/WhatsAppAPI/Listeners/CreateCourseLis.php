<?php

namespace Modules\WhatsAppAPI\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\LMS\Events\CreateCourse;

class CreateCourseLis
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
    public function handle(CreateCourse $event)
    {
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Course')) && company_setting('Whatsappapi New Course')  == true)
        {
            $course = $event->course;
            $Assign_user_phone = User::where('id',$course->created_by)->first();
            if(!empty($Assign_user_phone->mobile_no)){
                $store = \Modules\LMS\Entities\Store::where('workspace_id',getActiveWorkSpace())->first();
                $msg = __('New Course '). $course->title . __(' is created by ') . $store->name;

                SendMsg::SendMsgs($Assign_user_phone->mobile_no,$msg);
            }
        }
    }
}
