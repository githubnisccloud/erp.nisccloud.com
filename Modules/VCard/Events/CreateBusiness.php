<?php

namespace Modules\VCard\Events;

use Illuminate\Queue\SerializesModels;

class CreateBusiness
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $business;
    public function __construct($request ,$business)
    {
        $this->request = $request;
        $this->business = $business;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
