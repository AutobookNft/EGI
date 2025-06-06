<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UpdateInvoicePreferencesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use App\Helpers\FegiAuth;

/**
 * @Oracode Controller: User Invoice Preferences Management
 * 🎯 Purpose: Manage user billing and invoice configuration
 * 🛡️ Privacy: Invoice data with VAT compliance for Italian market
 * 🧱 Core Logic: Edit/Update pattern for invoice preferences with FegiAuth
 */
class UserInvoicePreferencesController extends BaseUserDomainController
{
    /**
     * Show invoice preferences edit form
     */
    public function edit(): View|RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can access invoice preferences
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('manage_own_invoice_preferences')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();

            // Load or create invoice preferences
            $invoicePreferences = $user->invoicePreferences ?? $user->invoicePreferences()->create([
                'billing_type' => 'individual',
                'send_invoices_via_email' => true,
                'invoice_language' => 'it',
                'payment_terms_days' => 30,
            ]);

            $billingTypes = [
                'individual' => __('user_invoice.billing_type_individual'),
                'business' => __('user_invoice.billing_type_business'),
                'professional' => __('user_invoice.billing_type_professional'),
                'non_profit' => __('user_invoice.billing_type_non_profit'),
            ];

            $invoiceLanguages = [
                'it' => __('user_invoice.language_italian'),
                'en' => __('user_invoice.language_english'),
                'fr' => __('user_invoice.language_french'),
                'de' => __('user_invoice.language_german'),
                'es' => __('user_invoice.language_spanish'),
            ];

            $paymentMethods = [
                'bank_transfer' => __('user_invoice.payment_bank_transfer'),
                'credit_card' => __('user_invoice.payment_credit_card'),
                'paypal' => __('user_invoice.payment_paypal'),
                'crypto' => __('user_invoice.payment_crypto'),
            ];

            $this->logger->info('[User Invoice] Preferences edit form accessed', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'current_billing_type' => $invoicePreferences->billing_type
            ]);

            return view('user.invoice-preferences.edit', compact(
                'user',
                'invoicePreferences',
                'billingTypes',
                'invoiceLanguages',
                'paymentMethods'
            ));

        } catch (\Exception $e) {
            return $this->handleError('USER_INVOICE_PREFERENCES_EDIT_FAILED', [
                'action' => 'edit_form'
            ], $e);
        }
    }

    /**
     * Update invoice preferences
     */
    public function update(UpdateInvoicePreferencesRequest $request): RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can configure invoice preferences
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('configure_invoice_preferences')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();
            $validated = $request->validated();

            // Get or create invoice preferences
            $invoicePreferences = $user->invoicePreferences ?? $user->invoicePreferences()->create();

            // Track changes for audit
            $oldData = $invoicePreferences->toArray();

            // Update preferences
            $invoicePreferences->update([
                'billing_type' => $validated['billing_type'],
                'company_name' => $validated['company_name'] ?? null,
                'vat_number' => $validated['vat_number'] ?? null,
                'tax_code' => $validated['tax_code'] ?? null,
                'sdi_code' => $validated['sdi_code'] ?? null,
                'certified_email' => $validated['certified_email'] ?? null,
                'billing_address_line_1' => $validated['billing_address_line_1'],
                'billing_address_line_2' => $validated['billing_address_line_2'] ?? null,
                'billing_city' => $validated['billing_city'],
                'billing_state' => $validated['billing_state'] ?? null,
                'billing_postal_code' => $validated['billing_postal_code'],
                'billing_country' => $validated['billing_country'],
                'send_invoices_via_email' => $request->boolean('send_invoices_via_email'),
                'invoice_email' => $validated['invoice_email'] ?? $user->email,
                'invoice_language' => $validated['invoice_language'],
                'preferred_payment_method' => $validated['preferred_payment_method'],
                'payment_terms_days' => $validated['payment_terms_days'],
                'special_instructions' => $validated['special_instructions'] ?? null,
            ]);

            // Log changes for audit trail
            $this->logUserAction('invoice_preferences_updated', [
                'old_billing_type' => $oldData['billing_type'] ?? null,
                'new_billing_type' => $validated['billing_type'],
                'has_vat_number' => !empty($validated['vat_number']),
                'changes_count' => count(array_diff_assoc($validated, $oldData)),
            ], 'invoice_management');

            $this->logger->info('[User Invoice] Preferences updated successfully', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'billing_type' => $validated['billing_type'],
                'has_company_data' => !empty($validated['company_name'])
            ]);

            return redirect()->route('user.invoice-preferences.edit')
                ->with('success', __('user_invoice.preferences_updated_success'));

        } catch (\Exception $e) {
            return $this->handleError('USER_INVOICE_PREFERENCES_UPDATE_FAILED', [
                'action' => 'update',
                'billing_type' => $request->input('billing_type'),
            ], $e);
        }
    }

    /**
     * Show user invoices history
     */
    public function invoices(): View|RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can view invoices
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('view_own_invoices')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();

            $invoices = $user->invoices()
                ->with(['items'])
                ->orderBy('invoice_date', 'desc')
                ->paginate(20);

            $totalPaid = $user->invoices()
                ->where('status', 'paid')
                ->sum('total_amount');

            $totalPending = $user->invoices()
                ->where('status', 'pending')
                ->sum('total_amount');

            $this->logger->info('[User Invoice] Invoices history accessed', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'invoices_count' => $invoices->total(),
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending
            ]);

            return view('user.invoice-preferences.invoices', compact(
                'user',
                'invoices',
                'totalPaid',
                'totalPending'
            ));

        } catch (\Exception $e) {
            return $this->handleError('USER_INVOICES_HISTORY_FAILED', [
                'action' => 'invoices_history'
            ], $e);
        }
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(int $invoiceId): Response|RedirectResponse
    {
        try {
            // FegiAuth check - only strong auth can download invoices
            if (FegiAuth::isWeakAuth()) {
                return $this->redirectToUpgrade();
            }

            if (!$this->checkWeakAuthAccess('download_own_invoices')) {
                abort(403, __('user_domain.access_denied'));
            }

            $user = FegiAuth::user();
            $invoice = $user->invoices()->findOrFail($invoiceId);

            // Log download for audit trail
            $this->logUserAction('invoice_downloaded', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->total_amount,
            ], 'invoice_management');

            $this->logger->info('[User Invoice] Invoice downloaded', [
                'user_id' => $user->id,
                'auth_type' => FegiAuth::getAuthType(),
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number
            ]);

            // Generate PDF and return download
            $pdf = app('invoicePdfGenerator')->generate($invoice);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . __('user_invoice.filename_prefix') . $invoice->invoice_number . '.pdf"'
            ]);

        } catch (\Exception $e) {
            return $this->handleError('USER_INVOICE_DOWNLOAD_FAILED', [
                'action' => 'download_invoice',
                'invoice_id' => $invoiceId,
            ], $e);
        }
    }
}
