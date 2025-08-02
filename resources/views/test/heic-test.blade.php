<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEIC/HEIF Upload Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .upload-area {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .result {
            background: #f5f5f5;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .error {
            background: #fee;
            border: 1px solid #fcc;
        }

        .success {
            background: #efe;
            border: 1px solid #cfc;
        }

        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        button {
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🧪 HEIC/HEIF Upload Test Tool</h1>
        <p><strong>Scopo:</strong> Testare upload di file HEIC/HEIF per debugging.</p>

        <div class="upload-area">
            <input type="file" id="fileInput" accept=".heic,.heif,image/heic,image/heif">
            <br><br>
            <button onclick="testUpload()">Test Upload</button>
            <button onclick="downloadTestFile()">Download Test HEIC</button>
        </div>

        <div id="results"></div>
    </div>

    <script>
        function testUpload() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];

            if (!file) {
                showResult('error', 'Seleziona un file prima');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            showResult('info', `Testing file: ${file.name} (${file.type || 'no MIME'}, ${file.size} bytes)`);

            fetch('/test/heic-upload', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult('success', 'Test completato!', data);
                    } else {
                        showResult('error', 'Test fallito', data);
                    }
                })
                .catch(error => {
                    showResult('error', 'Errore di rete', {
                        error: error.message
                    });
                });
        }

        function showResult(type, message, data = null) {
            const results = document.getElementById('results');
            const div = document.createElement('div');
            div.className = `result ${type}`;

            let content = `<h3>${message}</h3>`;
            if (data) {
                content += `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            }

            div.innerHTML = content;
            results.appendChild(div);
            results.scrollTop = results.scrollHeight;
        }

        function downloadTestFile() {
            // Create a minimal HEIC-like test file
            const testContent = new Uint8Array([
                0x00, 0x00, 0x00, 0x1C, 0x66, 0x74, 0x79, 0x70, // ftyp header
                0x68, 0x65, 0x69, 0x63, 0x00, 0x00, 0x00, 0x00, // heic
                0x68, 0x65, 0x69, 0x63, 0x6D, 0x69, 0x66, 0x31 // heic mif1
            ]);

            const blob = new Blob([testContent], {
                type: 'image/heic'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'test_image.heic';
            a.click();
            URL.revokeObjectURL(url);

            showResult('info', 'File di test HEIC scaricato. Prova ad usarlo per il test.');
        }
    </script>
</body>

</html>
