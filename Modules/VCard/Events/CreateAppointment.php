<?php

namespace Modules\VCard\Events;

use Illuminate\Queue\SerializesModels;

class CreateAppointment
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $appointment;
    public function __construct($request ,$appointment)
    {
        $this->request = $request;
        $this->appointment = $appointment;
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
