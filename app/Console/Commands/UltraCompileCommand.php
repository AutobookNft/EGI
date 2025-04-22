<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem; // Usare iniezione o Facade File
use Illuminate\Support\Facades\File; // Facade per comodità
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo; // Per type hint

class UltraCompileCommand extends Command
{
    protected $signature = 'ultra:compile {module : The name of the Ultra module (e.g., UltraConfigManager)}'; // Rimosso default, reso obbligatorio

    protected $description = 'Compile Ultra module source into categorized text files and enhanced semantic index according to Oracode principles.';

    // Definisci categorie e percorsi/estensioni associati
    protected array $categories = [
        'Server' => ['src/**/*.php'],
        'Client' => ['resources/ts/**/*.ts', 'resources/js/**/*.js', 'resources/css/**/*.css'],
        'Config' => ['config/**/*.php', '*.env'],
        'Views' => ['resources/views/**/*.blade.php'],
        'Lang' => ['resources/lang/**/*.php'],
        'Routes' => ['routes/**/*.php'],
        'Migrations' => ['database/migrations/**/*.php'],
        'Seeders' => ['database/Seeders/**/*.php'], // Corretto percorso Seeder con maiuscola
        'Tests' => ['tests/**/*.php'],
        'Docs' => ['*.md', '*.txt', 'docs/**/*.md', 'docs/**/*.txt'], // Aggiunto Docs
        'Metadata' => ['composer.json', 'package.json', '.gitignore', 'phpunit.xml', '*.stub'], // Aggiunto Metadata
        // 'Assets' => ['resources/images/*', 'resources/fonts/*'], // Categoria opzionale per assets binari (forse escluderli?)
    ];

    // Estensioni da includere nel Finder
    protected array $includedExtensions = [
        'php', 'ts', 'js', 'env', 'css', 'ico', 'txt', 'json', 'stub', 'md', 'xml', 'gitignore'
        // Escludi .blade.php qui, gestito da Views
    ];

    public function handle(Filesystem $filesystem): int // Inietta Filesystem per testabilità (opzionale, Facade ok)
    {
        $module = $this->argument('module');
        // Validazione input modulo (base)
        if (!Str::startsWith($module, 'Ultra')) {
            $this->error("Invalid module name: '$module'. Must start with 'Ultra'.");
            return self::FAILURE;
        }

        $projectCode = strtoupper(preg_replace('/^Ultra/', '', $module));
        // Usa DIRECTORY_SEPARATOR per portabilità
        $basePath = "/home/fabio/libraries" . DIRECTORY_SEPARATOR . $module;
        $outputPath = "/home/fabio/libraries" . DIRECTORY_SEPARATOR . "{$projectCode}_Compiled";
        $sharedPath = "/var/www/shared"; // Mantenuto come specificato

        // Verifica esistenza directory modulo
        if (!$filesystem->isDirectory($basePath)) {
            $this->error("Module directory not found: $basePath");
            return self::FAILURE;
        }

        // Assicura esistenza directory output
        $filesystem->ensureDirectoryExists($outputPath);
        $filesystem->ensureDirectoryExists($sharedPath); // Assicura anche shared path

        $timestamp = now()->format('Y-m-d H:i:s');
        $logFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_compilation_log.txt";
        $hashFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_file_hashes.txt";
        $prevHashFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_file_hashes_prev.txt";
        $modifiedFile = $sharedPath . DIRECTORY_SEPARATOR . "{$projectCode}_modified_files.txt"; // In shared
        $indexFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_semantic_index.json";

        $this->info("Starting Oracode compilation for module: $projectCode ($module)");
        $filesystem->put($logFile, "Start compilation: " . $timestamp);

        // Gestione file hash precedente
        if ($filesystem->exists($hashFile)) {
            $filesystem->move($hashFile, $prevHashFile);
            $filesystem->append($logFile, "\nPrevious hash file moved to: $prevHashFile");
        } else {
             $filesystem->append($logFile, "\nNo previous hash file found.");
        }

        // Inizializza contenuti per categorie e metadati
        $categoryContents = []; // Array per contenere il contenuto di ogni categoria
        $hashContent = "### Hash dei File Inclusi in $projectCode (relativi a $module) ###\n";
        $semanticIndex = [];
        $fileCount = 0;
        $unreadableCount = 0;

        // Configura Finder
        $finder = Finder::create()->files()
            ->in($basePath)
            ->exclude(['vendor', 'node_modules', 'storage', 'public', 'dist', 'build']) // Aggiunto dist/build
            ->ignoreDotFiles(false) // Include .gitignore, .env etc. ma non . e ..
            ->ignoreVCS(true) // Ignora .git, .svn etc.
            ->filter(function (SplFileInfo $file) {
                // Filtra per estensioni incluse
                return in_array(strtolower($file->getExtension()), $this->includedExtensions)
                    // Includi specificamente i file Blade
                    || Str::endsWith($file->getFilename(), '.blade.php');
            });

        // Itera sui file trovati
        foreach ($finder as $file) {
            $fileCount++;
            $realPath = $file->getRealPath();

            // Calcola percorso relativo alla directory base del modulo
            $moduleRelativePath = ltrim(Str::after($realPath, $basePath), DIRECTORY_SEPARATOR);

            // Verifica leggibilità
            if (!$file->isReadable()) {
                $unreadableCount++;
                $errorMessage = "\n[WARNING] File not readable, skipped: $moduleRelativePath";
                $this->warn(substr($errorMessage, 1)); // Mostra warning in console
                $filesystem->append($logFile, $errorMessage);
                continue; // Salta al prossimo file
            }

            // Calcola hash
            $hash = hash_file('sha256', $realPath);
            $hashContent .= "$moduleRelativePath: $hash\n";

            // Determina categoria in modo più granulare
            $category = $this->determineCategory($moduleRelativePath);

            // Leggi contenuto
            $content = $filesystem->get($realPath);

            // Inizializza contenuto categoria se non esiste
            if (!isset($categoryContents[$category])) {
                $categoryContents[$category] = "######## $projectCode - Categoria: $category ########\n\n";
            }

            // Aggiungi contenuto al buffer della categoria
            $categoryContents[$category] .= "\n######## File: $moduleRelativePath ########\n\n$content\n\n";

            // Prepara entry per indice semantico
            $semanticIndex[] = [
                "file" => $moduleRelativePath,
                "category" => $category,
                "hash" => $hash,
                // "size" => $file->getSize(), // Opzionale: dimensione file
                // "modified" => date('c', $file->getMTime()), // Opzionale: ultima modifica
            ];

            // Log aggiunta file
            $filesystem->append($logFile, "\nFile aggiunto a [$category]: $moduleRelativePath");
        }

        // Scrivi i file di output per ogni categoria popolata
        foreach ($categoryContents as $category => $content) {
            $categoryFilename = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_" . strtolower(str_replace([' ', '/'], '_', $category)) . "_code.txt";
            $filesystem->put($categoryFilename, $content);
            $filesystem->append($logFile, "\nWritten category file: $categoryFilename");
        }

        // Scrivi file hash e indice semantico
        $filesystem->put($hashFile, $hashContent);
        $filesystem->put($indexFile, json_encode($semanticIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); // Aggiunto JSON_UNESCAPED_SLASHES

        // Confronta hash e scrivi file modificati
        $this->compareHashesAndWriteModified($prevHashFile, $hashFile, $modifiedFile, $projectCode, $filesystem, $logFile);

        // Log finale
        $filesystem->append($logFile, "\n\nCompilation finished: " . now());
        $filesystem->append($logFile, "\nProcessed files: $fileCount");
        if ($unreadableCount > 0) {
            $filesystem->append($logFile, "\nUnreadable files skipped: $unreadableCount");
        }

        $this->info("Compilation $projectCode completed successfully.");
        if ($unreadableCount > 0) {
             $this->warn("$unreadableCount files were not readable and skipped.");
        }
        $this->comment("Output files generated in: $outputPath");
        $this->comment("Modified file list generated in: $sharedPath");

        return self::SUCCESS;
    }

    /**
     * Determina la categoria di un file basandosi sul suo percorso relativo al modulo.
     */
    protected function determineCategory(string $moduleRelativePath): string
    {
        $normalizedPath = str_replace(DIRECTORY_SEPARATOR, '/', $moduleRelativePath); // Normalizza i separatori

        foreach ($this->categories as $category => $patterns) {
            foreach ($patterns as $pattern) {
                if (Str::is($pattern, $normalizedPath)) {
                    return $category;
                }
            }
        }

        // Fallback per estensioni non coperte dai percorsi specifici
        $extension = strtolower(pathinfo($normalizedPath, PATHINFO_EXTENSION));
        return match ($extension) {
            'php' => 'Server', // PHP generico fuori da percorsi noti?
            'ts', 'js', 'css', 'scss', 'less', 'vue' => 'Client',
            'blade.php' => 'Views', // Riconferma blade
            'json', 'xml', 'yaml', 'yml', 'env', 'stub' => 'Metadata', // Config o metadata
            'md', 'txt' => 'Docs',
            default => 'Misc', // Categoria residuale per file non classificati
        };
    }

    /**
     * Confronta i file hash e scrive l'elenco dei file modificati.
     */
    protected function compareHashesAndWriteModified(string $prevHashFile, string $hashFile, string $modifiedFile, string $projectCode, Filesystem $filesystem, string $logFile): void
    {
        if (!$filesystem->exists($prevHashFile)) {
            $filesystem->put($modifiedFile, "# Elenco file modificati per $projectCode (nessun confronto precedente)\n\nNessun file hash precedente trovato.");
            $filesystem->append(dirname($logFile) . DIRECTORY_SEPARATOR . basename($logFile), "\nModified file check: No previous hash file."); // Log nel file di log principale
            return;
        }

        $modified = [];
        $prevHashes = $this->parseHashFile($prevHashFile, $filesystem);
        $currentHashes = $this->parseHashFile($hashFile, $filesystem);
        $logAppend = ""; // Stringa per log modifiche

        // File modificati o aggiunti
        foreach ($currentHashes as $path => $hash) {
            if (!isset($prevHashes[$path]) || $prevHashes[$path] !== $hash) {
                $status = isset($prevHashes[$path]) ? 'MODIFIED' : 'ADDED';
                $modified[] = "[$status] $path";
                $logAppend .= "\n - [$status] $path";
            }
        }

        // File rimossi
        foreach ($prevHashes as $path => $hash) {
            if (!isset($currentHashes[$path])) {
                $modified[] = "[REMOVED] $path";
                 $logAppend .= "\n - [REMOVED] $path";
            }
        }

        if (empty($modified)) {
            $content = "# Elenco file modificati per $projectCode (nessuna modifica rilevata)\n\nNessun file modificato, aggiunto o rimosso dall'ultima compilazione.";
             $logAppend = "\nModified file check: No changes detected.";
        } else {
            $content = "# Elenco file modificati per $projectCode\n\n" . implode("\n", $modified);
            $logAppend = "\nModified file check:" . $logAppend;
        }

        $filesystem->put($modifiedFile, $content);
        $filesystem->append(dirname($logFile) . DIRECTORY_SEPARATOR . basename($logFile), $logAppend); // Log nel file di log principale
    }

    /**
     * Legge un file hash e lo trasforma in un array associativo path => hash.
     */
    protected function parseHashFile(string $filePath, Filesystem $filesystem): array
    {
        if (!$filesystem->exists($filePath)) {
            return [];
        }

        $lines = $filesystem->lines($filePath);
        $hashes = [];
        foreach ($lines as $line) {
            // Salta header o righe vuote
            if (str_starts_with($line, '#') || trim($line) === '') {
                continue;
            }
            $parts = explode(': ', $line, 2);
            if (count($parts) === 2) {
                $hashes[trim($parts[0])] = trim($parts[1]);
            }
        }
        return $hashes;
    }
}
