<?php

namespace Modules\VCard\Events;

use Illuminate\Queue\SerializesModels;

class BusinessStatus
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $status;
    public function __construct($status)
    {
        $this->status = $status;
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
