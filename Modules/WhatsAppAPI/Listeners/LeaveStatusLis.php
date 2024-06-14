<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\Hrm\Events\LeaveStatus;

class LeaveStatusLis
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
    public function handle(LeaveStatus $event)
    {
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi Leave Approve/Reject')) && company_setting('Whatsappapi Leave Approve/Reject')  == true)
        {
            $leave = $event->leave;
            $employee = \Modules\Hrm\Entities\Employee::where('id', '=', $leave->employee_id)->first();
            if(!empty($employee->phone)){
                $msg = __("Your leave has been").' '.$leave->status.'.';
                SendMsg::SendMsgs($employee->phone,$msg);
            }
        }
    }
}
