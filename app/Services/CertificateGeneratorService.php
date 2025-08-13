<?php

namespace App\Services;

use App\Models\EgiReservationCertificate;
use App\Models\Reservation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TCPDF;
use Ultra\ErrorManager\Facades\UltraError;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: CertificateGeneratorService
 * 🎯 Purpose: Generates and manages certificates for EGI reservations
 * 🧱 Core Logic: Creates certificates, signatures, and PDF documents
 * 🛡️ GDPR: Ensures minimal PII in certificates
 *
 * @package App\Services
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-16
 */
class CertificateGeneratorService
{
    /**
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     */
    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Generate a certificate for a reservation
     *
     * @param Reservation $reservation The reservation
     * @param array $additionalData Additional data for the certificate
     * @return EgiReservationCertificate The generated certificate
     * @throws \Exception If certificate generation fails
     *
     * @privacy-safe Creates certificates with minimal PII
     */
    public function generateCertificate(Reservation $reservation, array $additionalData = []): EgiReservationCertificate
    {
        try {
            // Create the certificate record
            $certificate = $reservation->createCertificate($additionalData);

            // Generate and store PDF
            $this->generatePdf($certificate);

            $this->logger->info('Certificate generated successfully', [
                'certificate_uuid' => $certificate->certificate_uuid,
                'reservation_id' => $reservation->id,
                'egi_id' => $reservation->egi_id
            ]);

            return $certificate;

        } catch (\Exception $e) {
            $this->logger->error('Failed to generate certificate', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id,
                'trace' => $e->getTraceAsString()
            ]);

            throw UltraError::handle('RESERVATION_CERTIFICATE_GENERATION_FAILED', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Generate a PDF for a certificate
     *
     * @param EgiReservationCertificate $certificate The certificate
     * @return bool Whether PDF generation was successful
     */
    public function generatePdf(EgiReservationCertificate $certificate): bool
    {
        try {
            // Create PDF filename
            $filename = 'certificate_' . $certificate->certificate_uuid . '.pdf';
            $path = 'certificates/' . Str::substr($certificate->certificate_uuid, 0, 2) . '/' . $filename;

            // We'll use a simple PDF generation approach for MVP
            // In a real implementation, you might want to use a more robust PDF library
            $pdf = $this->createPdfContent($certificate);

            // Store the PDF
            Storage::put($path, $pdf);

            // Update the certificate with the path
            $certificate->pdf_path = $path;
            $certificate->save();

            $this->logger->info('PDF generated for certificate', [
                'certificate_uuid' => $certificate->certificate_uuid,
                'path' => $path
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logger->error('Failed to generate PDF for certificate', [
                'error' => $e->getMessage(),
                'certificate_uuid' => $certificate->certificate_uuid,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Create PDF content for a certificate
     *
     * @param EgiReservationCertificate $certificate The certificate
     * @return string The PDF content
     */
    private function createPdfContent(EgiReservationCertificate $certificate): string
    {
        // For MVP, we'll use a simple approach with TCPDF
        // In a real implementation, you might want to use a template with more styling
        // This is a placeholder for the actual PDF generation logic

        // You'll need to add the TCPDF library to your project
        // composer require tecnickcom/tcpdf

        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('FlorenceEGI');
        $pdf->SetAuthor('FlorenceEGI Certificate Authority');
        $pdf->SetTitle('EGI Reservation Certificate');
        $pdf->SetSubject('Reservation Certificate for EGI #' . $certificate->egi_id);

        // Set default header data
        $pdf->SetHeaderData('', 0, 'FlorenceEGI Reservation Certificate', 'Certificate UUID: ' . $certificate->certificate_uuid);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // Add a page
        $pdf->AddPage();

        // Create certificate content
        $html = $this->generateHtmlForCertificate($certificate);

        // Print content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Generate QR code with verification URL
        $style = [
            'border' => 2,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => [0, 0, 0],
            'bgcolor' => [255, 255, 255],
            'module_width' => 1,
            'module_height' => 1
        ];

        $verificationUrl = route('egi-certificates.verify', $certificate->certificate_uuid);
        $pdf->write2DBarcode($verificationUrl, 'QRCODE,L', 150, 190, 40, 40, $style, 'N');

        // Close and return PDF content
        return $pdf->Output('', 'S');
    }

    /**
     * Generate HTML content for the certificate
     *
     * @param EgiReservationCertificate $certificate The certificate
     * @return string HTML content
     */
    private function generateHtmlForCertificate(EgiReservationCertificate $certificate): string
    {
        // Load certificate details
        $egi = $certificate->egi;
        $reservation = $certificate->reservation;

        // Build HTML content
        $html = '
        <style>
            .certificate {
                font-family: Arial, sans-serif;
                color: #333;
            }
            .header {
                font-size: 24px;
                font-weight: bold;
                color: #4a5568;
                text-align: center;
                margin-bottom: 20px;
            }
            .section {
                margin-bottom: 15px;
            }
            .label {
                font-weight: bold;
            }
            .value {
                margin-left: 10px;
            }
            .signature {
                margin-top: 30px;
                font-size: 12px;
                color: #718096;
            }
            .footer {
                text-align: center;
                font-size: 10px;
                color: #a0aec0;
                margin-top: 30px;
            }
            .badge {
                display: inline-block;
                padding: 5px 10px;
                border-radius: 5px;
                color: white;
                font-weight: bold;
            }
            .badge-strong {
                background-color: #4299e1;
            }
            .badge-weak {
                background-color: #ed8936;
            }
        </style>

        <div class="certificate">
            <div class="header">EGI Reservation Certificate</div>

            <div class="section">
                <span class="badge badge-' . $certificate->reservation_type . '">' .
                    ucfirst($certificate->reservation_type) . ' Reservation
                </span>
            </div>

            <div class="section">
                <span class="label">EGI Title:</span>
                <span class="value">' . htmlspecialchars($egi->title ?? 'Unknown EGI') . '</span>
            </div>

            <div class="section">
                <span class="label">Collection:</span>
                <span class="value">' . htmlspecialchars($egi->collection->collection_name ?? 'Unknown Collection') . '</span>
            </div>

            <div class="section">
                <span class="label">Wallet Address:</span>
                <span class="value">' . htmlspecialchars($certificate->wallet_address) . '</span>
            </div>

            <div class="section">
                <span class="label">Offer Amount:</span>
                <span class="value">€' . number_format($certificate->offer_amount_fiat, 2) . ' (' . number_format($certificate->offer_amount_algo, 8) . ' ALGO)</span>
            </div>

            <div class="section">
                <span class="label">Reservation Date:</span>
                <span class="value">' . $certificate->created_at->format('d M Y H:i:s') . '</span>
            </div>

            <div class="section">
                <span class="label">Certificate UUID:</span>
                <span class="value">' . $certificate->certificate_uuid . '</span>
            </div>

            <div class="signature">
                <p>This certificate is digitally signed by FlorenceEGI.</p>
                <p>Signature Hash: ' . $certificate->signature_hash . '</p>
                <p>To verify this certificate, visit:</p>
                <p>' . route('egi-certificates.verify', $certificate->certificate_uuid) . '</p>
            </div>

            <div class="footer">
                <p>This certificate confirms a reservation on the FlorenceEGI platform, a bridge between digital and ecological value.</p>
                <p>© ' . date('Y') . ' FlorenceEGI - All Rights Reserved</p>
            </div>
        </div>';

        return $html;
    }
}
