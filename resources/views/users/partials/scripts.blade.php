{{--
@Oracode Partial: Personal Data Management Styles
🎯 Purpose: CSS styling for personal data form with country-specific adaptations
🛡️ Privacy: Visual indicators for GDPR compliance and sensitive data handling
🧱 Core Logic: Responsive design + country form adaptations + validation feedback
🌍 Scale: Multi-device support with accessibility-first approach
⏰ MVP: Critical styling for user domain interface

@package resources/views/user/domains/personal-data/partials
@author Padmin D. Curtis (AI Partner OS1-Compliant)
@version 1.0.0 (FlorenceEGI MVP - GDPR Native)
@deadline 2025-06-30

@oracode-dimension communication (visual interface)
@oracode-dimension impact (user experience quality)
@value-flow Enhances form usability and GDPR transparency
@accessibility-compliant Full WCAG 2.1 AA compliance
@responsive-design Mobile-first with tablet and desktop optimization
--}}

<style>
/* =============================================================================
   GDPR Status Indicators
   ============================================================================= */
.gdpr-status-indicator {
    @apply flex items-center justify-between p-4 rounded-lg border-l-4 transition-all duration-300 ease-in-out;
    background: linear-gradient(135deg, var(--bg-color) 0%, var(--bg-light) 100%);
}

.gdpr-status-indicator.compliant {
    --bg-color: #ecfdf5;
    --bg-light: #f0fdf4;
    --border-color: #10b981;
    --text-color: #047857;
    --icon-color: #059669;
    border-left-color: var(--border-color);
    color: var(--text-color);
}

.gdpr-status-indicator.partial {
    --bg-color: #fef3c7;
    --bg-light: #fffbeb;
    --border-color: #f59e0b;
    --text-color: #92400e;
    --icon-color: #d97706;
    border-left-color: var(--border-color);
    color: var(--text-color);
}

.gdpr-status-indicator.non-compliant {
    --bg-color: #fef2f2;
    --bg-light: #fefefe;
    --border-color: #ef4444;
    --text-color: #dc2626;
    --icon-color: #f87171;
    border-left-color: var(--border-color);
    color: var(--text-color);
}

.gdpr-compliance-score {
    @apply inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold;
}

.gdpr-compliance-score.high {
    @apply bg-green-100 text-green-800;
}

.gdpr-compliance-score.medium {
    @apply bg-yellow-100 text-yellow-800;
}

.gdpr-compliance-score.low {
    @apply bg-red-100 text-red-800;
}

/* =============================================================================
   Authentication Type Badges
   ============================================================================= */
.auth-type-badge {
    @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-200;
}

.auth-type-badge.strong {
    @apply bg-green-100 text-green-800 border border-green-200;
    box-shadow: 0 1px 3px rgba(34, 197, 94, 0.1);
}

.auth-type-badge.weak {
    @apply bg-yellow-100 text-yellow-800 border border-yellow-200;
    box-shadow: 0 1px 3px rgba(245, 158, 11, 0.1);
}

.auth-type-badge .icon {
    @apply w-3 h-3 mr-1;
}

/* =============================================================================
   Country-Specific Form Adaptations
   ============================================================================= */
.country-specific-field {
    @apply transition-all duration-300 ease-in-out;
}

.country-specific-field.inactive {
    @apply opacity-40 pointer-events-none;
}

.country-specific-field.required::after {
    content: " *";
    @apply text-red-500 font-bold;
}

/* Country-specific input styling */
.fiscal-code-input {
    @apply uppercase tracking-wider font-mono;
    letter-spacing: 0.1em;
}

.postal-code-input {
    @apply font-mono tracking-wider;
}

/* Dynamic country labels */
.dynamic-label {
    @apply transition-all duration-200 ease-in-out;
}

.dynamic-label.changing {
    @apply opacity-50 scale-95;
}

/* =============================================================================
   Fiscal Validation Feedback
   ============================================================================= */
.fiscal-validation-feedback {
    @apply text-sm mt-2 px-3 py-2 rounded-md border-l-3 transition-all duration-300;
}

.fiscal-validation-feedback.valid {
    @apply bg-green-50 text-green-800 border-green-400;
}

.fiscal-validation-feedback.invalid {
    @apply bg-red-50 text-red-800 border-red-400;
}

.fiscal-validation-feedback.checking {
    @apply bg-blue-50 text-blue-800 border-blue-400 animate-pulse;
}

.fiscal-validation-feedback .icon {
    @apply inline-block w-4 h-4 mr-2 align-text-bottom;
}

/* =============================================================================
   Processing Purposes Grid
   ============================================================================= */
.processing-purposes-grid {
    @apply grid gap-4;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

.processing-purpose-card {
    @apply p-4 border border-gray-200 rounded-lg transition-all duration-200 hover:shadow-md;
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
}

.processing-purpose-card.selected {
    @apply border-blue-300 bg-blue-50 shadow-sm;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.processing-purpose-card:hover {
    @apply border-gray-300 transform scale-105;
}

.processing-purpose-card.selected:hover {
    @apply border-blue-400;
}

/* =============================================================================
   Form Sections and Layout
   ============================================================================= */
.form-section {
    @apply space-y-6 pb-8 border-b border-gray-200 last:border-b-0;
}

.form-section-header {
    @apply flex items-center justify-between mb-6;
}

.form-section-title {
    @apply text-lg font-semibold text-gray-900 flex items-center;
}

.form-section-title .icon {
    @apply w-5 h-5 mr-2 text-gray-600;
}

.form-section-description {
    @apply text-sm text-gray-600 mt-1 max-w-2xl;
}

/* =============================================================================
   Form Input Enhancements
   ============================================================================= */
.form-input-group {
    @apply space-y-2;
}

.form-input-with-validation {
    @apply relative;
}

.form-input-with-validation .validation-icon {
    @apply absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 transition-all duration-200;
}

.form-input-with-validation .validation-icon.valid {
    @apply text-green-500;
}

.form-input-with-validation .validation-icon.invalid {
    @apply text-red-500;
}

.form-input-with-validation .validation-icon.checking {
    @apply text-blue-500 animate-spin;
}

/* =============================================================================
   Loading and State Indicators
   ============================================================================= */
.loading-overlay {
    @apply absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50 transition-opacity duration-300;
}

.loading-spinner {
    @apply animate-spin h-8 w-8 text-blue-600;
}

.form-saving {
    @apply pointer-events-none opacity-60;
}

.form-saving::after {
    content: "";
    @apply absolute inset-0 bg-white bg-opacity-50 z-40;
}

/* =============================================================================
   Last Updated Indicator
   ============================================================================= */
.last-updated-indicator {
    @apply flex items-center text-sm text-gray-500 space-x-2;
}

.last-updated-indicator .icon {
    @apply w-4 h-4;
}

.last-updated-indicator time {
    @apply font-medium;
}

/* =============================================================================
   Export and Action Buttons
   ============================================================================= */
.gdpr-action-buttons {
    @apply flex flex-wrap gap-3 pt-4 border-t border-gray-200;
}

.export-button {
    @apply inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200;
}

.export-button:disabled {
    @apply opacity-50 cursor-not-allowed;
}

.export-button .icon {
    @apply w-4 h-4 mr-2;
}

.delete-data-button {
    @apply inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors duration-200;
}

/* =============================================================================
   Responsive Design Adaptations
   ============================================================================= */
@media (max-width: 640px) {
    .processing-purposes-grid {
        grid-template-columns: 1fr;
    }

    .form-section-header {
        @apply flex-col items-start space-y-3;
    }

    .gdpr-status-indicator {
        @apply flex-col items-start space-y-3;
    }

    .auth-type-badge {
        @apply self-start;
    }

    .last-updated-indicator {
        @apply flex-col items-start space-y-1 space-x-0;
    }
}

@media (max-width: 768px) {
    .form-section {
        @apply space-y-4 pb-6;
    }

    .form-section-title {
        @apply text-base;
    }

    .processing-purpose-card {
        @apply p-3;
    }

    .gdpr-action-buttons {
        @apply flex-col space-y-2 gap-0;
    }
}

/* =============================================================================
   Print Styles for GDPR Export
   ============================================================================= */
@media print {
    .gdpr-status-indicator,
    .auth-type-badge,
    .form-actions,
    .gdpr-action-buttons {
        @apply hidden;
    }

    .form-section {
        @apply border-b border-gray-400 pb-4 mb-4;
    }

    .processing-purposes-grid {
        grid-template-columns: 1fr 1fr;
    }

    .processing-purpose-card {
        @apply border border-gray-400 shadow-none;
    }

    body {
        @apply text-black bg-white;
    }
}

/* =============================================================================
   Dark Mode Support (Future Enhancement)
   ============================================================================= */
@media (prefers-color-scheme: dark) {
    .form-section {
        @apply border-gray-700;
    }

    .processing-purpose-card {
        @apply border-gray-600 bg-gray-800;
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    }

    .processing-purpose-card.selected {
        @apply border-blue-500 bg-blue-900;
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }

    .gdpr-status-indicator.compliant {
        --bg-color: #064e3b;
        --bg-light: #065f46;
    }

    .gdpr-status-indicator.partial {
        --bg-color: #78350f;
        --bg-light: #92400e;
    }

    .gdpr-status-indicator.non-compliant {
        --bg-color: #7f1d1d;
        --bg-light: #991b1b;
    }
}

/* =============================================================================
   Accessibility Enhancements
   ============================================================================= */
@media (prefers-reduced-motion: reduce) {
    .country-specific-field,
    .fiscal-validation-feedback,
    .processing-purpose-card,
    .loading-overlay {
        @apply transition-none;
    }

    .loading-spinner {
        @apply animate-none;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .gdpr-status-indicator {
        @apply border-2;
    }

    .processing-purpose-card {
        @apply border-2;
    }

    .fiscal-validation-feedback {
        @apply border-l-4;
    }
}

/* Focus indicators for keyboard navigation */
.form-input:focus,
.form-select:focus,
.form-checkbox:focus,
.form-textarea:focus {
    @apply ring-2 ring-blue-500 ring-offset-2 outline-none;
}

.processing-purpose-card:focus-within {
    @apply ring-2 ring-blue-500 ring-offset-2;
}

/* =============================================================================
   Animation Keyframes
   ============================================================================= */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.3s ease-out;
}

.animate-slide-in-right {
    animation: slideInRight 0.3s ease-out;
}
</style>
