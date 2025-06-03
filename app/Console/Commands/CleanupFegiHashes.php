<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * @Oracode Artisan Command: Cleanup corrupted FEGI hashes
 * 🎯 Purpose: Fix users with non-Bcrypt personal_secret hashes
 * 🧱 Core Logic: Identify and remove/fix corrupted hash data
 * 🛡️ GDPR: Safe cleanup of corrupted authentication data
 */
class CleanupFegiHashes extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fegi:cleanup-hashes {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up corrupted FEGI personal_secret hashes in the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Starting FEGI hash cleanup process...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Find users with non-null personal_secret
        $users = User::whereNotNull('personal_secret')->get();

        $this->info("Found {$users->count()} users with personal_secret values");

        $corruptedCount = 0;
        $fixedCount = 0;
        $removedCount = 0;

        foreach ($users as $user) {
            $hash = $user->personal_secret;

            // Check if it's a valid Bcrypt hash
            if (!str_starts_with($hash, '$2y$')) {
                $corruptedCount++;

                $this->warn("User ID {$user->id}: Non-Bcrypt hash detected");
                $this->line("  Hash: " . substr($hash, 0, 20) . '...');
                $this->line("  User: {$user->name} ({$user->email})");

                if (!$isDryRun) {
                    // Option 1: Remove the corrupted hash (user will need to create new account)
                    $user->update(['personal_secret' => null]);
                    $removedCount++;
                    $this->info("  → Removed corrupted hash");

                    // Option 2: If you want to try to preserve the user, you could:
                    // - Generate a new FEGI key and hash it
                    // - But this would lock the user out unless you inform them
                }
            }
        }

        $validCount = $users->count() - $corruptedCount;

        $this->info("\n=== SUMMARY ===");
        $this->info("Total users checked: {$users->count()}");
        $this->info("Valid Bcrypt hashes: {$validCount}");
        $this->info("Corrupted hashes found: {$corruptedCount}");

        if (!$isDryRun) {
            $this->info("Hashes removed: {$removedCount}");
            $this->info("Hashes fixed: {$fixedCount}");
        } else {
            $this->warn("Run without --dry-run to apply changes");
        }

        if ($corruptedCount > 0) {
            $this->warn("\nUsers with removed hashes will need to create new accounts.");
            $this->info("They can use the 'Create New Account' option in the FEGI modal.");
        }

        return Command::SUCCESS;
    }
}
