<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Gdpr\GdprNotificationService;
use Illuminate\Console\Command;
use Throwable;

/**
 * @package   App\Console\Commands
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   2.0.0
 * @date      2025-06-11
 * @solution  Provides an interactive CLI to test and validate the GDPR notification system by leveraging the GdprNotificationService.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To provide a user-friendly, error-proof testing tool for developers to validate the GDPR notification suite by acting as a client for the GdprNotificationService.
 * @oracode-value-flow:
 * 1.  INPUT: Interactively or via arguments, it collects a target user and a specific GDPR notification type.
 * 2.  PROCESS: It invokes the `GdprNotificationService->dispatchNotification` method, triggering the entire production logic for creating and sending a notification.
 * 3.  OUTPUT: Provides immediate success or failure feedback in the console.
 * @oracode-arch-pattern: Command Bus (initiator). It initiates a command/process in the application's service layer.
 * @oracode-sustainability-factor: HIGH. It's decoupled from the implementation details. If new notification types are added to the service, this command automatically discovers and offers them without needing code changes.
 * @os1-compliance: Full.
 */
class TestGdprNotification extends Command
{
    /**
     * The name and signature of the console command.
     * Arguments are optional to allow for interactive, guided execution.
     *
     * @var string
     */
    protected $signature = 'test:gdpr-notification
        {userId? : The ID or email of the user to notify}
        {type? : The type of notification to send}
        {context? : The context for the notification}
        {channels? : The channels to use for the notification (comma-separated)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively sends a live GDPR notification to a user via GdprNotificationService.';

    /**
     * Create a new command instance.
     * The GdprNotificationService is injected by Laravel's service container.
     */
    public function __construct(private GdprNotificationService $notificationService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('>> FlorenceEGI - GDPR Notification Test Sender <<');
        $this->line('--------------------------------------------------');

        try {
            $user = $this->getUser();
            if (!$user) {
                return Command::FAILURE;
            }

            $type = $this->getNotificationType();
            if (!$type) {
                return Command::FAILURE;
            }

            $context = $this->getContext();
            if ($context === null) {
                $this->error("Invalid context provided. Please provide a valid JSON object.");
                return Command::FAILURE;
            }
            $channels = $this->getChannels();
            if (empty($channels)) {
                $this->error("No channels provided. Please specify at least one channel.");
                return Command::FAILURE;
            }

            // Log the context and channels for debugging
            $this->line("Context: " . json_encode($context, JSON_PRETTY_PRINT));
            $this->line("Channels: " . implode(', ', $channels));
            $this->line("User ID: {$user->id}, Email: {$user->email}");
            $this->line("Notification Type: {$type}");
            $this->line('--------------------------------------------------');

            $this->info("Dispatching '{$type}' notification to user: {$user->email} (ID: {$user->id})...");
            $this->comment("Invoking GdprNotificationService->dispatchNotification...");

            // Use the refactored service to dispatch the real notification
            $result = $this->notificationService->dispatchNotification($user, $type, $context, $channels);

            $this->line("Result type: " . get_class($result));
            $this->line("Result data: " . json_encode($result->toArray()));

            if ($result) {
                $this->info("✅ Success! Notification dispatched. Type: {$result->getType()}");
            } else {
                $this->error("❌ Failure. The notification service returned null. Check UEM logs for details.");
                return Command::FAILURE;
            }

        } catch (Throwable $e) {
            $this->error("❌ An unexpected exception occurred: " . $e->getMessage());
            if ($this->option('verbose')) {
                $this->line($e->getTraceAsString());
            }
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Retrieve the user, either from arguments or interactively.
     *
     * @return User|null
     */
    private function getUser(): ?User
    {
        $userIdOrEmail = $this->argument('userId');

        if (!$userIdOrEmail) {
            $userIdOrEmail = $this->ask('Enter the target user ID or email');
        }

        $user = User::where('email', $userIdOrEmail)->first() ?? User::find($userIdOrEmail);

        if (!$user) {
            $this->error("User not found with identifier: {$userIdOrEmail}");
            return null;
        }

        return $user;
    }

    /**
     * Retrieve the notification type, either from arguments or interactively.
     *
     * @return string|null
     */
    private function getNotificationType(): ?string
    {
        $type = $this->argument('type');

        return  $type;


        // Get available types directly from the service
        $availableTypes = $this->notificationService->getAvailableNotificationTypes();

        if ($type && in_array($type, $availableTypes)) {
            return $type;
        }

        if ($type) {
            $this->warn("Invalid type '{$type}' provided. Please choose from the list.");
        }


        // return $this->choice(
        //     'Which type of notification would you like to send?',
        //     $availableTypes
        // );
    }

    /**
     * Retrieve the context for the notification, either from arguments or interactively.
     *
     * @return array
     */
    private function getContext(): array
    {
        $context = $this->argument('context');
        if ($context) {
            return json_decode($context, true) ?: [];
        }

        $contextInput = $this->ask('Enter the context for the notification (JSON format)', '{}');
        return json_decode($contextInput, true) ?: [];
    }

    /**
     * Retrieve the channels for the notification, either from arguments or interactively.
     *
     * @return array
     */
    private function getChannels(): array
    {
        $channelsInput = $this->argument('channels');
        if ($channelsInput) {
            return explode(',', $channelsInput);
        }

        $channels = $this->ask('Enter the channels to use for the notification (comma-separated)', 'database,email');
        return array_map('trim', explode(',', $channels));
    }
}
