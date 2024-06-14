<?php

namespace Modules\ActivityLog\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Entities\Bill;
use Modules\ActivityLog\Entities\AllActivityLog;
use Modules\Account\Events\UpdateBill;

class UpdateBillLis
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
    public function handle(UpdateBill $event)
    {
        if (module_is_active('ActivityLog')) {
            $bill = $event->request;

            $activity                   = new AllActivityLog();
            $activity['module']         = 'Accounting';
            $activity['sub_module']     = 'Expense';
            $activity['description']    = __('Bill ') . Bill::billNumberFormat($bill->bill_id) . __(' Updated by the ');
            $activity['user_id']        =  Auth::user()->id;
            $activity['url']            = '';
            $activity['workspace']      = $bill->workspace;
            $activity['created_by']     = $bill->created_by;
            $activity->save();
        }
    }
}
