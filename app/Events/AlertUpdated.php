<?php

namespace App\Events;

use App\Models\Alert;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlertUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alert; 

    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    public function broadcastOn()
    {
        return new Channel('alerts-updated'); 
    }

    public function broadcastAs()
    {
        return 'AlertUpdated'; 
    }
}

