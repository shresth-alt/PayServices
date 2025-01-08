<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Order;

class OrderCreated
{
  use Dispatchable, InteractsWithSockets, SerializesModels;
  
  public $order, $service_id, $postal_code;
  /**
  * Create a new event instance.
  *
  * @return void
  */
  public function __construct(Order $order, $service_id, $postal_code)
  {
    $this->order = $order;
    $this->service_id = $service_id;
    $this->postal_code = $postal_code;
  }
  
  /**
  * Get the channels the event should broadcast on.
  *
  * @return \Illuminate\Broadcasting\Channel|array
  */
  public function broadcastOn()
  {
    return new PrivateChannel('channel-name');
  }
}
