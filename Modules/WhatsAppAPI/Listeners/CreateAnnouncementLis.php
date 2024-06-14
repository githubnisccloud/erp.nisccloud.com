<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\Hrm\Events\CreateAnnouncement;

class CreateAnnouncementLis
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
    public function handle(CreateAnnouncement $event)
    {
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Announcement')) && company_setting('Whatsappapi New Announcement')  == true)
        {
            $request = $event->request;
            $branch = \Modules\Hrm\Entities\Branch::where('id', '=', $request->branch_id)->first();
            $employee = \Modules\Hrm\Entities\Employee::whereIn('id', $request->employee_id)->get();
            foreach($employee as $emp){
                if(!empty($emp->phone)){
                    $msg = $request->title.' '. __("announcement created for branch").' '.$branch->name.' '. __("from").' '.$request->start_date.' '. __("to").' '.$request->end_date.'.';
                    SendMsg::SendMsgs($emp->phone,$msg);
                }
            }
        }
    }
}
