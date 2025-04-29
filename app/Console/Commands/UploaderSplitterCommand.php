<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UploaderSplitterCommand extends Command
{
    /**
     * Il nome e la firma del comando console.
     *
     * @var string
     */
    protected $signature = 'uploader:split {source? : Il percorso del file sorgente} {destination? : Il percorso della directory di destinazione}';

    /**
     * La descrizione del comando console.
     *
     * @var string
     */
    protected $description = 'Suddivide il file compilato di UPLOADMANAGER in file di categoria';

    // Definizione delle categorie con i relativi pattern
    protected $categories = [
        'config' => [
            'pattern' => '/######## File: config\/(.+)\.php ########/',
            'filename' => 'config.txt'
        ],
        'controller' => [
            'pattern' => '/######## File: src\/Controllers\/([^\/]+)\.php ########/',
            'filename' => 'controller.txt'
        ],
        'controller_mail' => [
            'pattern' => '/######## File: src\/Controllers\/Mail\/(.+)\.php ########/',
            'filename' => 'controller_mail.txt'
        ],
        'controller_config' => [
            'pattern' => '/######## File: src\/Controllers\/Config\/(.+)\.php ########/',
            'filename' => 'controller_config.txt'
        ],
        'service' => [
            'pattern' => '/######## File: src\/Services\/(.+)\.php ########/',
            'filename' => 'service.txt'
        ],
        'job' => [
            'pattern' => '/######## File: src\/Jobs\/(.+)\.php ########/',
            'filename' => 'job.txt'
        ],
        'exception' => [
            'pattern' => '/######## File: src\/Exceptions\/(.+)\.php ########/',
            'filename' => 'exception.txt'
        ],
        'trait' => [
            'pattern' => '/######## File: src\/Traits\/(.+)\.php ########/',
            'filename' => 'trait.txt'
        ],
        'helper' => [
            'pattern' => '/######## File: src\/Helpers\/(.+)\.php ########/',
            'filename' => 'helper.txt'
        ],
        'provider' => [
            'pattern' => '/######## File: src\/Providers\/(.+)\.php ########/',
            'filename' => 'provider.txt'
        ],
        'mail' => [
            'pattern' => '/######## File: src\/Mail\/(.+)\.php ########/',
            'filename' => 'mail.txt'
        ],
        'handler' => [
            'pattern' => '/######## File: src\/Handlers\/(.+)\.php ########/',
            'filename' => 'handler.txt'
        ],
        'logging' => [
            'pattern' => '/######## File: Logging\/(.+)\.php ########/',
            'filename' => 'logging.txt'
        ],
        'event' => [
            'pattern' => '/######## File: src\/Events\/(.+)\.php ########/',
            'filename' => 'event.txt'
        ],
        'view' => [
            'pattern' => '/######## File: resources\/views\/(.+)\.blade\.php ########/',
            'filename' => 'view.txt'
        ],
        'route' => [
            'pattern' => '/######## File: routes\/(.+)\.php ########/',
            'filename' => 'route.txt'
        ],
        'console' => [
            'pattern' => '/######## File: src\/Console\/(.+)\.php ########/',
            'filename' => 'console.txt'
        ]
    ];

    /**
     * Esegue il comando console.
     *
     * @return int
     */
    public function handle()
    {
        // Ottenere i percorsi sorgente e destinazione
        $sourcePath = $this->argument('source') ?: '/home/fabio/libraries/UPLOADMANAGER_Compiled/UPLOADMANAGER_server_code.txt';
        $destPath = $this->argument('destination') ?: '/home/fabio/libraries/UPLOADMANAGER_Compiled/server';

        // Verificare se il file sorgente esiste
        if (!File::exists($sourcePath)) {
            $this->error("Il file sorgente {$sourcePath} non esiste!");
            return Command::FAILURE;
        }

        // Verificare se la directory di destinazione esiste, altrimenti crearla
        if (!File::exists($destPath)) {
            File::makeDirectory($destPath, 0755, true);
        }

        // Leggere il contenuto del file
        $content = File::get($sourcePath);

        $this->info("Elaborazione del file {$sourcePath} in corso...");

        // Estrai i marker di inizio di tutti i file nel documento
        $allMarkers = [];
        foreach ($this->categories as $categoryName => $categoryConfig) {
            preg_match_all($categoryConfig['pattern'], $content, $matches, PREG_OFFSET_CAPTURE);
            if (!empty($matches[0])) {
                foreach ($matches[0] as $index => $match) {
                    $allMarkers[] = [
                        'category' => $categoryName,
                        'filename' => $matches[1][$index][0],
                        'position' => $match[1],
                        'header' => $match[0],
                    ];
                }
            }
        }

        // Ordina i marker per posizione
        usort($allMarkers, function ($a, $b) {
            return $a['position'] - $b['position'];
        });

        // Per ogni marker, estrae il contenuto fino al prossimo marker
        $categoryContents = [];
        for ($i = 0; $i < count($allMarkers); $i++) {
            $marker = $allMarkers[$i];
            $startPos = $marker['position'] + strlen($marker['header']);
            $endPos = ($i < count($allMarkers) - 1) ? $allMarkers[$i + 1]['position'] : strlen($content);

            // Estrai il contenuto fino al prossimo marker
            $fileContent = trim(substr($content, $startPos, $endPos - $startPos));

            $category = $marker['category'];
            if (!isset($categoryContents[$category])) {
                $categoryContents[$category] = "# {$category}\n\n";
            }

            // Aggiungi il contenuto alla categoria
            $categoryContents[$category] .= "## {$marker['filename']}\n\n";
            $categoryContents[$category] .= $fileContent . "\n\n";
            $categoryContents[$category] .= "----------------------------------------\n\n";
        }

        // Salva i file di categoria
        $filesCreated = 0;
        foreach ($categoryContents as $category => $content) {
            $filename = $this->categories[$category]['filename'];
            $filePath = "{$destPath}/{$filename}";
            File::put($filePath, $content);
            $fileSizeKb = round(filesize($filePath) / 1024, 2);
            $this->line("  File categoria salvato: {$filePath} ({$category}) - {$fileSizeKb} KB");
            $filesCreated++;
        }

        $this->info("Estrazione completata. {$filesCreated} file categorizzati sono stati salvati in {$destPath}");

        return Command::SUCCESS;
    }
}
