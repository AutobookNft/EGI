<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ConsentType;
use App\Models\UserConsent;
use App\Models\ConsentHistory;
use App\Models\GdprRequest;
use App\Models\DataExport;
use App\Models\UserActivityLog;
use App\Models\BreachReport;
use App\Models\PrivacyPolicy;
use App\Models\PrivacyPolicyAcceptance;
use App\Models\ProcessingRestriction;
use App\Models\DataRetentionPolicy;
use App\Enums\GdprRequestType;
use App\Enums\GdprRequestStatus;
use App\Enums\DataExportStatus;
use App\Enums\Gdpr\ConsentStatus;
use App\Models\UserActivity;
use Carbon\Carbon;

class GdprSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting GDPR data seeding...');

        // 1. Create Consent Types
        $this->command->info('Creating consent types...');
        $consentTypes = [
            [
                'slug' => 'essential_cookies',
                'name' => 'Essential Cookies',
                'description' => 'Required for the website to function properly',
                'is_required' => true,
                'category' => 'cookies',
            ],
            [
                'slug' => 'analytics_cookies',
                'name' => 'Analytics Cookies',
                'description' => 'Help us understand how visitors interact with our website',
                'is_required' => false,
                'category' => 'cookies',
            ],
            [
                'slug' => 'marketing_cookies',
                'name' => 'Marketing Cookies',
                'description' => 'Used to track visitors across websites for marketing',
                'is_required' => false,
                'category' => 'cookies',
            ],
            [
                'slug' => 'email_marketing',
                'name' => 'Email Marketing',
                'description' => 'Receive promotional emails and newsletters',
                'is_required' => false,
                'category' => 'communication',
            ],
            [
                'slug' => 'data_profiling',
                'name' => 'Data Profiling',
                'description' => 'Allow us to analyze your data for personalized services',
                'is_required' => false,
                'category' => 'processing',
            ],
        ];

        foreach ($consentTypes as $type) {
            ConsentType::create($type);
        }

        // 2. Create Privacy Policy
        $this->command->info('Creating privacy policy...');
        $privacyPolicy = PrivacyPolicy::create([
            'version' => '1.0',
            'content' => json_encode([
                'sections' => [
                    [
                        'title' => 'Introduction',
                        'content' => 'This privacy policy explains how FlorenceEGI collects, uses, and protects your personal data.'
                    ],
                    [
                        'title' => 'Data Collection',
                        'content' => 'We collect data that you provide directly to us, such as when you create an account.',
                        'subsections' => [
                            ['title' => 'Personal Information', 'content' => 'Name, email, phone number'],
                            ['title' => 'Usage Data', 'content' => 'How you interact with our platform']
                        ]
                    ],
                    [
                        'title' => 'Your Rights',
                        'content' => 'Under GDPR, you have the right to access, rectify, and delete your personal data.',
                        'list_items' => [
                            'Right to access your data',
                            'Right to rectification',
                            'Right to erasure',
                            'Right to data portability',
                            'Right to object'
                        ]
                    ]
                ]
            ]),
            'effective_date' => now()->subMonths(6),
            'change_summary' => 'Initial privacy policy version',
            'created_by' => 'legal@florenceegi.com',
        ]);

        // 3. Create Data Retention Policies
        $this->command->info('Creating data retention policies...');
        DataRetentionPolicy::create([
            'data_category' => 'user_accounts',
            'retention_period_days' => 2555, // 7 years
            'description' => 'User account data retained for 7 years after last activity',
            'legal_basis' => 'Legitimate interest and legal obligations',
            'is_active' => true,
        ]);

        DataRetentionPolicy::create([
            'data_category' => 'transaction_logs',
            'retention_period_days' => 3650, // 10 years
            'description' => 'Financial transaction logs for tax purposes',
            'legal_basis' => 'Legal obligation - tax requirements',
            'is_active' => true,
        ]);

        // 4. Create test data for existing users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Creating test users...');
            $users = User::factory(10)->create();
        }

        foreach ($users as $index => $user) {
            $this->command->info("Processing user {$user->email}...");

            // User Consents
            ConsentType::all()->each(function ($consentType) use ($user) {
                // Skip if already exists
                if ($user->userConsents()->where('consent_type_id', $consentType->id)->exists()) {
                    return;
                }

                // 80% chance of having consent for non-required types
                if ($consentType->is_required || rand(1, 100) <= 80) {
                    $consent = UserConsent::create([
                        'user_id' => $user->id,
                        'consent_type_id' => $consentType->id,
                        'status' => ConsentStatus::ACTIVE->value,
                        'ip_address' => fake()->ipv4(),
                        'user_agent' => fake()->userAgent(),
                        'granted_at' => now()->subDays(rand(30, 365)),
                    ]);

                    // Create consent history
                    ConsentHistory::create([
                        'user_consent_id' => $consent->id,
                        'user_id' => $user->id,
                        'action' => 'granted',
                        'previous_status' => null,
                        'new_status' => ConsentStatus::ACTIVE->value,
                        'ip_address' => $consent->ip_address,
                        'user_agent' => $consent->user_agent,
                    ]);
                }
            });

            // GDPR Requests (30% chance)
            if (rand(1, 100) <= 30) {
                GdprRequest::factory()
                    ->count(rand(1, 3))
                    ->create(['user_id' => $user->id]);
            }

            // Data Exports (20% chance)
            if (rand(1, 100) <= 20) {
                DataExport::factory()
                    ->count(rand(1, 2))
                    ->create(['user_id' => $user->id]);
            }

            // Activity Logs
            UserActivity::factory()
                ->count(rand(5, 20))
                ->create(['user_id' => $user->id]);

            // Privacy Policy Acceptance (90% chance)
            if (rand(1, 100) <= 90) {
                PrivacyPolicyAcceptance::create([
                    'user_id' => $user->id,
                    'privacy_policy_id' => $privacyPolicy->id,
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                ]);
            }

            // Processing Restrictions (30% chance)
            if (rand(1, 100) <= 30) {
                ProcessingRestriction::factory()
                    ->count(rand(1, 3))
                    ->create(['user_id' => $user->id]);

                // 20% chance of having a lifted restriction
                if (rand(1, 100) <= 20) {
                    ProcessingRestriction::factory()
                        ->lifted()
                        ->create(['user_id' => $user->id]);
                }
            }

            // Breach Reports (5% chance - rare)
            if (rand(1, 100) <= 5) {
                BreachReport::factory()->create(['user_id' => $user->id]);
            }
        }

        $this->command->info('GDPR seeding completed successfully!');
    }
}
