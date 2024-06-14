<?php

namespace Modules\VCard\Events;

use Illuminate\Queue\SerializesModels;

class DestroyBusiness
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $business;
    public function __construct($business)
    {
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
