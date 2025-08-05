<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Debugging PDF Generation in FlorenceEGI\n";
echo "=======================================\n\n";

// Test 1: Basic DomPDF functionality
echo "1. Testing basic DomPDF...\n";
try {
    $html = '<html><body><h1>Test PDF</h1><p>This is a test.</p></body></html>';
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
    $pdfContent = $pdf->output();

    echo "✓ DomPDF basic test: " . strlen($pdfContent) . " bytes\n";
    echo "✓ PDF header check: " . (substr($pdfContent, 0, 4) === '%PDF' ? 'VALID' : 'INVALID') . "\n";

    // Save test file
    file_put_contents('/tmp/test_basic.pdf', $pdfContent);
    echo "✓ Basic PDF saved to /tmp/test_basic.pdf\n\n";
} catch (\Exception $e) {
    echo "✗ DomPDF Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Complex HTML like DataExportService generates
echo "2. Testing complex HTML like DataExportService...\n";
try {
    $testData = [
        'profile' => [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ],
        'activities' => [
            ['action' => 'login', 'timestamp' => '2024-01-01 10:00:00'],
            ['action' => 'logout', 'timestamp' => '2024-01-01 11:00:00']
        ]
    ];

    // Replicate the exact HTML generation from DataExportService
    $html = '<!DOCTYPE html><html><head>';
    $html .= '<meta charset="UTF-8">';
    $html .= '<title>FlorenceEGI Data Export</title>';
    $html .= '<style>';
    $html .= 'body { font-family: Arial, sans-serif; margin: 20px; }';
    $html .= 'h1 { color: #333; border-bottom: 2px solid #ccc; }';
    $html .= 'h2 { color: #666; margin-top: 30px; }';
    $html .= 'table { width: 100%; border-collapse: collapse; margin: 10px 0; }';
    $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
    $html .= 'th { background-color: #f5f5f5; }';
    $html .= 'pre { background: #f9f9f9; padding: 10px; overflow-wrap: break-word; }';
    $html .= '</style>';
    $html .= '</head><body>';

    $html .= '<h1>FlorenceEGI Data Export</h1>';
    $html .= '<p><strong>Generated:</strong> ' . date('Y-m-d H:i:s') . '</p>';

    foreach ($testData as $section => $sectionData) {
        $html .= '<h2>' . ucfirst(str_replace('_', ' ', $section)) . '</h2>';

        if (is_array($sectionData)) {
            $first = reset($sectionData);

            if (is_array($first) && !empty($first)) {
                // Table format
                $html .= '<table>';
                $html .= '<thead><tr>';
                foreach (array_keys($first) as $header) {
                    $html .= '<th>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $header))) . '</th>';
                }
                $html .= '</tr></thead><tbody>';

                foreach ($sectionData as $row) {
                    $html .= '<tr>';
                    foreach ($row as $value) {
                        $html .= '<td>' . htmlspecialchars(is_array($value) ? json_encode($value) : (string)$value) . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>';
            } else {
                // Key-value pairs
                $html .= '<table>';
                foreach ($sectionData as $key => $value) {
                    $html .= '<tr>';
                    $html .= '<th>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) . '</th>';
                    $html .= '<td>' . htmlspecialchars(is_array($value) ? json_encode($value) : (string)$value) . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
        }
    }

    $html .= '</body></html>';

    echo "HTML generated (" . strlen($html) . " chars)\n";

    // Generate PDF
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
    $pdf->setPaper('A4', 'portrait');
    $pdfContent = $pdf->output();

    echo "✓ Complex PDF generated: " . strlen($pdfContent) . " bytes\n";
    echo "✓ PDF header check: " . (substr($pdfContent, 0, 4) === '%PDF' ? 'VALID' : 'INVALID') . "\n";

    // Save test file
    file_put_contents('/tmp/test_complex.pdf', $pdfContent);
    echo "✓ Complex PDF saved to /tmp/test_complex.pdf\n\n";
} catch (\Exception $e) {
    echo "✗ Complex PDF Error: " . $e->getMessage() . "\n";
    echo "✗ Error file: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}

// Test 3: Check DomPDF configuration
echo "3. Checking DomPDF configuration...\n";
$config = config('dompdf');
echo "DomPDF Config:\n";
print_r($config);

// Test 4: Try alternative PDF generation method
echo "\n4. Testing alternative PDF generation...\n";
try {
    $pdf = app('dompdf.wrapper');
    $pdf->loadHTML('<html><body><h1>Alternative Test</h1></body></html>');
    $pdfContent = $pdf->output();

    echo "✓ Alternative method: " . strlen($pdfContent) . " bytes\n";
    echo "✓ PDF header check: " . (substr($pdfContent, 0, 4) === '%PDF' ? 'VALID' : 'INVALID') . "\n";

    file_put_contents('/tmp/test_alternative.pdf', $pdfContent);
    echo "✓ Alternative PDF saved to /tmp/test_alternative.pdf\n\n";
} catch (\Exception $e) {
    echo "✗ Alternative method Error: " . $e->getMessage() . "\n\n";
}

echo "Debug complete. Check /tmp/test_*.pdf files to verify PDF validity.\n";