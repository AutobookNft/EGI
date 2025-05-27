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