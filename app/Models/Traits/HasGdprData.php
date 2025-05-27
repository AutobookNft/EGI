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