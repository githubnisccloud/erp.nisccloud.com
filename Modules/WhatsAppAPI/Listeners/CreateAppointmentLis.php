<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\VCard\Events\CreateAppointment;

class CreateAppointmentLis
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
    public function handle(CreateAppointment $event)
    {
        if (module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Appointment')) && company_setting('Whatsappapi New Appointment') == true) {
            $request = $event->request;
            $appointment = $event->appointment;
            $to = \Auth::user()->mobile_no;
            
            if (!empty($to)) {
                $business_name = \Modules\VCard\Entities\Business::where('id', $appointment->business_id)->pluck('title')->first();
                $msg = $request->name . ' ' . __("is booking an appointment on") . ' ' . $request->date . ' ' . __("at") . ' ' . $request->time . ' for ' . $business_name . ' business';
                SendMsg::SendMsgs($to, $msg);
            }

        }
    }
}
