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
    public ?int $collectionId; // ID della collection per stats specifiche (null = globali)

    public bool $afterCommit = true;

    public function __construct(array $stats, string $updatedAt, ?string $trigger = null, ?int $collectionId = null) {
        $this->stats = $stats;
        $this->updatedAt = $updatedAt;
        $this->trigger = $trigger;
        $this->collectionId = $collectionId;
    }

    public function broadcastOn(): array {
        $channels = [
            new Channel("global.stats") // Sempre broadcast globale
        ];

        // Se è per una collection specifica, aggiungi anche il canale collection
        if ($this->collectionId) {
            $channels[] = new Channel("collection.{$this->collectionId}.stats");
        }

        return $channels;
    }

    public function broadcastAs(): string {
        return 'stats.updated';
    }

    public function broadcastWith(): array {
        return [
            'stats' => $this->stats,
            'updated_at' => $this->updatedAt,
            'trigger' => $this->trigger,
            'collection_id' => $this->collectionId,
            'context' => $this->collectionId ? 'collection' : 'global'
        ];
    }
}
