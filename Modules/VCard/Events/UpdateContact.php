<?php

namespace Modules\VCard\Events;

use Illuminate\Queue\SerializesModels;

class UpdateContact
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $contact;
    public function __construct($contact,$request)
    {
        $this->request = $request;
        $this->contact = $contact;
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
