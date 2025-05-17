<?php

namespace App\Http\Controllers;

use App\Models\EgiReservationCertificate;
use App\Services\CertificateGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ultra\ErrorManager\Facades\UltraError;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Controller: EgiReservationCertificateController
 * 🎯 Purpose: Handles certificate display, download, and verification
 * 🧱 Core Logic: Manages certificate lifecycle and verification
 * 🛡️ GDPR: Handles certificate data with privacy in mind
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-16
 * @seo-purpose Provides public certificate verification pages
 */
class EgiReservationCertificateController extends Controller
{
    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * @var CertificateGeneratorService
     */
    protected CertificateGeneratorService $certificateGenerator;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param CertificateGeneratorService $certificateGenerator
     */
    public function __construct(
        UltraLogManager $logger,
        CertificateGeneratorService $certificateGenerator
    ) {
        $this->logger = $logger;
        $this->certificateGenerator = $certificateGenerator;
    }

    /**
     * Show certificate details
     *
     * @param Request $request The HTTP request
     * @param string $uuid The certificate UUID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *
     * @seo-purpose Display certificate details with proper metadata
     * @schema-type Certificate
     */
    public function show(Request $request, string $uuid)
    {
        try {
            $certificate = EgiReservationCertificate::where('certificate_uuid', $uuid)
                ->with(['egi', 'egi.collection', 'reservation'])
                ->firstOrFail();

            // Log access to certificate
            $this->logger->info('Certificate view accessed', [
                'certificate_uuid' => $uuid,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Check if this is a newly created certificate (coming from reservation flow)
            $showSuccess = $request->session()->has('success');

            return view('certificates.show', [
                'certificate' => $certificate,
                'showSuccess' => $showSuccess,
                'title' => __('certificate.page_title', ['uuid' => $certificate->certificate_uuid]),
                'metaDescription' => __('certificate.meta_description', [
                    'type' => ucfirst($certificate->reservation_type),
                    'title' => $certificate->egi->title ?? __('certificate.unknown_egi')
                ])
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to display certificate', [
                'certificate_uuid' => $uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('home')->with('error', __('certificate.not_found'));
        }
    }

    /**
     * Download certificate PDF
     *
     * @param Request $request The HTTP request
     * @param string $uuid The certificate UUID
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(Request $request, string $uuid)
    {
        try {
            $certificate = EgiReservationCertificate::where('certificate_uuid', $uuid)->firstOrFail();

            // Check if PDF exists
            if (!$certificate->hasPdf()) {
                // Try to generate it if missing
                $result = $this->certificateGenerator->generatePdf($certificate);

                if (!$result || !$certificate->hasPdf()) {
                    throw new \Exception('Failed to generate PDF for certificate');
                }
            }

            // Log download
            $this->logger->info('Certificate PDF downloaded', [
                'certificate_uuid' => $uuid,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Return the file
            return Storage::download(
                $certificate->pdf_path,
                'Certificate_' . $certificate->certificate_uuid . '.pdf',
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="Certificate_' . $certificate->certificate_uuid . '.pdf"'
                ]
            );

        } catch (\Exception $e) {
            $this->logger->error('Certificate PDF download failed', [
                'certificate_uuid' => $uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('egi-certificates.show', $uuid)
                ->with('error', __('certificate.download_failed'));
        }
    }

    /**
     * Verify certificate authenticity
     *
     * @param Request $request The HTTP request
     * @param string $uuid The certificate UUID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *
     * @seo-purpose Public verification page for certificates
     * @schema-type VerificationService
     */
    public function verify(Request $request, string $uuid)
    {
        try {
            $certificate = EgiReservationCertificate::where('certificate_uuid', $uuid)
                ->with(['egi', 'egi.collection', 'reservation'])
                ->firstOrFail();

            // Generate verification data
            $verificationData = $certificate->generateVerificationData();

            // Verify signature
            $isValid = $certificate->verifySignature($verificationData);

            // Check if certificate is current highest
            $isHighestPriority = $certificate->is_current_highest && !$certificate->is_superseded;

            // Check if EGI is still available (not minted)
            $isEgiAvailable = $certificate->egi && !$certificate->egi->mint;

            // Log verification
            $this->logger->info('Certificate verification accessed', [
                'certificate_uuid' => $uuid,
                'is_valid' => $isValid,
                'is_highest_priority' => $isHighestPriority,
                'is_egi_available' => $isEgiAvailable,
                'ip' => $request->ip()
            ]);

            return view('certificates.verify', [
                'certificate' => $certificate,
                'isValid' => $isValid,
                'isHighestPriority' => $isHighestPriority,
                'isEgiAvailable' => $isEgiAvailable,
                'title' => __('certificate.verify_page_title', ['uuid' => $certificate->certificate_uuid]),
                'metaDescription' => __('certificate.verify_meta_description', [
                    'uuid' => $certificate->certificate_uuid
                ])
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Certificate verification failed', [
                'certificate_uuid' => $uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('home')
                ->with('error', __('certificate.verification_failed'));
        }
    }

    /**
     * List certificates for an EGI
     *
     * @param Request $request The HTTP request
     * @param int $egiId The EGI ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function listByEgi(Request $request, int $egiId)
    {
        try {
            // Only show certificates for active reservations
            $certificates = EgiReservationCertificate::where('egi_id', $egiId)
                ->whereHas('reservation', function ($query) {
                    $query->where('status', 'active');
                })
                ->with(['reservation'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('certificates.list-by-egi', [
                'certificates' => $certificates,
                'egiId' => $egiId,
                'title' => __('certificate.list_by_egi_title', ['egi_id' => $egiId]),
                'metaDescription' => __('certificate.list_by_egi_meta_description', ['egi_id' => $egiId])
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to list certificates by EGI', [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('home.collections.index')
                ->with('error', __('certificate.list_failed'));
        }
    }

    /**
     * List certificates for a user
     *
     * @param Request $request The HTTP request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *
     * @privacy-safe Only shows user's own certificates
     */
    public function listByUser(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', __('certificate.auth_required'));
        }

        try {
            // Get all certificates for user's reservations
            $certificates = EgiReservationCertificate::where('user_id', $user->id)
                ->with(['egi', 'egi.collection', 'reservation'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('certificates.list-by-user', [
                'certificates' => $certificates,
                'title' => __('certificate.user_certificates_title'),
                'metaDescription' => __('certificate.user_certificates_meta_description')
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to list user certificates', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')
                ->with('error', __('certificate.list_failed'));
        }
    }
}
