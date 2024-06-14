<?php

namespace Modules\WhatsAppAPI\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\WhatsAppAPI\Entities\SendMsg;
use Modules\Sales\Events\CreateSalesOrder;

class CreateSalesOrderLis
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
    public function handle(CreateSalesOrder $event)
    {
        if(module_is_active('WhatsAppAPI') && company_setting('whatsappapi_notification_is')=='on' && !empty(company_setting('Whatsappapi New Sales Order')) && company_setting('Whatsappapi New Sales Order')  == true)
        {
            $salesorder = $event->salesorder;
            $Assign_user_phone = User::where('id',$salesorder->user_id)->first();
            if(!empty($Assign_user_phone->mobile_no))
            {
                $msg = __('New Sales Order ').$salesorder->salesorderNumberFormat(\Modules\Sales\Http\Controllers\SalesOrderController::salesorderNumber()).__(' created by ').Auth::user()->name.'.';
                SendMsg::SendMsgs($Assign_user_phone->mobile_no,$msg);
            }
        }
    }
}
