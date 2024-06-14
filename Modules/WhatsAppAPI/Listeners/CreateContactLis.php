<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\VCard\Events\CreateContact;

class CreateContactLis
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
    public function handle(CreateContact $event)
    {
        if (module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Contact')) && company_setting('Whatsappapi New Contact') == true) {
            
            $request = $event->request;
            $contact = $event->contact;
            $to = \Auth::user()->mobile_no;
            if (!empty($to)) {
                $business_name   = \Modules\VCard\Entities\Business::where('id',$contact->business_id)->pluck('title')->first();
                $msg =  __("New contact created by").' '.$request->name.' for '.$business_name.' business';
                SendMsg::SendMsgs($to, $msg);
            }

        }
    }
}
