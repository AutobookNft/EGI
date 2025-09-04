<?php

namespace App\Events;

use App\Helpers\FegiAuth;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserWelcomeUpdated implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $welcomeMessage;
    public $userName;

    /**
     * Create a new event instance.
     */
    public function __construct($userId) {
        $this->userId = $userId;

        // Genera i dati aggiornati usando la logica backend completa
        $this->welcomeMessage = FegiAuth::getWelcomeMessage();
        $this->userName = FegiAuth::getUserName();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array {
        return [
            new PrivateChannel('user-welcome.' . $this->userId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string {
        return 'welcome.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array {
        return [
            'welcome_message' => $this->welcomeMessage,
            'user_name' => $this->userName,
            'updated_at' => now()->toISOString(),
        ];
    }
}
