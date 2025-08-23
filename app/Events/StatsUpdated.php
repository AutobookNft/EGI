<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatsUpdated implements ShouldBroadcastNow {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $stats;
    public string $updatedAt;
    public ?string $trigger; // Cosa ha causato l'aggiornamento (reservation, cancellation, etc.)

    public bool $afterCommit = true;

    public function __construct(array $stats, string $updatedAt, ?string $trigger = null) {
        $this->stats = $stats;
        $this->updatedAt = $updatedAt;
        $this->trigger = $trigger;
    }

    public function broadcastOn(): Channel {
        return new Channel("global.stats");
    }

    public function broadcastAs(): string {
        return 'stats.updated';
    }

    public function broadcastWith(): array {
        return [
            'stats' => $this->stats,
            'updated_at' => $this->updatedAt,
            'trigger' => $this->trigger,
        ];
    }
}
