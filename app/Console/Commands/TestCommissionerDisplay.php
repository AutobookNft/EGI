<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestCommissionerDisplay extends Command
{
    protected $signature = 'test:commissioner-display';
    protected $description = 'Test the commissioner display functionality';

    public function handle()
    {
        $this->info('🎯 Testing Commissioner Display Functionality');
        $this->line('');

        // Crea un utente collector normale
        $collector = User::firstOrCreate(
            ['email' => 'collector.test@florenceegi.com'],
            [
                'name' => 'Test Collector',
                'first_name' => 'Test',
                'last_name' => 'Collector',
                'email' => 'collector.test@florenceegi.com',
                'password' => Hash::make('password123'),
                'wallet' => $this->generateAlgorandAddress(),
                'email_verified_at' => now(),
                'created_via' => 'test_command'
            ]
        );

        $collector->assignRole('collector');

        // Crea un utente commissioner
        $commissioner = User::firstOrCreate(
            ['email' => 'commissioner.test@florenceegi.com'],
            [
                'name' => 'Test Commissioner',
                'first_name' => 'Test',
                'last_name' => 'Commissioner',
                'email' => 'commissioner.test@florenceegi.com',
                'password' => Hash::make('password123'),
                'wallet' => $this->generateAlgorandAddress(),
                'email_verified_at' => now(),
                'created_via' => 'test_command'
            ]
        );

        $commissioner->assignRole('commissioner');

        $this->line('👥 Test Users Created:');
        $this->table(['Type', 'Name', 'Email', 'Wallet', 'Roles'], [
            [
                'Collector',
                $collector->name,
                $collector->email,
                substr($collector->wallet, 0, 10) . '...',
                $collector->getRoleNames()->join(', ')
            ],
            [
                'Commissioner',
                $commissioner->name,
                $commissioner->email,
                substr($commissioner->wallet, 0, 10) . '...',
                $commissioner->getRoleNames()->join(', ')
            ]
        ]);

        $this->line('');
        $this->info('🔍 Testing Display Functions:');

        // Test collector display
        $collectorDisplay = formatActivatorDisplay($collector);
        $this->line("Collector Display:");
        $this->line("  Name: {$collectorDisplay['name']}");
        $this->line("  Is Commissioner: " . ($collectorDisplay['is_commissioner'] ? 'YES' : 'NO'));
        $this->line("  Avatar: " . ($collectorDisplay['avatar'] ? $collectorDisplay['avatar'] : 'NULL'));
        $this->line("  Wallet Abbreviated: {$collectorDisplay['wallet_abbreviated']}");

        $this->line('');

        // Test commissioner display
        $commissionerDisplay = formatActivatorDisplay($commissioner);
        $this->line("Commissioner Display:");
        $this->line("  Name: {$commissionerDisplay['name']}");
        $this->line("  Is Commissioner: " . ($commissionerDisplay['is_commissioner'] ? 'YES' : 'NO'));
        $this->line("  Avatar: " . ($commissionerDisplay['avatar'] ? $commissionerDisplay['avatar'] : 'NULL'));
        $this->line("  Wallet Abbreviated: " . ($commissionerDisplay['wallet_abbreviated'] ?: 'NULL'));

        $this->line('');

        // Test permissions
        $this->info('🔐 Permission Check:');
        $this->line("Collector can display public name: " . 
            ($collector->can('display_public_name_on_egi') ? 'YES' : 'NO'));
        $this->line("Collector can display public avatar: " . 
            ($collector->can('display_public_avatar_on_egi') ? 'YES' : 'NO'));
        
        $this->line("Commissioner can display public name: " . 
            ($commissioner->can('display_public_name_on_egi') ? 'YES' : 'NO'));
        $this->line("Commissioner can display public avatar: " . 
            ($commissioner->can('display_public_avatar_on_egi') ? 'YES' : 'NO'));

        $this->line('');
        $this->info('✅ Test completed successfully!');
        
        return 0;
    }

    private function generateAlgorandAddress(): string
    {
        return 'TEST' . strtoupper(Str::random(54, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'));
    }
}
