<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class DebugFegiSession extends Command
{
    protected $signature = 'debug:fegi-session';
    protected $description = 'Debug FEGI session and authentication state';

    public function handle()
    {
        $this->info('=== FEGI SESSION DEBUG ===');

        // Check session data
        $sessionData = [
            'auth_status' => session('auth_status'),
            'connected_wallet' => session('connected_wallet'),
            'connected_user_id' => session('connected_user_id'),
            'is_weak_auth' => session('is_weak_auth'),
        ];

        $this->info('Session Data:');
        foreach ($sessionData as $key => $value) {
            $this->line("  {$key}: " . ($value ?? 'NULL'));
        }

        // Check Auth facade
        $this->info('Auth Facade:');
        $this->line('  Auth::check(): ' . (Auth::check() ? 'true' : 'false'));
        $this->line('  Auth::guest(): ' . (Auth::guest() ? 'true' : 'false'));
        $this->line('  Auth::id(): ' . (Auth::id() ?? 'NULL'));
        $this->line('  Auth::user(): ' . (Auth::user() ? Auth::user()->name : 'NULL'));

        // Check FEGI guard specifically
        $fegiGuard = Auth::guard('fegi');
        $this->info('FEGI Guard:');
        $this->line('  fegi->check(): ' . ($fegiGuard->check() ? 'true' : 'false'));
        $this->line('  fegi->user(): ' . ($fegiGuard->user() ? $fegiGuard->user()->name : 'NULL'));

        if (method_exists($fegiGuard, 'isWeakAuth')) {
            $this->line('  fegi->isWeakAuth(): ' . ($fegiGuard->isWeakAuth() ? 'true' : 'false'));
        }

        if (method_exists($fegiGuard, 'getAuthType')) {
            $this->line('  fegi->getAuthType(): ' . $fegiGuard->getAuthType());
        }

        // Check web guard
        $webGuard = Auth::guard('web');
        $this->info('Web Guard:');
        $this->line('  web->check(): ' . ($webGuard->check() ? 'true' : 'false'));
        $this->line('  web->user(): ' . ($webGuard->user() ? $webGuard->user()->name : 'NULL'));

        return 0;
    }
}
