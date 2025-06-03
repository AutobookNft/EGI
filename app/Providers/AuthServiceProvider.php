<?php

namespace App\Providers;

use App\Auth\Guards\FegiGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Log;

/**
 * @Oracode Service Provider: Extended Auth with FEGI Guard
 * 🎯 Purpose: Register custom FEGI auth guard in Laravel
 * 🧱 Core Logic: Extend Laravel's authentication system
 *
 * @package App\Providers
 * @author Padmin D. Curtis
 * @version 1.1.0 (Fixed Registration Order)
 * @date 2025-05-29
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();


        // Debug: Log che il driver è stato registrato
        // Log::info('FegiGuard driver registered successfully');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // CRITICAL: Register FEGI auth driver in register() method
        // This ensures it's available BEFORE other service providers boot
        $this->app->extend('auth', function ($auth, $app) {
            $auth->extend('fegi', function ($app, $name, array $config) {
                // Create user provider
                $userProvider = Auth::createUserProvider($config['provider'] ?? 'users');

                // Return new FegiGuard instance with FIXED constructor
                return new FegiGuard(
                    $userProvider,
                    $app['request']  // REMOVED session injection - uses Laravel session() helper
                );
            });

            return $auth;
        });


    }
}
