<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UpdateOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Helpers\FegiAuth;

/**
 * @Oracode Controller: User Organization Data Management
 * 🎯 Purpose: Manage organization/business data for creator/enterprise users
 * 🛡️ Privacy: Organization separation logic with role-based access
 * 🧱 Core Logic: Edit/Update pattern with permission-based visibility and FegiAuth
 */
class UserOrganizationController extends BaseUserDomainController
{
    /**
     * Show organization data edit form
     */
    public function edit(): View|RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can access organization data
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('edit_own_organization_data')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();

            // Verify user can manage organization data (creator, enterprise, epp_entity)
            $allowedRoles = ['creator', 'enterprise', 'epp_entity'];
            if (!$user->hasAnyRole($allowedRoles)) {
                abort(403, __('user_organization.role_not_allowed'));
            }

            // Load or create organization data
            $organizationData = $user->organizationData ?? $user->organizationData()->create([
                'organization_type' => $this->getDefaultOrganizationType($user),
                'is_verified' => false,
                'verification_level' => 'none',
            ]);

            $organizationTypes = [
                'sole_proprietorship' => __('user_organization.type_sole_proprietorship'),
                'srl' => __('user_organization.type_srl'),
                'spa' => __('user_organization.type_spa'),
                'snc' => __('user_organization.type_snc'),
                'sas' => __('user_organization.type_sas'),
                'cooperative' => __('user_organization.type_cooperative'),
                'association' => __('user_organization.type_association'),
                'foundation' => __('user_organization.type_foundation'),
                'ngo' => __('user_organization.type_ngo'),
                'public_entity' => __('user_organization.type_public_entity'),
                'other' => __('user_organization.type_other'),
            ];

            $businessSectors = [
                'art_culture' => __('user_organization.sector_art_culture'),
                'craftsmanship' => __('user_organization.sector_craftsmanship'),
                'design' => __('user_organization.sector_design'),
                'fashion' => __('user_organization.sector_fashion'),
                'food_beverage' => __('user_organization.sector_food_beverage'),
                'technology' => __('user_organization.sector_technology'),
                'sustainability' => __('user_organization.sector_sustainability'),
                'education' => __('user_organization.sector_education'),
                'consulting' => __('user_organization.sector_consulting'),
                'retail' => __('user_organization.sector_retail'),
                'manufacturing' => __('user_organization.sector_manufacturing'),
                'services' => __('user_organization.sector_services'),
                'tourism' => __('user_organization.sector_tourism'),
                'agriculture' => __('user_organization.sector_agriculture'),
                'other' => __('user_organization.sector_other'),
            ];

            $companySizes = [
                'micro' => __('user_organization.size_micro'),
                'small' => __('user_organization.size_small'),
                'medium' => __('user_organization.size_medium'),
                'large' => __('user_organization.size_large'),
                'individual' => __('user_organization.size_individual'),
            ];

            $this->logger->info('[User Organization] Organization edit form accessed', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'user_roles' => $user->getRoleNames()->toArray(),
                'organization_type' => $organizationData->organization_type
            ]);

            return view('user.organization.edit', compact(
                'user',
                'organizationData',
                'organizationTypes',
                'businessSectors',
                'companySizes'
            ));

        } catch (\Exception $e) {
            return $this->handleError('USER_ORGANIZATION_EDIT_FAILED', [
                'action' => 'edit_form'
            ], $e);
        }
    }

    /**
     * Update organization data
     */
    public function update(UpdateOrganizationRequest $request): RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can update organization data
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('edit_own_organization_data')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();

            // Double-check role permission
            $allowedRoles = ['creator', 'enterprise', 'epp_entity'];
            if (!$user->hasAnyRole($allowedRoles)) {
                abort(403, __('user_organization.role_not_allowed'));
            }

            $validated = $request->validated();

            // Get or create organization data
            $organizationData = $user->organizationData ?? $user->organizationData()->create();

            // Track changes for audit
            $oldData = $organizationData->toArray();

            // Update organization data
            $organizationData->update([
                'organization_type' => $validated['organization_type'],
                'company_name' => $validated['company_name'],
                'vat_number' => $validated['vat_number'] ?? null,
                'tax_code' => $validated['tax_code'] ?? null,
                'chamber_of_commerce_number' => $validated['chamber_of_commerce_number'] ?? null,
                'legal_representative_name' => $validated['legal_representative_name'] ?? null,
                'legal_representative_surname' => $validated['legal_representative_surname'] ?? null,
                'legal_representative_tax_code' => $validated['legal_representative_tax_code'] ?? null,
                'business_sector' => $validated['business_sector'],
                'company_size' => $validated['company_size'],
                'foundation_year' => $validated['foundation_year'] ?? null,
                'headquarters_address_line_1' => $validated['headquarters_address_line_1'],
                'headquarters_address_line_2' => $validated['headquarters_address_line_2'] ?? null,
                'headquarters_city' => $validated['headquarters_city'],
                'headquarters_state' => $validated['headquarters_state'] ?? null,
                'headquarters_postal_code' => $validated['headquarters_postal_code'],
                'headquarters_country' => $validated['headquarters_country'],
                'phone' => $validated['phone'] ?? null,
                'website' => $validated['website'] ?? null,
                'description' => $validated['description'] ?? null,
                'certifications' => $validated['certifications'] ?? null,
                'sustainability_goals' => $validated['sustainability_goals'] ?? null,
                'epp_commitment_level' => $validated['epp_commitment_level'] ?? null,
            ]);

            // Reset verification if critical data changed
            $criticalFields = ['company_name', 'vat_number', 'tax_code', 'chamber_of_commerce_number'];
            $criticalChanged = false;
            foreach ($criticalFields as $field) {
                if (($oldData[$field] ?? null) !== ($validated[$field] ?? null)) {
                    $criticalChanged = true;
                    break;
                }
            }

            if ($criticalChanged && $organizationData->is_verified) {
                $organizationData->update([
                    'is_verified' => false,
                    'verification_level' => 'pending_reverification',
                    'verification_notes' => __('user_organization.verification_reset_note')
                ]);
            }

            // Log changes for audit trail
            $this->logUserAction('organization_data_updated', [
                'organization_type' => $validated['organization_type'],
                'company_name' => $validated['company_name'],
                'has_vat_number' => !empty($validated['vat_number']),
                'business_sector' => $validated['business_sector'],
                'critical_data_changed' => $criticalChanged,
                'verification_reset' => $criticalChanged && $oldData['is_verified'] ?? false,
                'changes_count' => count(array_diff_assoc($validated, $oldData)),
            ], 'organization_management');

            $this->logger->info('[User Organization] Organization data updated successfully', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'organization_type' => $validated['organization_type'],
                'company_name' => $validated['company_name'],
                'verification_reset' => $criticalChanged
            ]);

            $message = __('user_organization.update_success');
            if ($criticalChanged && ($oldData['is_verified'] ?? false)) {
                $message .= ' ' . __('user_organization.verification_reset_warning');
            }

            return redirect()->route('user.organization.edit')
                ->with('success', $message);

        } catch (\Exception $e) {
            return $this->handleError('USER_ORGANIZATION_UPDATE_FAILED', [
                'action' => 'update',
                'organization_type' => $request->input('organization_type'),
                'company_name' => $request->input('company_name'),
            ], $e);
        }
    }

    /**
     * Show organization verification status
     */
    public function verificationStatus(): View|RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can view verification status
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('edit_own_organization_data')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();
            $organizationData = $user->organizationData;

            if (!$organizationData) {
                return redirect()->route('user.organization.edit')
                    ->with('info', __('user_organization.complete_data_first'));
            }

            $verificationSteps = [
                'basic_data' => $this->checkBasicDataComplete($organizationData),
                'legal_documents' => $this->checkLegalDocumentsUploaded($organizationData),
                'business_verification' => $this->checkBusinessVerification($organizationData),
                'epp_compliance' => $this->checkEppCompliance($organizationData),
            ];

            $this->logger->info('[User Organization] Verification status accessed', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'verification_level' => $organizationData->verification_level,
                'completion_percentage' => $this->calculateCompletionPercentage($verificationSteps)
            ]);

            return view('user.organization.verification-status', compact(
                'user',
                'organizationData',
                'verificationSteps'
            ));

        } catch (\Exception $e) {
            return $this->handleError('USER_ORGANIZATION_VERIFICATION_STATUS_FAILED', [
                'action' => 'verification_status'
            ], $e);
        }
    }

    /**
     * Get default organization type based on user role
     */
    private function getDefaultOrganizationType($user): string
    {
        if ($user->hasRole('creator')) {
            return 'sole_proprietorship';
        } elseif ($user->hasRole('enterprise')) {
            return 'srl';
        } elseif ($user->hasRole('epp_entity')) {
            return 'association';
        }

        return 'other';
    }

    /**
     * Check if basic organization data is complete
     */
    private function checkBasicDataComplete($organizationData): array
    {
        $required = [
            'company_name', 'organization_type', 'business_sector',
            'headquarters_address_line_1', 'headquarters_city',
            'headquarters_postal_code', 'headquarters_country'
        ];

        $completed = 0;
        foreach ($required as $field) {
            if (!empty($organizationData->$field)) {
                $completed++;
            }
        }

        return [
            'status' => $completed === count($required) ? 'complete' : 'incomplete',
            'progress' => round(($completed / count($required)) * 100),
            'required_count' => count($required),
            'completed_count' => $completed,
        ];
    }

    /**
     * Check if legal documents are uploaded
     */
    private function checkLegalDocumentsUploaded($organizationData): array
    {
        $user = $organizationData->user;
        $legalDocs = $user->documents()
            ->whereIn('document_type', ['vat_certificate', 'business_registration'])
            ->where('verification_status', '!=', 'rejected')
            ->count();

        return [
            'status' => $legalDocs >= 1 ? 'complete' : 'incomplete',
            'uploaded_count' => $legalDocs,
            'required_count' => 1,
        ];
    }

    /**
     * Check business verification status
     */
    private function checkBusinessVerification($organizationData): array
    {
        return [
            'status' => $organizationData->is_verified ? 'complete' : 'pending',
            'verification_level' => $organizationData->verification_level,
            'verified_at' => $organizationData->verified_at,
        ];
    }

    /**
     * Check EPP compliance
     */
    private function checkEppCompliance($organizationData): array
    {
        $hasEppCommitment = !empty($organizationData->epp_commitment_level);
        $hasSustainabilityGoals = !empty($organizationData->sustainability_goals);

        return [
            'status' => ($hasEppCommitment && $hasSustainabilityGoals) ? 'complete' : 'incomplete',
            'has_commitment' => $hasEppCommitment,
            'has_goals' => $hasSustainabilityGoals,
            'commitment_level' => $organizationData->epp_commitment_level,
        ];
    }

    /**
     * Calculate overall completion percentage
     */
    private function calculateCompletionPercentage($steps): int
    {
        $total = count($steps);
        $completed = 0;

        foreach ($steps as $step) {
            if ($step['status'] === 'complete') {
                $completed++;
            }
        }

        return round(($completed / $total) * 100);
    }
}
