<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\Recruitment\Events\CreateInterviewSchedule;

class CreateInterviewScheduleLis
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
    public function handle(CreateInterviewSchedule $event)
    {
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi Interview Schedule')) && company_setting('Whatsappapi Interview Schedule')  == true)
        {
            $request = $event->request;
            $schedule = $event->schedule;
            $employee = \Modules\Hrm\Entities\Employee::where('id',$request->employee)->first();
            if(!empty($employee->phone)){
                $msg =  __("Interview Schedule created by").' ' . \Auth::user()->name.' '.__("assigning"). ' ' . $schedule->users->name.' '.__("for").' '.$schedule->applications->name .' '.__("Interview").'.';
                SendMsg::SendMsgs($employee->phone,$msg);
            }
        }
    }
}
