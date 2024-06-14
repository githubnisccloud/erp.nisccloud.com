<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\Appointment\Events\AppointmentStatus;

class AppointmentStatusLis
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
    public function handle(AppointmentStatus $event)
    {
        if (module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is') == 'on' && !empty(company_setting('Whatsappapi Appointment Status')) && company_setting('Whatsappapi Appointment Status')  == true) {
            $request = $event->request;
            $schedule = $event->schedule;
            if (!empty($schedule->phone)) {
                $msg = __("Your") . ' ' . $schedule->appointment->name . ' ' . __("appointment request has been") . ' ' . $schedule->status . '.';
                SendMsg::SendMsgs($schedule->phone, $msg, $schedule->created_by, $schedule->workspace);
            }
        }
    }
}
