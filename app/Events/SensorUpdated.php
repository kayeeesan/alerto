<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Models\SensorUnderAlerto;
use App\Models\SensorUnderPh;

class SensorUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sensorUnderAlerto;
    public $sensorUnderPh;

    public function __construct(?SensorUnderAlerto $sensorUnderAlerto = null, ?SensorUnderPh $sensorUnderPh = null)
    {
        $this->sensorUnderAlerto = $sensorUnderAlerto;
        $this->sensorUnderPh = $sensorUnderPh;
    }


    public function broadcastOn()
    {
        return new Channel('public-sensors');
    }

    public function broadcastAs()
    {
        return 'SensorUpdated';
    }
}