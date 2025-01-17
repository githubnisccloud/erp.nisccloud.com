<?php

namespace Modules\ActivityLog\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Modules\ActivityLog\Entities\AllActivityLog;
use Modules\CMMS\Events\CreatePart;

class CreatePartLis
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
    public function handle(CreatePart $event)
    {
        if (module_is_active('ActivityLog')) {
            $parts = $event->parts;

            $activity                   = new AllActivityLog();
            $activity['module']         = 'CMMS';
            $activity['sub_module']     = 'Parts';
            $activity['description']    = __('New Parts Created by the ');
            $activity['user_id']        =  Auth::user()->id;
            $activity['url']            = '';
            $activity['workspace']      = $parts->workspace;
            $activity['created_by']     = $parts->created_by;
            $activity->save();
        }
    }
}
