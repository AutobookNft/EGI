/ ================================================
// 3. PDF TEMPLATE (resources/views/gdpr/pdf/policy-template.blade.php)
// ================================================

?>
<!DOCTYPE html>
<html lang="{{ $language }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $policy->title }} - {{ $platform_name }}</title>
    <style>
        /* FlorenceEGI PDF Styling OS2 */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #2c3e50;
            background: #ffffff;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .meta-info {
            background: #f8f9fa;
            padding: 20px 40px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .meta-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-info td {
            padding: 5px 0;
            font-size: 10px;
        }

        .meta-info .label {
            font-weight: 600;
            width: 150px;
            color: #495057;
        }

        .content {
            padding: 0 40px;
            margin-bottom: 40px;
        }

        .content h1, .content h2, .content h3 {
            color: #2c3e50;
            margin-top: 25px;
            margin-bottom: 15px;
            page-break-after: avoid;
        }

        .content h1 {
            font-size: 18px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 8px;
        }

        .content h2 {
            font-size: 16px;
            color: #495057;
        }

        .content h3 {
            font-size: 14px;
            color: #6c757d;
        }

        .content p {
            margin-bottom: 12px;
            text-align: justify;
        }

        .content ul, .content ol {
            margin: 15px 0 15px 25px;
        }

        .content li {
            margin-bottom: 6px;
        }

        .content strong {
            color: #2c3e50;
            font-weight: 600;
        }

        .content em {
            color: #667eea;
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 15px 40px;
            font-size: 9px;
            color: #6c757d;
        }

        .footer .left {
            float: left;
        }

        .footer .right {
            float: right;
        }

        .page-break {
            page-break-before: always;
        }

        /* FlorenceEGI Brand Elements */
        .brand-accent {
            color: #667eea;
            font-weight: 600;
        }

        .env-badge {
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media print {
            .footer {
                position: fixed;
                bottom: 0;
            }

            .content {
                margin-bottom: 80px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $policy->title }}</h1>
        <div class="subtitle">
            {{ $platform_name }} - Rinascimento Digitale Sostenibile
            <span class="env-badge">🌱 Carbon Negative</span>
        </div>
    </div>

    <!-- Meta Information -->
    <div class="meta-info">
        <table>
            <tr>
                <td class="label">Versione:</td>
                <td>{{ $policy->version }}</td>
                <td class="label">Tipo Documento:</td>
                <td>{{ ucfirst(str_replace('_', ' ', $policy->document_type)) }}</td>
            </tr>
            <tr>
                <td class="label">Data Effettiva:</td>
                <td>{{ $policy->effective_date->format('d/m/Y') }}</td>
                <td class="label">Lingua:</td>
                <td>{{ strtoupper($policy->language) }}</td>
            </tr>
            <tr>
                <td class="label">Stato:</td>
                <td class="brand-accent">{{ ucfirst($policy->status) }}</td>
                <td class="label">Generato il:</td>
                <td>{{ $generated_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Contatto Privacy:</td>
                <td colspan="3">{{ $contact_email }}</td>
            </tr>
        </table>
    </div>

    <!-- Policy Content -->
    <div class="content">
        {!! \Illuminate\Mail\Markdown::parse($policy->content) !!}
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="left">
            © {{ date('Y') }} {{ $platform_name }} | Documento generato automaticamente
        </div>
        <div class="right">
            Pagina <span class="pagenum"></span> | {{ $platform_url }}
        </div>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Inter, DejaVu Sans", "normal");
                $size = 9;
                $pageText = "Pagina " . $PAGE_NUM . " di " . $PAGE_COUNT;
                $pdf->text(520, 820, $pageText, $font, $size);
            ');
        }
    </script>
</body>
</html>
