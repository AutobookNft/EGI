<?php

namespace App\Http\Requests\Gdpr;

use Illuminate\Foundation\Http\FormRequest;

/**
 * GDPR Breach Report Request Validation
 *
 * Validates breach report requests with proper rules.
 *
 * @oracode-dimension governance
 * @value-flow Ensures valid breach reporting data
 * @community-impact Facilitates secure reporting channel
 * @transparency-level High - provides clear validation messages
 * @narrative-coherence Supports data protection vigilance
 */
class BreachReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Anyone (even guests) can submit a breach report
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'reporter_name' => 'required|string|max:255',
            'reporter_email' => 'required|email|max:255',
            'incident_date' => 'required|date|before_or_equal:today',
            'breach_description' => 'required|string|min:20|max:5000',
            'affected_data' => 'required|string|max:1000',
            'discovery_method' => 'required|string|max:255',
            'supporting_evidence' => 'nullable|file|mimes:pdf,jpg,jpeg,png,txt,doc,docx|max:10240',
            'consent_to_contact' => 'required|boolean|accepted',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'reporter_name.required' => __('gdpr.validation.reporter_name_required'),
            'reporter_email.required' => __('gdpr.validation.reporter_email_required'),
            'reporter_email.email' => __('gdpr.validation.reporter_email_format'),
            'incident_date.required' => __('gdpr.validation.incident_date_required'),
            'incident_date.date' => __('gdpr.validation.incident_date_format'),
            'incident_date.before_or_equal' => __('gdpr.validation.incident_date_past'),
            'breach_description.required' => __('gdpr.validation.breach_description_required'),
            'breach_description.min' => __('gdpr.validation.breach_description_min'),
            'affected_data.required' => __('gdpr.validation.affected_data_required'),
            'discovery_method.required' => __('gdpr.validation.discovery_method_required'),
            'supporting_evidence.mimes' => __('gdpr.validation.supporting_evidence_format'),
            'supporting_evidence.max' => __('gdpr.validation.supporting_evidence_max'),
            'consent_to_contact.required' => __('gdpr.validation.consent_to_contact_required'),
            'consent_to_contact.accepted' => __('gdpr.validation.consent_to_contact_accepted'),
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'consent_to_contact' => $this->boolean('consent_to_contact'),
        ]);
    }
}
