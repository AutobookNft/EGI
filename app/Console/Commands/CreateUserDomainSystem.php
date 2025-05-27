<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @Oracode Command: Create User Domain System
 * 🎯 Purpose: Generate complete user domain separation architecture
 * 🛡️ Privacy: Creates GDPR-compliant data structure automatically
 * 🧱 Core Logic: Orchestrates creation of migrations, models, and traits
 *
 * @oracode-dimension technical
 * @value-flow Automates complex architectural setup for user data management
 * @community-impact Enables rapid deployment of privacy-compliant user system
 * @transparency-level High - clear feedback on each component creation
 * @sustainability-factor High - generates maintainable, structured codebase
 * @narrative-coherence Embodies FlorenceEGI's commitment to automated excellence
 */
class CreateUserDomainSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'florence:create-user-domain
                            {--migrate : Run migrations immediately after creation}
                            {--force : Overwrite existing files}
                            {--dry-run : Show what would be created without actually creating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create complete User Domain System: migrations, models, traits, and relationships for GDPR-compliant user data management';

    /**
     * Domain components to create
     */
    protected array $domainComponents = [
        'migrations' => [
            'create_user_domain_tables',
            'populate_user_domain_tables'
        ],
        'models' => [
            'UserProfile',
            'UserPersonalData',
            'UserOrganizationData',
            'UserDocuments',
            'UserInvoicePreferences'
        ],
        'traits' => [
            'HasGdprData',
            'HasSellerCapabilities',
            'HasWalletManagement',
            'HasCollectionAccess'
        ]
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 FlorenceEGI User Domain System Creator');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No files will be created');
        }

        // Step 1: Create Migrations
        $this->createMigrations($dryRun, $force);

        // Step 2: Create Models
        $this->createModels($dryRun, $force);

        // Step 3: Create Traits
        $this->createTraits($dryRun, $force);

        // Step 4: Update User Model
        $this->updateUserModel($dryRun, $force);

        // Step 5: Run Migrations (if requested)
        if ($this->option('migrate') && !$dryRun) {
            $this->runMigrations();
        }

        $this->displaySummary($dryRun);

        return Command::SUCCESS;
    }

    /**
     * Create migration files
     */
    protected function createMigrations(bool $dryRun, bool $force): void
    {
        $this->info("\n📋 Creating Migrations...");

        $migrations = [
            'create_user_domain_tables' => $this->getMigrationContent('create'),
            'populate_user_domain_tables' => $this->getMigrationContent('populate')
        ];

        foreach ($migrations as $name => $content) {
            $timestamp = now()->addSecond()->format('Y_m_d_His');
            $filename = "{$timestamp}_{$name}.php";
            $path = database_path("migrations/{$filename}");

            if ($dryRun) {
                $this->line("   📄 Would create: {$filename}");
                continue;
            }

            if (File::exists($path) && !$force) {
                $this->warn("   ⚠️  Migration exists: {$filename} (use --force to overwrite)");
                continue;
            }

            File::put($path, $content);
            $this->info("   ✅ Created: {$filename}");
        }
    }

    /**
     * Create model files
     */
    protected function createModels(bool $dryRun, bool $force): void
    {
        $this->info("\n🏗️  Creating Models...");

        foreach ($this->domainComponents['models'] as $modelName) {
            $path = app_path("Models/{$modelName}.php");
            $content = $this->getModelContent($modelName);

            if ($dryRun) {
                $this->line("   📄 Would create: Models/{$modelName}.php");
                continue;
            }

            if (File::exists($path) && !$force) {
                $this->warn("   ⚠️  Model exists: {$modelName} (use --force to overwrite)");
                continue;
            }

            File::put($path, $content);
            $this->info("   ✅ Created: Models/{$modelName}.php");
        }
    }

    /**
     * Create trait files
     */
    protected function createTraits(bool $dryRun, bool $force): void
    {
        $this->info("\n🔧 Creating Traits...");

        // Ensure Traits directory exists
        $traitsDir = app_path('Models/Traits');
        if (!$dryRun && !File::exists($traitsDir)) {
            File::makeDirectory($traitsDir, 0755, true);
        }

        foreach ($this->domainComponents['traits'] as $traitName) {
            $path = app_path("Models/Traits/{$traitName}.php");
            $content = $this->getTraitContent($traitName);

            if ($dryRun) {
                $this->line("   📄 Would create: Models/Traits/{$traitName}.php");
                continue;
            }

            if (File::exists($path) && !$force) {
                $this->warn("   ⚠️  Trait exists: {$traitName} (use --force to overwrite)");
                continue;
            }

            File::put($path, $content);
            $this->info("   ✅ Created: Models/Traits/{$traitName}.php");
        }
    }

    /**
     * Update User model with traits
     */
    protected function updateUserModel(bool $dryRun, bool $force): void
    {
        $this->info("\n👤 Updating User Model...");

        $userModelPath = app_path('Models/User.php');

        if ($dryRun) {
            $this->line("   📄 Would update: Models/User.php (add traits and relationships)");
            return;
        }

        if (!File::exists($userModelPath)) {
            $this->error("   ❌ User model not found at: {$userModelPath}");
            return;
        }

        // Create backup
        $backupPath = $userModelPath . '.backup.' . time();
        File::copy($userModelPath, $backupPath);
        $this->info("   💾 Backup created: User.php.backup." . time());

        $this->info("   ✅ User model backup created (manual update recommended)");
        $this->warn("   ⚠️  Please manually add traits to User model:");
        $this->line("      use HasGdprData, HasSellerCapabilities, HasWalletManagement, HasCollectionAccess;");
    }

    /**
     * Run migrations
     */
    protected function runMigrations(): void
    {
        $this->info("\n🔄 Running Migrations...");

        try {
            Artisan::call('migrate', [], $this->getOutput());
            $this->info("   ✅ Migrations completed successfully");
        } catch (\Exception $e) {
            $this->error("   ❌ Migration failed: " . $e->getMessage());
        }
    }

    /**
     * Display creation summary
     */
    protected function displaySummary(bool $dryRun): void
    {
        $this->info("\n📊 CREATION SUMMARY");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $mode = $dryRun ? 'DRY RUN' : 'CREATED';

        $this->line("📋 Migrations: 2 files {$mode}");
        $this->line("🏗️  Models: " . count($this->domainComponents['models']) . " files {$mode}");
        $this->line("🔧 Traits: " . count($this->domainComponents['traits']) . " files {$mode}");
        $this->line("👤 User Model: Backup created (manual update needed)");

        if (!$dryRun) {
            $this->info("\n🎯 NEXT STEPS:");
            $this->line("1. Review generated files");
            $this->line("2. Update User model with traits");
            $this->line("3. Run: php artisan florence:create-user-domain --migrate");
            $this->line("4. Test the new structure");
        }

        $this->info("\n✨ FlorenceEGI User Domain System ready!");
    }

    /**
     * Get migration content
     */
    protected function getMigrationContent(string $type): string
    {
        switch ($type) {
            case 'create':
                return $this->getCreateMigrationContent();
            case 'populate':
                return $this->getPopulateMigrationContent();
            default:
                return '';
        }
    }

    /**
     * Get create migration content
     */
    protected function getCreateMigrationContent(): string
    {
        return <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Create User Domain Separation Tables (Generated)
 * 🎯 Purpose: Split User model into focused domain tables with personal/org separation
 * 🛡️ Privacy: Enables granular GDPR compliance with separate personal/business data
 * 🧱 Core Logic: Maintains referential integrity while optimizing data categorization
 */
return new class extends Migration
{
    public function up(): void
    {
        // user_profiles table
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('job_role')->nullable();
            $table->string('site_url')->nullable();
            $table->string('facebook')->nullable();
            $table->string('social_x')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('instagram')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('twitch')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('discord')->nullable();
            $table->string('telegram')->nullable();
            $table->string('other')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->text('annotation')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        // user_personal_data table
        Schema::create('user_personal_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('work_phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('fiscal_code')->nullable();
            $table->string('tax_id_number')->nullable();
            $table->boolean('allow_personal_data_processing')->default(false);
            $table->json('processing_purposes')->nullable();
            $table->timestamp('consent_updated_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
            $table->index('fiscal_code');
        });

        // user_organization_data table
        Schema::create('user_organization_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('org_name')->nullable();
            $table->string('org_email')->nullable();
            $table->string('org_street')->nullable();
            $table->string('org_city')->nullable();
            $table->string('org_region')->nullable();
            $table->string('org_state')->nullable();
            $table->string('org_zip')->nullable();
            $table->string('org_site_url')->nullable();
            $table->string('org_phone_1')->nullable();
            $table->string('org_phone_2')->nullable();
            $table->string('org_phone_3')->nullable();
            $table->string('rea')->nullable();
            $table->string('org_fiscal_code')->nullable();
            $table->string('org_vat_number')->nullable();
            $table->boolean('is_seller_verified')->default(false);
            $table->boolean('can_issue_invoices')->default(false);
            $table->enum('business_type', ['individual', 'sole_proprietorship', 'partnership', 'corporation', 'non_profit', 'other'])->nullable();
            $table->timestamps();
            $table->unique('user_id');
            $table->index('org_vat_number');
        });

        // user_documents table
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('doc_typo')->nullable();
            $table->string('doc_num')->nullable();
            $table->date('doc_issue_date')->nullable();
            $table->date('doc_expired_date')->nullable();
            $table->string('doc_issue_from')->nullable();
            $table->string('doc_photo_path_f')->nullable();
            $table->string('doc_photo_path_r')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->boolean('is_encrypted')->default(true);
            $table->timestamps();
            $table->unique('user_id');
        });

        // user_invoice_preferences table
        Schema::create('user_invoice_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_name')->nullable();
            $table->string('invoice_fiscal_code')->nullable();
            $table->string('invoice_vat_number')->nullable();
            $table->string('invoice_address')->nullable();
            $table->string('invoice_city')->nullable();
            $table->string('invoice_country', 2)->nullable();
            $table->boolean('can_issue_invoices')->default(false);
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_invoice_preferences');
        Schema::dropIfExists('user_documents');
        Schema::dropIfExists('user_organization_data');
        Schema::dropIfExists('user_personal_data');
        Schema::dropIfExists('user_profiles');
    }
};
PHP;
    }

    /**
     * Get populate migration content
     */
    protected function getPopulateMigrationContent(): string
    {
        return <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Migration: Populate User Domain Tables (Generated)
 * 🎯 Purpose: Migrate existing user data to optimized domain-specific tables
 * 🛡️ Privacy: Preserves all data while improving GDPR compliance structure
 */
return new class extends Migration
{
    public function up(): void
    {
        // Create backup
        $backupTableName = 'users_backup_' . date('Y_m_d_H_i_s');
        DB::statement("CREATE TABLE {$backupTableName} AS SELECT * FROM users");

        // Populate user_profiles
        DB::statement("
            INSERT INTO user_profiles (user_id, title, job_role, site_url, facebook, social_x, tiktok, instagram, snapchat, twitch, linkedin, discord, telegram, other, annotation, created_at, updated_at)
            SELECT id, title, job_role, site_url, facebook, social_x, tiktok, instagram, snapchat, twitch, linkedin, discord, telegram, other, annotation, created_at, updated_at
            FROM users
            WHERE title IS NOT NULL OR job_role IS NOT NULL OR site_url IS NOT NULL OR facebook IS NOT NULL OR social_x IS NOT NULL OR tiktok IS NOT NULL OR instagram IS NOT NULL OR snapchat IS NOT NULL OR twitch IS NOT NULL OR linkedin IS NOT NULL OR discord IS NOT NULL OR telegram IS NOT NULL OR other IS NOT NULL OR annotation IS NOT NULL
        ");

        // Populate user_personal_data
        DB::statement("
            INSERT INTO user_personal_data (user_id, street, city, region, state, zip, home_phone, cell_phone, work_phone, birth_date, fiscal_code, tax_id_number, allow_personal_data_processing, consent_updated_at, created_at, updated_at)
            SELECT id, street, city, region, state, zip, home_phone, cell_phone, work_phone, birth_date, fiscal_code, tax_id_number, COALESCE(consent, false), CASE WHEN consent IS NOT NULL THEN updated_at ELSE NULL END, created_at, updated_at
            FROM users
            WHERE street IS NOT NULL OR city IS NOT NULL OR region IS NOT NULL OR state IS NOT NULL OR zip IS NOT NULL OR home_phone IS NOT NULL OR cell_phone IS NOT NULL OR work_phone IS NOT NULL OR birth_date IS NOT NULL OR fiscal_code IS NOT NULL OR tax_id_number IS NOT NULL
        ");

        // Populate user_organization_data
        DB::statement("
            INSERT INTO user_organization_data (user_id, org_name, org_email, org_street, org_city, org_region, org_state, org_zip, org_site_url, org_phone_1, org_phone_2, org_phone_3, rea, org_fiscal_code, org_vat_number, is_seller_verified, can_issue_invoices, business_type, created_at, updated_at)
            SELECT id, org_name, org_email, org_street, org_city, org_region, org_state, org_zip, org_site_url, org_phone_1, org_phone_2, org_phone_3, rea, org_fiscal_code, org_vat_number,
            CASE WHEN usertype IN ('creator', 'azienda', 'epp_entity') AND org_name IS NOT NULL AND (org_fiscal_code IS NOT NULL OR org_vat_number IS NOT NULL) THEN true ELSE false END,
            CASE WHEN usertype IN ('creator', 'azienda', 'epp_entity') AND org_vat_number IS NOT NULL THEN true ELSE false END,
            CASE WHEN usertype = 'creator' THEN 'individual' WHEN usertype = 'azienda' THEN 'corporation' WHEN usertype = 'epp_entity' THEN 'non_profit' ELSE 'other' END,
            created_at, updated_at
            FROM users
            WHERE org_name IS NOT NULL OR org_email IS NOT NULL OR org_street IS NOT NULL OR org_city IS NOT NULL OR org_region IS NOT NULL OR org_state IS NOT NULL OR org_zip IS NOT NULL OR org_site_url IS NOT NULL OR org_phone_1 IS NOT NULL OR org_phone_2 IS NOT NULL OR org_phone_3 IS NOT NULL OR rea IS NOT NULL OR org_fiscal_code IS NOT NULL OR org_vat_number IS NOT NULL
        ");

        // Populate user_documents
        DB::statement("
            INSERT INTO user_documents (user_id, doc_typo, doc_num, doc_issue_date, doc_expired_date, doc_issue_from, doc_photo_path_f, doc_photo_path_r, verification_status, is_encrypted, created_at, updated_at)
            SELECT id, doc_typo, doc_num, doc_issue_date, doc_expired_date, doc_issue_from, doc_photo_path_f, doc_photo_path_r,
            CASE WHEN doc_num IS NOT NULL AND doc_expired_date > NOW() THEN 'verified' WHEN doc_num IS NOT NULL AND doc_expired_date <= NOW() THEN 'expired' WHEN doc_num IS NOT NULL THEN 'pending' ELSE 'pending' END,
            true, created_at, updated_at
            FROM users
            WHERE doc_typo IS NOT NULL OR doc_num IS NOT NULL OR doc_issue_date IS NOT NULL OR doc_expired_date IS NOT NULL OR doc_issue_from IS NOT NULL OR doc_photo_path_f IS NOT NULL OR doc_photo_path_r IS NOT NULL
        ");

        // Populate user_invoice_preferences
        DB::statement("
            INSERT INTO user_invoice_preferences (user_id, can_issue_invoices, created_at, updated_at)
            SELECT id, CASE WHEN usertype IN ('creator', 'azienda', 'epp_entity') THEN true ELSE false END, created_at, updated_at
            FROM users
        ");
    }

    public function down(): void
    {
        DB::table('user_invoice_preferences')->truncate();
        DB::table('user_documents')->truncate();
        DB::table('user_organization_data')->truncate();
        DB::table('user_personal_data')->truncate();
        DB::table('user_profiles')->truncate();
    }
};
PHP;
    }

    /**
     * Get model content
     */
    protected function getModelContent(string $modelName): string
    {
        $tableName = Str::snake(Str::pluralStudly($modelName));

        switch ($modelName) {
            case 'UserProfile':
                return $this->getUserProfileModelContent();
            case 'UserPersonalData':
                return $this->getUserPersonalDataModelContent();
            case 'UserOrganizationData':
                return $this->getUserOrganizationDataModelContent();
            case 'UserDocuments':
                return $this->getUserDocumentsModelContent();
            case 'UserInvoicePreferences':
                return $this->getUserInvoicePreferencesModelContent();
            default:
                return $this->getGenericModelContent($modelName, $tableName);
        }
    }

    /**
     * Get UserProfile model content
     */
    protected function getUserProfileModelContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Profile Data
 * 🎯 Purpose: Manages public profile information and social links
 * 🛡️ Privacy: Contains non-sensitive, publicly shareable user data
 * 🧱 Core Logic: Handles social media integration and professional identity
 */
class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'title', 'job_role', 'site_url', 'facebook', 'social_x',
        'tiktok', 'instagram', 'snapchat', 'twitch', 'linkedin', 'discord',
        'telegram', 'other', 'profile_photo_path', 'annotation'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSocialLinksAttribute(): array
    {
        return array_filter([
            'website' => $this->site_url,
            'facebook' => $this->facebook,
            'twitter' => $this->social_x,
            'tiktok' => $this->tiktok,
            'instagram' => $this->instagram,
            'snapchat' => $this->snapchat,
            'twitch' => $this->twitch,
            'linkedin' => $this->linkedin,
            'discord' => $this->discord,
            'telegram' => $this->telegram,
            'other' => $this->other
        ]);
    }

    public function hasCompletedProfile(): bool
    {
        return !empty($this->job_role) || !empty($this->annotation) || count($this->social_links) > 0;
    }
}
PHP;
    }

    /**
     * Get UserPersonalData model content
     */
    protected function getUserPersonalDataModelContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Personal Data (GDPR Sensitive)
 * 🎯 Purpose: Manages GDPR-sensitive personal identification data
 * 🛡️ Privacy: Ultra-sensitive data with strict access controls
 * 🧱 Core Logic: Handles personal identity and compliance tracking
 */
class UserPersonalData extends Model
{
    protected $table = 'user_personal_data';

    protected $fillable = [
        'user_id', 'street', 'city', 'region', 'state', 'zip',
        'home_phone', 'cell_phone', 'work_phone', 'birth_date',
        'fiscal_code', 'tax_id_number', 'allow_personal_data_processing',
        'processing_purposes', 'consent_updated_at'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'allow_personal_data_processing' => 'boolean',
        'processing_purposes' => 'array',
        'consent_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'fiscal_code', 'tax_id_number', 'birth_date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): ?string
    {
        $parts = array_filter([$this->street, $this->city, $this->region, $this->state, $this->zip]);
        return empty($parts) ? null : implode(', ', $parts);
    }

    public function hasCompleteAddress(): bool
    {
        return !empty($this->street) && !empty($this->city) && !empty($this->zip);
    }

    public function isDataProcessingAllowed(string $purpose = null): bool
    {
        if (!$this->allow_personal_data_processing) {
            return false;
        }

        if ($purpose && $this->processing_purposes) {
            return in_array($purpose, $this->processing_purposes);
        }

        return true;
    }
}
PHP;
    }

    /**
     * Get UserOrganizationData model content
     */
    protected function getUserOrganizationDataModelContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Organization Data
 * 🎯 Purpose: Manages business and organizational information
 * 🛡️ Privacy: Business data with moderate sensitivity
 * 🧱 Core Logic: Handles seller verification and business compliance
 */
class UserOrganizationData extends Model
{
    protected $table = 'user_organization_data';

    protected $fillable = [
        'user_id', 'org_name', 'org_email', 'org_street', 'org_city',
        'org_region', 'org_state', 'org_zip', 'org_site_url',
        'org_phone_1', 'org_phone_2', 'org_phone_3', 'rea',
        'org_fiscal_code', 'org_vat_number', 'is_seller_verified',
        'can_issue_invoices', 'business_type'
    ];

    protected $casts = [
        'is_seller_verified' => 'boolean',
        'can_issue_invoices' => 'boolean',
        'vat_registered' => 'boolean',
        'requires_compliance_review' => 'boolean',
        'business_categories' => 'array',
        'seller_verified_at' => 'datetime',
        'compliance_checked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullOrganizationAddressAttribute(): ?string
    {
        $parts = array_filter([
            $this->org_street, $this->org_city,
            $this->org_region, $this->org_state, $this->org_zip
        ]);
        return empty($parts) ? null : implode(', ', $parts);
    }

    public function hasCompleteSellerData(): bool
    {
        return !empty($this->org_name) &&
               (!empty($this->org_fiscal_code) || !empty($this->org_vat_number)) &&
               $this->hasCompleteAddress();
    }

    public function hasCompleteAddress(): bool
    {
        return !empty($this->org_street) && !empty($this->org_city) && !empty($this->org_zip);
    }

    public function getMissingSellerDataFields(): array
    {
        $missing = [];

        if (empty($this->org_name)) $missing[] = 'org_name';
        if (empty($this->org_fiscal_code) && empty($this->org_vat_number)) {
            $missing[] = 'org_fiscal_code_or_vat';
        }
        if (empty($this->org_street)) $missing[] = 'org_street';
        if (empty($this->org_city)) $missing[] = 'org_city';
        if (empty($this->org_zip)) $missing[] = 'org_zip';

        return $missing;
    }
}
PHP;
    }

    /**
     * Get UserDocuments model content
     */
    protected function getUserDocumentsModelContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Documents (GDPR Ultra-Sensitive)
 * 🎯 Purpose: Manages encrypted document storage and verification
 * 🛡️ Privacy: Ultra-sensitive documents with encryption and access tracking
 * 🧱 Core Logic: Handles document verification and compliance retention
 */
class UserDocuments extends Model
{
    protected $fillable = [
        'user_id', 'doc_typo', 'doc_num', 'doc_issue_date', 'doc_expired_date',
        'doc_issue_from', 'doc_photo_path_f', 'doc_photo_path_r',
        'verification_status', 'verification_notes', 'verified_at', 'verified_by',
        'is_encrypted', 'document_purpose', 'retention_until'
    ];

    protected $casts = [
        'doc_issue_date' => 'date',
        'doc_expired_date' => 'date',
        'verified_at' => 'datetime',
        'is_encrypted' => 'boolean',
        'retention_until' => 'date',
        'scheduled_for_deletion' => 'boolean',
        'last_accessed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'doc_num', 'doc_photo_path_f', 'doc_photo_path_r'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->doc_expired_date && $this->doc_expired_date->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified' && !$this->isExpired();
    }

    public function needsRenewal(): bool
    {
        if (!$this->doc_expired_date) return false;

        return $this->doc_expired_date->diffInDays(now()) <= 30;
    }

    public function canBeDeleted(): bool
    {
        return $this->retention_until && $this->retention_until->isPast();
    }

    public function trackAccess(?string $accessedBy = null): void
    {
        $this->increment('access_count');
        $this->update([
            'last_accessed_at' => now(),
            'last_accessed_by' => $accessedBy
        ]);
    }
}
PHP;
    }

    /**
     * Get UserInvoicePreferences model content
     */
    protected function getUserInvoicePreferencesModelContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Invoice Preferences
 * 🎯 Purpose: Manages invoice generation and billing preferences
 * 🛡️ Privacy: Business invoice data with moderate sensitivity
 * 🧱 Core Logic: Handles buyer/seller invoice capabilities and settings
 */
class UserInvoicePreferences extends Model
{
    protected $fillable = [
        'user_id', 'invoice_name', 'invoice_fiscal_code', 'invoice_vat_number',
        'invoice_address', 'invoice_city', 'invoice_state', 'invoice_postal_code',
        'invoice_country', 'auto_request_invoice', 'preferred_invoice_format',
        'invoice_email', 'require_invoice_for_purchases', 'can_issue_invoices',
        'invoice_template_id', 'invoice_settings', 'invoice_series_prefix',
        'last_invoice_number', 'electronic_invoicing_enabled', 'tax_settings'
    ];

    protected $casts = [
        'auto_request_invoice' => 'boolean',
        'require_invoice_for_purchases' => 'boolean',
        'can_issue_invoices' => 'boolean',
        'electronic_invoicing_enabled' => 'boolean',
        'invoice_settings' => 'array',
        'tax_settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasCompleteInvoiceData(): bool
    {
        return !empty($this->invoice_name) &&
               !empty($this->invoice_fiscal_code) &&
               !empty($this->invoice_address) &&
               !empty($this->invoice_city);
    }

    public function canReceiveInvoices(): bool
    {
        return $this->hasCompleteInvoiceData();
    }

    public function getNextInvoiceNumber(): int
    {
        return $this->last_invoice_number + 1;
    }

    public function generateInvoiceCode(): string
    {
        $prefix = $this->invoice_series_prefix ?? 'INV';
        $number = $this->getNextInvoiceNumber();
        $year = date('Y');

        return sprintf('%s-%s-%04d', $prefix, $year, $number);
    }

    public function updateLastInvoiceNumber(): void
    {
        $this->increment('last_invoice_number');
    }
}
PHP;
    }

    /**
     * Get trait content
     */
    protected function getTraitContent(string $traitName): string
    {
        switch ($traitName) {
            case 'HasGdprData':
                return $this->getHasGdprDataTraitContent();
            case 'HasSellerCapabilities':
                return $this->getHasSellerCapabilitiesTraitContent();
            case 'HasWalletManagement':
                return $this->getHasWalletManagementTraitContent();
            case 'HasCollectionAccess':
                return $this->getHasCollectionAccessTraitContent();
            default:
                return $this->getGenericTraitContent($traitName);
        }
    }

    /**
     * Get HasGdprData trait content
     */
    protected function getHasGdprDataTraitContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\DB;

/**
 * @Oracode Trait: GDPR Data Management
 * 🎯 Purpose: Provides comprehensive GDPR data handling capabilities
 * 🛡️ Privacy: Implements data export, anonymization, and consent management
 * 🧱 Core Logic: Centralizes GDPR compliance operations across user domains
 */
trait HasGdprData
{
    /**
     * Get comprehensive GDPR export data
     */
    public function getGdprExportData(): array
    {
        return [
            'export_info' => [
                'generated_at' => now()->toISOString(),
                'user_id' => $this->id,
                'user_type' => $this->usertype,
                'platform' => 'FlorenceEGI',
                'version' => '2.0'
            ],
            'core_data' => $this->only([
                'id', 'name', 'last_name', 'email', 'username', 'usertype',
                'created_at', 'updated_at'
            ]),
            'profile_data' => $this->profile?->toArray(),
            'personal_data' => $this->personalData?->makeVisible(['fiscal_code', 'tax_id_number'])->toArray(),
            'organization_data' => $this->organizationData?->toArray(),
            'documents_info' => $this->documents?->only([
                'doc_typo', 'doc_issue_date', 'doc_expired_date', 'verification_status'
            ]),
            'invoice_preferences' => $this->invoicePreferences?->toArray(),
            'consents' => $this->consents()->orderBy('created_at', 'desc')->get(),
            'gdpr_activities' => $this->gdprActivities()->orderBy('created_at', 'desc')->limit(1000)->get()
        ];
    }

    /**
     * Anonymize user data for GDPR deletion
     */
    public function anonymizeForGdprDeletion(): bool
    {
        return DB::transaction(function () {
            try {
                // Anonymize core user data
                $this->update([
                    'name' => 'Deleted User ' . $this->id,
                    'last_name' => '',
                    'email' => 'deleted_' . $this->id . '@deleted.florence-egi.local',
                    'username' => 'deleted_' . $this->id
                ]);

                // Delete profile data
                $this->profile()?->delete();

                // Delete personal data (GDPR sensitive)
                $this->personalData()?->delete();

                // Anonymize organization data (keep for business records)
                $this->organizationData()?->update([
                    'org_name' => 'Deleted Organization ' . $this->id,
                    'org_email' => null,
                    'org_site_url' => null
                ]);

                // Delete documents
                $this->documents()?->delete();

                // Clear invoice preferences
                $this->invoicePreferences()?->update([
                    'invoice_name' => null,
                    'invoice_address' => null,
                    'invoice_email' => null
                ]);

                return true;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Get user's data organized by GDPR categories
     */
    public function getDataByGdprCategory(array $categories = []): array
    {
        $allCategories = [
            'core_identity' => fn() => $this->only(['name', 'email', 'usertype']),
            'profile_data' => fn() => $this->profile?->toArray() ?? [],
            'personal_data' => fn() => $this->personalData?->toArray() ?? [],
            'organization_data' => fn() => $this->organizationData?->toArray() ?? [],
            'documents' => fn() => $this->documents?->only(['doc_typo', 'verification_status']) ?? [],
            'invoice_preferences' => fn() => $this->invoicePreferences?->toArray() ?? []
        ];

        $categoriesToProcess = empty($categories) ? array_keys($allCategories) : $categories;
        $result = [];

        foreach ($categoriesToProcess as $category) {
            if (isset($allCategories[$category])) {
                $data = $allCategories[$category]();
                if (!empty($data)) {
                    $result[$category] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * Check processing restrictions for specific data category
     */
    public function hasProcessingRestriction(string $category): bool
    {
        return $this->processingRestrictions()
            ->where('restriction_type', $category)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get privacy compliance score (0-100)
     */
    public function getPrivacyComplianceScore(): int
    {
        $score = 100;

        // Check required consents
        $requiredConsents = ['privacy_policy_accepted', 'terms_accepted'];
        foreach ($requiredConsents as $consent) {
            if (!$this->hasActiveConsentFor($consent)) {
                $score -= 20;
            }
        }

        // Check data completeness based on user type
        if ($this->canActAsSeller() && !$this->organizationData?->hasCompleteSellerData()) {
            $score -= 30;
        }

        // Check for expired documents
        if ($this->documents?->isExpired()) {
            $score -= 10;
        }

        // Check for pending GDPR requests
        $pendingRequests = $this->gdprRequests()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        $score -= min($pendingRequests * 10, 20);

        return max(0, $score);
    }
}
PHP;
    }

    /**
     * Get HasSellerCapabilities trait content
     */
    protected function getHasSellerCapabilitiesTraitContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Models\Traits;

/**
 * @Oracode Trait: Seller Capabilities Management
 * 🎯 Purpose: Manages seller verification and business capabilities
 * 🛡️ Privacy: Handles business data with appropriate access controls
 * 🧱 Core Logic: Centralizes seller-specific business logic
 */
trait HasSellerCapabilities
{
    /**
     * Check if user can act as seller
     */
    public function canActAsSeller(): bool
    {
        if (!in_array($this->usertype, ['creator', 'azienda', 'epp_entity'])) {
            return false;
        }

        return $this->organizationData?->hasCompleteSellerData() ?? false;
    }

    /**
     * Get missing seller compliance data
     */
    public function getMissingSellerData(): array
    {
        if (!in_array($this->usertype, ['creator', 'azienda', 'epp_entity'])) {
            return [];
        }

        return $this->organizationData?->getMissingSellerDataFields() ?? [];
    }

    /**
     * Check if seller is verified
     */
    public function isVerifiedSeller(): bool
    {
        return $this->organizationData?->is_seller_verified ?? false;
    }

    /**
     * Get seller information for display/invoicing
     */
    public function getSellerInfo(): array
    {
        $orgData = $this->organizationData;

        return [
            'legal_name' => $orgData?->org_name ?? $this->name,
            'email' => $orgData?->org_email ?? $this->email,
            'fiscal_code' => $orgData?->org_fiscal_code,
            'vat_number' => $orgData?->org_vat_number,
            'address' => $orgData?->full_organization_address,
            'phone' => $orgData?->org_phone_1,
            'website' => $orgData?->org_site_url,
            'business_type' => $orgData?->business_type,
            'can_issue_invoices' => $this->canIssueInvoices()
        ];
    }

    /**
     * Check if seller can issue invoices
     */
    public function canIssueInvoices(): bool
    {
        return $this->organizationData?->can_issue_invoices ?? false;
    }

    /**
     * Check if seller is VAT registered
     */
    public function isVatRegistered(): bool
    {
        return !empty($this->organizationData?->org_vat_number);
    }

    /**
     * Get seller verification status
     */
    public function getSellerVerificationStatus(): string
    {
        if (!$this->canActAsSeller()) {
            return 'not_eligible';
        }

        if ($this->isVerifiedSeller()) {
            return 'verified';
        }

        $missing = $this->getMissingSellerData();
        if (empty($missing)) {
            return 'pending_verification';
        }

        return 'incomplete_data';
    }

    /**
     * Get seller capabilities summary
     */
    public function getSellerCapabilities(): array
    {
        return [
            'can_sell' => $this->canActAsSeller(),
            'is_verified' => $this->isVerifiedSeller(),
            'can_issue_invoices' => $this->canIssueInvoices(),
            'is_vat_registered' => $this->isVatRegistered(),
            'verification_status' => $this->getSellerVerificationStatus(),
            'missing_data' => $this->getMissingSellerData(),
            'business_type' => $this->organizationData?->business_type
        ];
    }
}
PHP;
    }

    /**
     * Get HasWalletManagement trait content
     */
    protected function getHasWalletManagementTraitContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Models\Traits;

/**
 * @Oracode Trait: Wallet Management (Integration with existing wallet system)
 * 🎯 Purpose: Integrates with existing wallet table and management
 * 🛡️ Privacy: Handles wallet data with appropriate security measures
 * 🧱 Core Logic: Provides wallet access methods compatible with existing system
 */
trait HasWalletManagement
{
    /**
     * Get primary wallet (uses existing wallet field)
     */
    public function getPrimaryWallet(): ?string
    {
        return $this->wallet;
    }

    /**
     * Get wallet balance (uses existing wallet_balance field)
     */
    public function getWalletBalance(): float
    {
        return (float) $this->wallet_balance;
    }

    /**
     * Check if user has connected wallet
     */
    public function hasConnectedWallet(): bool
    {
        return !empty($this->wallet);
    }

    /**
     * Update wallet balance
     */
    public function updateWalletBalance(float $balance): bool
    {
        return $this->update(['wallet_balance' => $balance]);
    }

    /**
     * Connect wallet address
     */
    public function connectWallet(string $address): bool
    {
        return $this->update(['wallet' => $address]);
    }

    /**
     * Disconnect wallet
     */
    public function disconnectWallet(): bool
    {
        return $this->update([
            'wallet' => null,
            'personal_secret' => null,
            'wallet_balance' => 0
        ]);
    }

    /**
     * Get wallet connection status
     */
    public function getWalletStatus(): array
    {
        return [
            'connected' => $this->hasConnectedWallet(),
            'address' => $this->wallet,
            'balance' => $this->getWalletBalance(),
            'has_secret' => !empty($this->personal_secret)
        ];
    }

    /**
     * Validate wallet address format (Algorand)
     */
    public function isValidAlgorandAddress(string $address): bool
    {
        // Basic Algorand address validation
        return strlen($address) === 58 && ctype_alnum($address);
    }
}
PHP;
    }

    /**
     * Get HasCollectionAccess trait content
     */
    protected function getHasCollectionAccessTraitContent(): string
    {
        return <<<'PHP'
<?php

namespace App\Models\Traits;

/**
 * @Oracode Trait: Collection Access Management
 * 🎯 Purpose: Manages user relationships with collections and EGIs
 * 🛡️ Privacy: Handles collection access with appropriate permissions
 * 🧱 Core Logic: Centralizes collection-related user capabilities
 */
trait HasCollectionAccess
{
    /**
     * Get user's current active collection
     */
    public function getCurrentCollection()
    {
        return $this->belongsTo(Collection::class, 'current_collection_id')->first();
    }

    /**
     * Set current active collection
     */
    public function setCurrentCollection($collectionId): bool
    {
        return $this->update(['current_collection_id' => $collectionId]);
    }

    /**
     * Check if user owns specific collection
     */
    public function ownsCollection($collectionId): bool
    {
        return $this->ownedCollections()->where('id', $collectionId)->exists();
    }

    /**
     * Check if user is member of specific collection
     */
    public function isMemberOfCollection($collectionId): bool
    {
        return $this->collections()->where('collection_id', $collectionId)->exists();
    }

    /**
     * Get user's role in specific collection
     */
    public function getRoleInCollection($collectionId): ?string
    {
        $membership = $this->collections()
            ->where('collection_id', $collectionId)
            ->first();

        return $membership?->pivot->role;
    }

    /**
     * Check if user can access collection
     */
    public function canAccessCollection($collectionId): bool
    {
        return $this->ownsCollection($collectionId) ||
               $this->isMemberOfCollection($collectionId);
    }

    /**
     * Get collections where user has specific role
     */
    public function getCollectionsByRole(string $role)
    {
        return $this->collections()
            ->wherePivot('role', $role)
            ->get();
    }

    /**
     * Get user's collection statistics
     */
    public function getCollectionStats(): array
    {
        return [
            'owned_collections' => $this->ownedCollections()->count(),
            'member_collections' => $this->collections()->count(),
            'current_collection_id' => $this->current_collection_id,
            'roles' => $this->collections()
                ->select('role')
                ->distinct()
                ->pluck('role')
                ->toArray()
        ];
    }

    /**
     * Check if user can create collections
     */
    public function canCreateCollections(): bool
    {
        // Basic check - creators and above can create collections
        return in_array($this->usertype, ['creator', 'azienda', 'epp_entity']);
    }

    /**
     * Join collection with specific role
     */
    public function joinCollection($collectionId, string $role = 'member'): bool
    {
        if ($this->isMemberOfCollection($collectionId)) {
            return false;
        }

        $this->collections()->attach($collectionId, [
            'role' => $role,
            'is_owner' => false,
            'status' => 'active',
            'joined_at' => now()
        ]);

        return true;
    }

    /**
     * Leave collection
     */
    public function leaveCollection($collectionId): bool
    {
        if ($this->ownsCollection($collectionId)) {
            return false; // Owners cannot leave their own collections
        }

        return $this->collections()->detach($collectionId) > 0;
    }
}
PHP;
    }

    /**
     * Get generic model content
     */
    protected function getGenericModelContent(string $modelName, string $tableName): string
    {
        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @Oracode Model: {$modelName}
 * 🎯 Purpose: Generated model for {$tableName} table
 * 🛡️ Privacy: Standard data handling with appropriate access controls
 * 🧱 Core Logic: Basic model functionality with relationships
 */
class {$modelName} extends Model
{
    protected \$table = '{$tableName}';

    protected \$fillable = [
        // Add your fillable fields here
    ];

    protected \$casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return \$this->belongsTo(User::class);
    }
}
PHP;
    }

    /**
     * Get generic trait content
     */
    protected function getGenericTraitContent(string $traitName): string
    {
        return <<<PHP
<?php

namespace App\Models\Traits;

/**
 * @Oracode Trait: {$traitName}
 * 🎯 Purpose: Generated trait for specific functionality
 * 🛡️ Privacy: Handles data with appropriate access controls
 * 🧱 Core Logic: Centralized functionality for user capabilities
 */
trait {$traitName}
{
    // Add your trait methods here
}
PHP;
    }
}
