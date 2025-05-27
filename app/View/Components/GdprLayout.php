<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * @Oracode Component: GDPR Layout Component
 * 🎯 Purpose: Provide dedicated layout for GDPR privacy management interfaces
 * 🛡️ Privacy: Optimized for data protection user interfaces
 * 🎨 Brand: FlorenceEGI compliant with light theme for better accessibility
 *
 * @package App\View\Components
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0 - Initial GDPR layout component
 * @date 2025-05-25
 * @seo-purpose Structured layout for privacy control interfaces
 * @accessibility-trait Full ARIA support and semantic HTML structure
 */
class GdprLayout extends Component
{
    /**
     * Page title for SEO and accessibility
     */
    public string $pageTitle;

    /**
     * Page description for SEO
     */
    public string $pageDescription;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $pageTitle = null,
        ?string $pageDescription = null
    ) {
        $this->pageTitle = $pageTitle ?? __('gdpr.privacy_control_center');
        $this->pageDescription = $pageDescription ?? __('gdpr.privacy_control_center_description');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.gdpr-layout');
    }
}
