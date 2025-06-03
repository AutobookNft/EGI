<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class DebugAuthGuards extends Command
{
    protected $signature = 'debug:auth-guards';
    protected $description = 'Debug available auth guards and drivers';

    public function handle()
    {
        $this->info('=== AUTH CONFIGURATION DEBUG ===');

        // Show default guard
        $defaultGuard = config('auth.defaults.guard');
        $this->info("Default guard: {$defaultGuard}");

        // Show all configured guards
        $guards = config('auth.guards');
        $this->info('Configured guards:');
        foreach ($guards as $name => $config) {
            $this->line("  - {$name}: driver={$config['driver']}, provider={$config['provider']}");
        }

        // Test guard resolution
        try {
            $fegiGuard = Auth::guard('fegi');
            $this->info('✅ FEGI guard resolved successfully');
            $this->info('FEGI guard class: ' . get_class($fegiGuard));
        } catch (\Exception $e) {
            $this->error('❌ FEGI guard resolution failed: ' . $e->getMessage());
        }

        // Test default guard
        try {
            $defaultGuardInstance = Auth::guard();
            $this->info('✅ Default guard resolved successfully');
            $this->info('Default guard class: ' . get_class($defaultGuardInstance));
        } catch (\Exception $e) {
            $this->error('❌ Default guard resolution failed: ' . $e->getMessage());
        }

        return 0;
    }
}
