<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\Hrm\Events\CreateTrip;

class CreateTripLis
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
    public function handle(CreateTrip $event)
    {
        if (module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Trip')) && company_setting('Whatsappapi New Trip')  == true)
        {
            $request = $event->request;
            $employee = \Modules\Hrm\Entities\Employee::where('user_id', '=', $request->employee_id)->first();
            if (!empty($employee->phone)) {
                $msg = $request->purpose_of_visit . ' ' . __("is created to visit") . ' ' . $request->place_of_visit . ' ' . __("for") . ' ' .  $employee->name . ' ' . __("from") . ' ' . $request->start_date . ' ' . __("to") . ' ' . $request->end_date . '.';
                SendMsg::SendMsgs($employee->phone,$msg);
            }
        }
    }
}
