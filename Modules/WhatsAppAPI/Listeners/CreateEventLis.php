<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\Hrm\Events\CreateEvent;

class CreateEventLis
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
    public function handle(CreateEvent $event)
    {
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Event')) && company_setting('Whatsappapi New Event')  == true)
        {
            $request = $event->request;
            $branch = \Modules\Hrm\Entities\Branch::find($request->branch_id);
            $employee = \Modules\Hrm\Entities\Employee::whereIn('id', $request->employee_id)->get();
            foreach($employee as $emp){
                if(!empty($emp->phone)){
                    $msg = $request->title . ' ' . __("for branch") . ' ' . $branch->name . ' ' . ("from") . ' ' . $request->start_date . ' ' . __("to") . ' ' . $request->end_date . '.';
                    SendMsg::SendMsgs($emp->phone,$msg);
                }
            }
        }
    }
}
