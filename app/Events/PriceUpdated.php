<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PriceUpdated implements ShouldBroadcastNow {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $egiId;
    public string $amount;
    public string $currency;
    public string $updatedAt;

    public bool $afterCommit = true;

    public function __construct(int $egiId, string $amount, string $currency, string $updatedAt) {
        $this->egiId     = $egiId;
        $this->amount    = $amount;
        $this->currency  = $currency;
        $this->updatedAt = $updatedAt;
    }

    public function broadcastOn(): Channel {
        return new Channel("price.{$this->egiId}");
    }

    public function broadcastAs(): string {
        return 'price.updated';
    }

    public function broadcastWith(): array {
        return [
            'id'         => $this->egiId,
            'amount'     => $this->amount,
            'currency'   => $this->currency,
            'updated_at' => $this->updatedAt,
        ];
    }
}
