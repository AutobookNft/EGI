<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UltraMigrationsCompileCommand extends Command
{
    protected $signature = 'ultra_migrations:compile {module : The name of the Ultra module (e.g., UltraConfigManager)}';

    protected $description = 'Compile Ultra module migrations into a single text file with hash tracking.';

    // Percorso specifico per le migrations
    protected string $migrationsPath = 'database/migrations';

    public function handle(Filesystem $filesystem): int
    {
        $module = $this->argument('module');
        // Validazione input modulo
        // if (!Str::startsWith($module, 'Ultra')) {
        //     $this->error("Invalid module name: '$module'. Must start with 'Ultra'.");
        //     return self::FAILURE;
        // }

        // $projectCode = strtoupper(preg_replace('/^Ultra/', '', $module));
        $projectCode = strtoupper($module);
        $basePath = "/home/fabio" . DIRECTORY_SEPARATOR . $module;
        $outputPath = "/home/fabio/libraries" . DIRECTORY_SEPARATOR . "{$projectCode}_TestOutput";
        $sharedPath = "/var/www/shared";

        // Verifica esistenza directory modulo
        if (!$filesystem->isDirectory($basePath)) {
            $this->error("Module directory not found: $basePath");
            return self::FAILURE;
        }

        // Verifica esistenza directory migrations
        $migrationsFullPath = $basePath . DIRECTORY_SEPARATOR . $this->migrationsPath;
        if (!$filesystem->isDirectory($migrationsFullPath)) {
            $this->error("Migrations directory not found: $migrationsFullPath");
            return self::FAILURE;
        }

        // Assicura esistenza directory output
        $filesystem->ensureDirectoryExists($outputPath);
        $filesystem->ensureDirectoryExists($sharedPath);

        $timestamp = now()->format('Y-m-d H:i:s');
        $logFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_migrations_log.txt";
        $hashFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_migrations_hashes.txt";
        $prevHashFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_migrations_hashes_prev.txt";
        $modifiedFile = $sharedPath . DIRECTORY_SEPARATOR . "{$projectCode}_modified_migrations.txt";

        // File di output per le migrations
        $migrationsOutputFile = $outputPath . DIRECTORY_SEPARATOR . "{$projectCode}_migrations.txt";

        $this->info("Starting migrations compilation for module: $projectCode ($module)");
        $filesystem->put($logFile, "Start migrations compilation: " . $timestamp);

        // Gestione file hash precedente
        if ($filesystem->exists($hashFile)) {
            $filesystem->move($hashFile, $prevHashFile);
            $filesystem->append($logFile, "\nPrevious hash file moved to: $prevHashFile");
        } else {
             $filesystem->append($logFile, "\nNo previous hash file found.");
        }

        // Inizializza contenuti
        $migrationsContent = "######## $projectCode - Migrations ########\n\n";
        $hashContent = "### Hash delle Migrations Incluse in $projectCode ###\n";
        $fileCount = 0;
        $unreadableCount = 0;

        // Configura Finder specificamente per le migrations
        $finder = Finder::create()->files()
            ->in($migrationsFullPath)
            ->name('*.php');

        // Itera sui file migrations trovati
        foreach ($finder as $file) {
            $fileCount++;
            $realPath = $file->getRealPath();

            // Calcola percorso relativo alla directory migrations
            $migrationRelativePath = $file->getFilename();
            $moduleRelativePath = $this->migrationsPath . DIRECTORY_SEPARATOR . $migrationRelativePath;

            // Verifica leggibilità
            if (!$file->isReadable()) {
                $unreadableCount++;
                $errorMessage = "\n[WARNING] Migration file not readable, skipped: $migrationRelativePath";
                $this->warn(substr($errorMessage, 1));
                $filesystem->append($logFile, $errorMessage);
                continue;
            }

            // Calcola hash
            $hash = hash_file('sha256', $realPath);
            $hashContent .= "$moduleRelativePath: $hash\n";

            // Leggi contenuto
            $content = $filesystem->get($realPath);

            // Aggiungi contenuto al buffer delle migrations
            $migrationsContent .= "\n######## Migration: $migrationRelativePath ########\n\n$content\n\n";

            // Log aggiunta file
            $filesystem->append($logFile, "\nMigration added: $migrationRelativePath");
        }

        // Scrivi file output migrations
        $filesystem->put($migrationsOutputFile, $migrationsContent);
        $filesystem->append($logFile, "\nWritten migrations file: $migrationsOutputFile");

        // Scrivi file hash
        $filesystem->put($hashFile, $hashContent);

        // Confronta hash e scrivi file modificati
        $this->compareHashesAndWriteModified($prevHashFile, $hashFile, $modifiedFile, $projectCode, $filesystem, $logFile);

        // Log finale
        $filesystem->append($logFile, "\n\nCompilation finished: " . now());
        $filesystem->append($logFile, "\nProcessed migration files: $fileCount");
        if ($unreadableCount > 0) {
            $filesystem->append($logFile, "\nUnreadable migration files skipped: $unreadableCount");
        }

        $this->info("Migrations compilation for $projectCode completed successfully.");
        if ($unreadableCount > 0) {
             $this->warn("$unreadableCount migration files were not readable and skipped.");
        }
        $this->comment("Migrations output file generated: $migrationsOutputFile");
        $this->comment("Modified migrations list generated in: $sharedPath");

        return self::SUCCESS;
    }

    /**
     * Confronta i file hash e scrive l'elenco dei file modificati.
     */
    protected function compareHashesAndWriteModified(string $prevHashFile, string $hashFile, string $modifiedFile, string $projectCode, Filesystem $filesystem, string $logFile): void
    {
        if (!$filesystem->exists($prevHashFile)) {
            $filesystem->put($modifiedFile, "# Elenco migrations modificate per $projectCode (nessun confronto precedente)\n\nNessun file hash precedente trovato.");
            $filesystem->append($logFile, "\nModified migrations check: No previous hash file.");
            return;
        }

        $modified = [];
        $prevHashes = $this->parseHashFile($prevHashFile, $filesystem);
        $currentHashes = $this->parseHashFile($hashFile, $filesystem);
        $logAppend = "";

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
            $content = "# Elenco migrations modificate per $projectCode (nessuna modifica rilevata)\n\nNessuna migration modificata, aggiunta o rimossa dall'ultima compilazione.";
            $logAppend = "\nModified migrations check: No changes detected.";
        } else {
            $content = "# Elenco migrations modificate per $projectCode\n\n" . implode("\n", $modified);
            $logAppend = "\nModified migrations check:" . $logAppend;
        }

        $filesystem->put($modifiedFile, $content);
        $filesystem->append($logFile, $logAppend);
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
