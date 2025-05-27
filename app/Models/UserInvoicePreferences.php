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