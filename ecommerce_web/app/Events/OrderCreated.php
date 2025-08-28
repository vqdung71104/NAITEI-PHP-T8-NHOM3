<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use SerializesModels;

    public array $order;

    public function __construct(Order $order)
    {
        $this->order = [
            'id' => $order->id,
            'total_price' => $order->total_price,
            'status' => $order->status,
            'created_at' => $order->created_at?->toDateTimeString(),
        ];
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin.notifications')];
    }

    public function broadcastAs(): string
    {
        return 'OrderCreated';
    }
}