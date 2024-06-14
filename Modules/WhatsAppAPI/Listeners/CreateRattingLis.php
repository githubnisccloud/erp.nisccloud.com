<?php

namespace Modules\WhatsAppAPI\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;

class CreateRattingLis
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
        $ratting = $event->ratting;
        if(!empty($ratting)){
            $store = \Modules\LMS\Entities\Store::where('slug',$ratting->slug)->first();
            if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Ratting',$store->created_by,$store->workspace_id)) && company_setting('Whatsappapi New Ratting',$store->created_by,$store->workspace_id)  == true)
            {
                $Assign_user_phone = User::where('id',$store->created_by)->first();
                if(!empty($Assign_user_phone->mobile_no)){
                    $student = \Modules\LMS\Entities\Student::where('id',$ratting->student_id)->first();
                    $course = \Modules\LMS\Entities\Course::where('id',$ratting->course_id)->first();
                    $msg = $student->name .__(' added a review for ') . $course->title . __(' at '). $store->name.'.';
                    SendMsg::SendMsgs($Assign_user_phone->mobile_no,$msg);
                }
            }
        }
    }
}
