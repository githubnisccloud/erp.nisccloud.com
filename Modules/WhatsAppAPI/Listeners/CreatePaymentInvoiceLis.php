<?php

namespace Modules\WhatsAppAPI\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use App\Events\CreatePaymentInvoice;

class CreatePaymentInvoiceLis
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
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi Invoice status updated')) && company_setting('Whatsappapi Invoice status updated')  == true)
        {
            $invoice = $event->invoice;
            $customer = \Modules\Account\Entities\Customer::where('user_id',$invoice->user_id)->first();
            if(!empty($customer))
            {
                $customer->mobile_no = $customer->contact;
            }
            if(empty($customer))
            {
                $customer =User::where('id',$invoice->user_id)->first();
            }
            if(!empty($customer->mobile_no)){
                $msg = __(" Invoice status updated by "). \Auth::user()->name.'.';
                SendMsg::SendMsgs($customer->mobile_no,$msg);
            }
        }
    }
}
