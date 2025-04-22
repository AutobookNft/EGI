<?php

/**
 * 📜 Oracode Command: UltraSandboxCompiler
 *
 * @package         App\Console\Commands
 * @version         1.2.0 // Finalized signature, included authsandbox, refined exclusions
 * @author          Padmin D. Curtis (Adattato per Fabio Cherici - Sandbox App)
 * @copyright       2024 Fabio Cherici
 * @license         MIT
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File; // Usiamo Facade per semplicità
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * 🎯 Purpose: Compiles relevant parts of the Sandbox Laravel application source code
 *    (including the packages/ultra/authsandbox package, excluding others like lang, storage, etc.)
 *    into categorized text files and an enhanced semantic index according to Oracode principles.
 *
 * 🧱 Structure: Targets the application root, applies standard and specific exclusions,
 *    outputs to _Compiled directory within the sandbox.
 *
 * 🛠️ Usage: `php artisan ultrasandbox:compiler`
 */
class UltraSandboxCompiler extends Command // <-- Nome Classe Corretto
{
    /**
     * ✍️ The name and signature of the console command.
     * @var string
     */
    // --- CORREZIONE: Firma Comando ---
    protected $signature = 'ultrasandbox:compiler'; // <-- Firma come richiesto
    // --- FINE CORREZIONE ---

    /**
     * 💬 The console command description.
     * @var string
     */
    protected $description = 'Compile relevant Sandbox Application source (incl. authsandbox, with exclusions) into categorized files (Oracode).';

    // Definisci categorie e percorsi/estensioni
    // Rimuovi UUM, Aggiungi USM (authsandbox)
    protected array $categories = [
        'App Logic' => ['app/**/*.php'],
        'Bootstrap' => ['bootstrap/app.php', 'bootstrap/providers.php'],
        'Config' => ['config/**/*.php', '.env.example'],
        'Database (Migrations)' => ['database/migrations/**/*.php'],
        'Routes' => ['routes/**/*.php'],
        'Tests' => ['tests/**/*.php'],
        'Console' => ['app/Console/**/*.php', 'artisan'],
        'Client (Raw App)' => ['resources/ts/**/*.ts', 'resources/js/**/*.js', 'resources/css/**/*.css', 'resources/scss/**/*.scss'], // Client dell'App
        'Docs' => ['*.md', '*.txt'],
        'Metadata' => ['composer.json', 'package.json', 'vite.config.js', 'pint.json', '.gitignore', 'phpunit.xml', '*.stub'],
        // --- NUOVE Categorie per USM (authsandbox) ---
        'Package USM (Server)' => ['packages/ultra/authsandbox/src/**/*.php'],
        // Correggo i percorsi client per puntare a authsandbox
        'Package USM (Client)' => ['packages/ultra/authsandbox/resources/ts/**/*', 'packages/ultra/authsandbox/resources/js/**/*', 'packages/ultra/authsandbox/resources/css/**/*'],
        'Package USM (Config)' => ['packages/ultra/authsandbox/config/**/*.php'],
        'Package USM (Views)'  => ['packages/ultra/authsandbox/resources/views/**/*.blade.php'],
        'Package USM (Routes)' => ['packages/ultra/authsandbox/routes/**/*.php'],
        // Aggiungere qui altre categorie per altri pacchetti INTERNI alla sandbox se necessario
    ];

    // Estensioni da includere
    protected array $includedExtensions = [
        'php', 'ts', 'js', 'css', 'scss',
        'json', 'stub', 'md', 'txt', 'xml', 'gitignore',
    ];

    public function handle(Filesystem $filesystem): int
    {
        $projectCode = 'SANDBOX_APP';
        $basePath = "/home/fabio/sandbox/UltraUploadSandbox"; // Root della sandbox
        $outputPath = $basePath . DIRECTORY_SEPARATOR . "{$projectCode}_Compiled";
        $sharedPath = "/var/www/shared"; // Mantenuto

        if (!$filesystem->isDirectory($basePath)) { $this->error("Application directory not found: $basePath"); return self::FAILURE; }
        $filesystem->ensureDirectoryExists($outputPath);
        $filesystem->ensureDirectoryExists($sharedPath);

        $this->info("Cleaning previous compilation output in: $outputPath");
        $filesystem->cleanDirectory($outputPath);

        $timestamp = now()->format('Y-m-d H:i:s');
        $logFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_compilation_log.txt";
        $hashFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_file_hashes.txt";
        $prevHashFile = $basePath . DIRECTORY_SEPARATOR . ".{$projectCode}_hashes_prev.txt";
        $modifiedFile = $sharedPath . DIRECTORY_SEPARATOR . "{$projectCode}_modified_files.txt";
        $indexFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_semantic_index.json";

        $this->info("Starting Oracode compilation for: $projectCode (Application @ $basePath) - Applying Exclusions...");
        $filesystem->put($logFile, "Start APPLICATION compilation: " . $timestamp . "\nSource: " . $basePath);

        // Gestione hash precedente (copia l'attuale hash nell'output dir al _prev nella root dir)
        if ($filesystem->exists($hashFile)) { // Legge dall'output dir
            if ($filesystem->exists($prevHashFile)) $filesystem->delete($prevHashFile);
            $filesystem->copy($hashFile, $prevHashFile); // Copia da $output/$hashFile a $basePath/$prevHashFile
            $filesystem->append($logFile, "\nCurrent hash file copied to: $prevHashFile");
        } elseif($filesystem->exists($prevHashFile)) {
             $filesystem->append($logFile, "\nUsing existing previous hash file: $prevHashFile");
        } else {
             $filesystem->append($logFile, "\nNo previous hash file found at $prevHashFile.");
        }


        $categoryContents = [];
        $hashContent = "### Hash dei File Inclusi in $projectCode (relativi a $basePath) ###\n";
        $semanticIndex = [];
        $fileCount = 0;
        $unreadableCount = 0;
        $excludedPathsForLog = [];

        // --- Elenco File Specifici da Escludere nella Root ---
        $rootFilesToExclude = [
            'README.md',
            'package-lock.json',
            'server.php',
            'phpunit.xml', // Spesso si vuole escludere questo file di config test
            // Aggiungi qui altri file specifici nella root da escludere
            // Esempio: 'specifico_config_locale.yaml'
        ];
        // ------------------------------------------------------

        // Configura Finder con Esclusioni
        $finder = Finder::create()->files()
            ->in($basePath)
            // Esclusioni standard + quelle richieste
            ->exclude([
                'vendor',
                'node_modules',
                'storage', // ESCLUDI storage
                'public',
                'bootstrap/cache',
                'build',
                'dist',
                // Escludi cartella output stessa
                Str::after($outputPath, $basePath . DIRECTORY_SEPARATOR),
                // Esclusioni specifiche richieste
                'resources/lang',
                'lang',
                'app/Helpers',
                'resources/views/vendor',
                 // Escludi il vecchio UUM se per caso fosse ancora in packages E NON lo vuoi
                 'packages/ultra/uploadmanager' // Assicurati che non sia questo che vuoi includere!
            ])
            // Escludi singoli file basati su nome e posizione (root)
            ->filter(function (SplFileInfo $file) use ($basePath, &$excludedPathsForLog, $rootFilesToExclude) {
                $isRootFile = ($file->getPath() === $basePath);
                $filename = $file->getFilename();

                // Escludi se nella root E (inizia con 'U' O è nella lista specifica)
                if ($isRootFile && (Str::startsWith($filename, 'U') || in_array($filename, $rootFilesToExclude))) {
                    $excludedPathsForLog[] = $file->getRelativePathname();
                    return false; // Escludi
                }
                return true; // Altrimenti includi (verrà filtrato ulteriormente da estensioni)
            })
            ->ignoreDotFiles(false) // Include .env.example, .gitignore, etc. ma NON . / ..
            ->ignoreVCS(true)
            // Filtra per estensioni E file speciali dopo le esclusioni
            ->filter(function (SplFileInfo $file) {
                return in_array(strtolower($file->getExtension()), $this->includedExtensions)
                    || Str::endsWith($file->getFilename(), '.blade.php')
                    || in_array($file->getFilename(), ['artisan', '.env.example', 'pint.json', 'vite.config.js']); // Rimosso index.php e .env
            });


        // Log esclusioni specifiche applicate
        if(!empty($excludedPathsForLog)) {
            $filesystem->append($logFile, "\nSpecifically excluded root files: " . implode(', ', $excludedPathsForLog));
        }

        // Itera sui file filtrati
        foreach ($finder as $file) {
            $fileCount++;
            $realPath = $file->getRealPath();
            $appRelativePath = ltrim(Str::after($realPath, $basePath), DIRECTORY_SEPARATOR);

            if (!$file->isReadable()) { /* ... gestione illeggibili ... */ continue; }

            $hash = hash_file('sha256', $realPath);
            $hashContent .= "$appRelativePath: $hash\n";
            $category = $this->determineAppCategory($appRelativePath); // Usa la logica per l'app
            $content = $filesystem->get($realPath);

            if (!isset($categoryContents[$category])) {
                $categoryContents[$category] = "######## $projectCode - Categoria: $category ########\n\n";
            }
            $categoryContents[$category] .= "\n######## File: $appRelativePath ########\n\n$content\n\n";

            $semanticIndex[] = [ "file" => $appRelativePath, "category" => $category, "hash" => $hash ];
            $filesystem->append($logFile, "\nFile aggiunto a [$category]: $appRelativePath");
        }

        // Scrivi i file di output
        foreach ($categoryContents as $category => $content) {
            $categoryFilename = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_" . strtolower(str_replace([' ', '/','(',')'], '_', $category)) . "_code.txt";
            $filesystem->put($categoryFilename, $content);
            $filesystem->append($logFile, "\nWritten category file: $categoryFilename");
        }

        $filesystem->put($hashFile, $hashContent); // Scrive il NUOVO hash nell'output dir
        $filesystem->put($indexFile, json_encode($semanticIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Confronta NUOVO hash (hashFile nell'output) con il _prev (nella root)
        $this->compareHashesAndWriteModified($prevHashFile, $hashFile, $modifiedFile, $projectCode, $filesystem, $logFile);

        // Log finale
        $filesystem->append($logFile, "\n\nCompilation finished: " . now());
        $filesystem->append($logFile, "\nProcessed files: $fileCount");
        if ($unreadableCount > 0) $filesystem->append($logFile, "\nUnreadable files skipped: $unreadableCount");
        $this->info("Compilation $projectCode completed successfully.");
        if ($unreadableCount > 0) $this->warn("$unreadableCount files were not readable and skipped.");
        $this->comment("Output files generated in: $outputPath");
        $this->comment("Modified file list generated in: $sharedPath");

        return self::SUCCESS;
    }

    /**
     * Determina la categoria di un file (logica adattata per USM e nuove esclusioni).
     */
    protected function determineAppCategory(string $appRelativePath): string
    {
        $normalizedPath = str_replace(DIRECTORY_SEPARATOR, '/', $appRelativePath);

        // --- CORREZIONE: Controlla PRIMA il pacchetto authsandbox ---
        if (Str::startsWith($normalizedPath, 'packages/ultra/authsandbox/')) {
            $packageRelativePath = Str::after($normalizedPath, 'packages/ultra/authsandbox/');
            foreach ($this->categories as $category => $patterns) {
                 if (Str::startsWith($category, 'Package USM')) {
                     foreach ($patterns as $pattern) {
                        $packagePattern = Str::after($pattern, 'packages/ultra/authsandbox/');
                        if (Str::is($packagePattern, $packageRelativePath)) {
                            return $category;
                        }
                     }
                 }
            }
             // Fallback se non matcha categorie specifiche USM ma è dentro la cartella
             return 'Package USM (Misc)';
        }
        // --- FINE CORREZIONE ---


        // Casi speciali per file nella root (logica invariata)
        if (strpos($normalizedPath, '/') === false) {
            return match ($normalizedPath) {
                'artisan' => 'Console',
                'composer.json', 'package.json', 'vite.config.js', 'pint.json', '.gitignore' => 'Metadata', // Rimosso phpunit.xml se escluso
                 '.env.example' => 'Config',
                 default => Str::endsWith($normalizedPath, '.md') || Str::endsWith($normalizedPath, '.txt') ? 'Docs' : 'Misc',
            };
        }

        // Check categorie definite con pattern (esclusi i pacchetti gestiti sopra)
        foreach ($this->categories as $category => $patterns) {
             if (Str::startsWith($category, 'Package ')) continue; // Salta tutte le categorie di pacchetti

            foreach ($patterns as $pattern) {
                if (Str::is($pattern, $normalizedPath)) {
                    // Non servono più i controlli specifici qui perché le cartelle sono escluse dal Finder
                    return $category;
                }
            }
        }

        // Fallback finale
        return 'Misc';
    }

    // --- Metodi compareHashesAndWriteModified e parseHashFile INVARIATI ---
    protected function compareHashesAndWriteModified(string $prevHashFile, string $hashFile, string $modifiedFile, string $projectCode, Filesystem $filesystem, string $logFile): void
    {
         if (!$filesystem->exists($prevHashFile)) {
             $filesystem->put($modifiedFile, "# Elenco file modificati per $projectCode (nessun confronto precedente)\n\nNessun file hash precedente trovato: $prevHashFile");
             $filesystem->append(dirname($logFile) . DIRECTORY_SEPARATOR . basename($logFile), "\nModified file check: No previous hash file found at $prevHashFile.");
             return;
         }
         $modified = []; $prevHashes = $this->parseHashFile($prevHashFile, $filesystem); $currentHashes = $this->parseHashFile($hashFile, $filesystem); $logAppend = "";
         foreach ($currentHashes as $path => $hash) { if (!isset($prevHashes[$path]) || $prevHashes[$path] !== $hash) { $status = isset($prevHashes[$path]) ? 'MODIFIED' : 'ADDED'; $modified[] = "[$status] $path"; $logAppend .= "\n - [$status] $path"; } }
         foreach ($prevHashes as $path => $hash) { if (!isset($currentHashes[$path])) { $modified[] = "[REMOVED] $path"; $logAppend .= "\n - [REMOVED] $path"; } }
         if (empty($modified)) { $content = "# Elenco file modificati per $projectCode (nessuna modifica rilevata)\n\nNessun file modificato, aggiunto o rimosso da $prevHashFile."; $logAppend = "\nModified file check: No changes detected since last hash file."; } else { $content = "# Elenco file modificati per $projectCode\n\n" . implode("\n", $modified); $logAppend = "\nModified file check:" . $logAppend; }
         $filesystem->put($modifiedFile, $content); $filesystem->append(dirname($logFile) . DIRECTORY_SEPARATOR . basename($logFile), $logAppend);
    }
    protected function parseHashFile(string $filePath, Filesystem $filesystem): array
    {
         if (!$filesystem->exists($filePath)) { return []; }
         $lines = $filesystem->lines($filePath); $hashes = [];
         foreach ($lines as $line) { if (str_starts_with($line, '#') || trim($line) === '') continue; $parts = explode(': ', $line, 2); if (count($parts) === 2) $hashes[trim($parts[0])] = trim($parts[1]); }
         return $hashes;
    }

} // Fine classe AppCompileCommand
