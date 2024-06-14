<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\VCard\Events\CreateBusiness;

class CreateBusinessLis
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
    public function handle(CreateBusiness $event)
    {
         if (module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Business')) && company_setting('Whatsappapi New Business') == true) {
            $request = $event->request;
            $business = $event->business;
            $to = \Auth::user()->mobile_no;
            if (!empty($to)) {
                $msg=__("The New Business ".$request->business_title." is created by").' '.\Auth::user()->name.'.';
                SendMsg::SendMsgs($to, $msg);
            }

        }
    }
}
