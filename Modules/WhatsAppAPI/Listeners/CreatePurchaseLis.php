<?php

namespace Modules\WhatsAppAPI\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use App\Models\User;
use Modules\Pos\Events\CreatePurchase;

class CreatePurchaseLis
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
    public function handle(CreatePurchase $event)
    {
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Purchase')) && company_setting('Whatsappapi New Purchase')  == true)
        {
            $request = $event->request;
            $purchase = $event->purchase;
            $vender = \Modules\Account\Entities\Vender::where('id',$request->vender_id)->first();
            if(!empty($vender))
            {
                $moblie = $vender->contact;
            }
            else
            {
                $user = User::where('id',$request->vender_id)->first();
                $moblie = $user->mobile_no;
            }
            if(!empty($moblie))
            {
                $msg = __('New Purchase ').\Modules\Pos\Entities\Purchase::purchaseNumberFormat($purchase->purchase_id).' created by '.\Auth::user()->name.'.';
                SendMsg::SendMsgs($moblie,$msg);
            }
        }
    }
}
