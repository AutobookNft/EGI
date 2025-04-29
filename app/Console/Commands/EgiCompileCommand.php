<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class EgiCompileCommand extends Command
{
    protected $signature = 'egi:compile';

    protected $description = 'Compila il progetto EGI in file di testo categorizzati per l\'analisi AI secondo i principi Oracode.';

    // Directory da includere con le loro corrispondenti categorie
    protected array $directoryMap = [
        'bootstrap' => 'Bootstrap',
        'config' => 'Config',
        'database' => 'Database',
        'routes' => 'Routes',
    ];

    // File specifici nella root da includere
    protected array $rootFiles = [
        'composer.json',
        'package.json', // corretto da packages.json a package.json che è lo standard
        'vite.config.js',
        'tailwind.config.js',
        'helpers.php',
        'phpunit.xml',
        'postcss.config.js',
        'README.md',
        'tsconfig.json',
    ];

    // Sottocartelle di app da processare separatamente
    protected array $appSubdirectories = [
        'Console',
        'Exceptions',
        'Http',
        'Models',
        'Providers',
        'Services',
        'Repositories',
        'Traits',
        'Helpers',
        // Altre sottocartelle che potrebbero esistere in app/
    ];

    // Directory di resources da includere (escludendo /lang)
    protected array $resourcesDirectories = [
        'js',
        'css',
        'sass',
        'scss',
        'ts',
        'views',
    ];

    public function handle(Filesystem $filesystem): int
    {
        // Percorsi base
        $projectPath = '/home/fabio/EGI';
        $outputPath = '/home/fabio/EGI_src';

        // Verifica esistenza directory progetto
        if (!$filesystem->isDirectory($projectPath)) {
            $this->error("Directory del progetto non trovata: $projectPath");
            return self::FAILURE;
        }

        // Assicura esistenza directory output
        $filesystem->ensureDirectoryExists($outputPath);
        $filesystem->ensureDirectoryExists($outputPath . '/app');

        $timestamp = now()->format('Y-m-d H:i:s');
        $logFile = $outputPath . '/compilation_log.txt';
        $indexFile = $outputPath . '/semantic_index.json';

        $this->info("Iniziando compilazione Oracode per EGI -> Output: $outputPath");
        $filesystem->put($logFile, "Inizio compilazione: " . $timestamp);

        $semanticIndex = [];
        $fileCount = 0;
        $unreadableCount = 0;

        // 1. Processa i file della root specificati
        $this->processRootFiles($projectPath, $outputPath, $filesystem, $semanticIndex, $fileCount, $unreadableCount, $logFile);

        // 2. Processa le directory standard
        $this->processStandardDirectories($projectPath, $outputPath, $filesystem, $semanticIndex, $fileCount, $unreadableCount, $logFile);

        // 3. Processa resources (escludendo lang)
        $this->processResourcesDirectory($projectPath, $outputPath, $filesystem, $semanticIndex, $fileCount, $unreadableCount, $logFile);
        
        // 4. Processo speciale per la directory app e le sue sottocartelle
        $this->processAppDirectory($projectPath, $outputPath, $filesystem, $semanticIndex, $fileCount, $unreadableCount, $logFile);

        // Scrivi indice semantico
        $filesystem->put($indexFile, json_encode($semanticIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Log finale
        $filesystem->append($logFile, "\n\nCompilazione completata: " . now());
        $filesystem->append($logFile, "\nFile processati: $fileCount");
        if ($unreadableCount > 0) {
            $filesystem->append($logFile, "\nFile non leggibili saltati: $unreadableCount");
        }

        $this->info("Compilazione EGI completata con successo.");
        if ($unreadableCount > 0) {
            $this->warn("$unreadableCount file non leggibili sono stati saltati.");
        }
        $this->comment("File di output generati in: $outputPath");

        return self::SUCCESS;
    }

    /**
     * Processa i file specifici nella directory root
     */
    protected function processRootFiles(
        string $projectPath,
        string $outputPath,
        Filesystem $filesystem,
        array &$semanticIndex,
        int &$fileCount,
        int &$unreadableCount,
        string $logFile
    ): void {
        $category = 'Root';
        $categoryContent = "######## EGI - Categoria: $category ########\n\n";
        $categoryFilename = $outputPath . '/EGI_root_files.txt';
        
        foreach ($this->rootFiles as $filename) {
            $filePath = $projectPath . '/' . $filename;
            
            if ($filesystem->exists($filePath) && is_readable($filePath)) {
                $fileCount++;
                $content = $filesystem->get($filePath);
                $hash = hash_file('sha256', $filePath);
                
                $categoryContent .= "\n######## File: $filename ########\n\n$content\n\n";
                
                $semanticIndex[] = [
                    "file" => $filename,
                    "category" => $category,
                    "hash" => $hash,
                ];
                
                $filesystem->append($logFile, "\nFile aggiunto a [$category]: $filename");
            } else if ($filesystem->exists($filePath)) {
                $unreadableCount++;
                $errorMessage = "\n[WARNING] File non leggibile, saltato: $filename";
                $this->warn(substr($errorMessage, 1));
                $filesystem->append($logFile, $errorMessage);
            } else {
                $this->warn("File specificato non trovato: $filename");
                $filesystem->append($logFile, "\n[INFO] File specificato non trovato: $filename");
            }
        }
        
        $filesystem->put($categoryFilename, $categoryContent);
        $filesystem->append($logFile, "\nScritto file categoria: $categoryFilename");
    }

    /**
     * Processa le directory standard mappate
     */
    protected function processStandardDirectories(
        string $projectPath,
        string $outputPath,
        Filesystem $filesystem,
        array &$semanticIndex,
        int &$fileCount,
        int &$unreadableCount,
        string $logFile
    ): void {
        foreach ($this->directoryMap as $directory => $category) {
            $directoryPath = $projectPath . '/' . $directory;
            
            if (!$filesystem->isDirectory($directoryPath)) {
                $this->warn("Directory non trovata: $directory");
                $filesystem->append($logFile, "\n[INFO] Directory non trovata: $directory");
                continue;
            }
            
            $finder = Finder::create()->files()
                ->in($directoryPath)
                ->exclude(['node_modules', 'vendor'])
                ->name('*.php'); // Principalmente file PHP
            
            $categoryContent = "######## EGI - Categoria: $category ########\n\n";
            $hasFiles = false;
            
            foreach ($finder as $file) {
                $hasFiles = true;
                $fileCount++;
                $relativePath = $this->getRelativePath($file->getRealPath(), $projectPath);
                
                if (!$file->isReadable()) {
                    $unreadableCount++;
                    $errorMessage = "\n[WARNING] File non leggibile, saltato: $relativePath";
                    $this->warn(substr($errorMessage, 1));
                    $filesystem->append($logFile, $errorMessage);
                    continue;
                }
                
                $content = $filesystem->get($file->getRealPath());
                $hash = hash_file('sha256', $file->getRealPath());
                
                $categoryContent .= "\n######## File: $relativePath ########\n\n$content\n\n";
                
                $semanticIndex[] = [
                    "file" => $relativePath,
                    "category" => $category,
                    "hash" => $hash,
                ];
                
                $filesystem->append($logFile, "\nFile aggiunto a [$category]: $relativePath");
            }
            
            if ($hasFiles) {
                $categoryFilename = $outputPath . '/EGI_' . strtolower($category) . '_code.txt';
                $filesystem->put($categoryFilename, $categoryContent);
                $filesystem->append($logFile, "\nScritto file categoria: $categoryFilename");
            }
        }
    }

    /**
     * Processa la directory resources (escludendo /lang)
     */
    protected function processResourcesDirectory(
        string $projectPath,
        string $outputPath,
        Filesystem $filesystem,
        array &$semanticIndex,
        int &$fileCount,
        int &$unreadableCount,
        string $logFile
    ): void {
        $resourcesPath = $projectPath . '/resources';
        
        if (!$filesystem->isDirectory($resourcesPath)) {
            $this->warn("Directory resources non trovata");
            $filesystem->append($logFile, "\n[INFO] Directory resources non trovata");
            return;
        }
        
        $category = 'Resources';
        $categoryContent = "######## EGI - Categoria: $category ########\n\n";
        $hasFiles = false;
        
        foreach ($this->resourcesDirectories as $subDir) {
            $subPath = $resourcesPath . '/' . $subDir;
            
            if (!$filesystem->isDirectory($subPath)) {
                continue;
            }
            
            $finder = Finder::create()->files()
                ->in($subPath)
                ->exclude(['node_modules', 'vendor']);
            
            foreach ($finder as $file) {
                $hasFiles = true;
                $fileCount++;
                $relativePath = $this->getRelativePath($file->getRealPath(), $projectPath);
                
                if (!$file->isReadable()) {
                    $unreadableCount++;
                    $errorMessage = "\n[WARNING] File non leggibile, saltato: $relativePath";
                    $this->warn(substr($errorMessage, 1));
                    $filesystem->append($logFile, $errorMessage);
                    continue;
                }
                
                $content = $filesystem->get($file->getRealPath());
                $hash = hash_file('sha256', $file->getRealPath());
                
                $categoryContent .= "\n######## File: $relativePath ########\n\n$content\n\n";
                
                $semanticIndex[] = [
                    "file" => $relativePath,
                    "category" => $category,
                    "hash" => $hash,
                ];
                
                $filesystem->append($logFile, "\nFile aggiunto a [$category]: $relativePath");
            }
        }
        
        if ($hasFiles) {
            $categoryFilename = $outputPath . '/EGI_resources_code.txt';
            $filesystem->put($categoryFilename, $categoryContent);
            $filesystem->append($logFile, "\nScritto file categoria: $categoryFilename");
        }
    }

    /**
     * Processa la directory app e le sue sottocartelle in modo speciale
     */
    protected function processAppDirectory(
        string $projectPath,
        string $outputPath,
        Filesystem $filesystem,
        array &$semanticIndex,
        int &$fileCount,
        int &$unreadableCount,
        string $logFile
    ): void {
        $appPath = $projectPath . '/app';
        
        if (!$filesystem->isDirectory($appPath)) {
            $this->warn("Directory app non trovata");
            $filesystem->append($logFile, "\n[INFO] Directory app non trovata");
            return;
        }
        
        // File nella root di app
        $this->processAppRootFiles($appPath, $outputPath, $filesystem, $semanticIndex, $fileCount, $unreadableCount, $logFile);
        
        // Processa ogni sottodirectory come un file separato
        foreach ($this->appSubdirectories as $subDir) {
            $subPath = $appPath . '/' . $subDir;
            
            if (!$filesystem->isDirectory($subPath)) {
                continue;
            }
            
            $this->processAppSubdirectory($subDir, $subPath, $projectPath, $outputPath, $filesystem, $semanticIndex, $fileCount, $unreadableCount, $logFile);
        }
        
        // Cerca altre sottodirectory non specificate
        $directories = $filesystem->directories($appPath);
        foreach ($directories as $directory) {
            $dirName = basename($directory);
            if (!in_array($dirName, $this->appSubdirectories)) {
                $this->processAppSubdirectory($dirName, $directory, $projectPath, $outputPath, $filesystem, $semanticIndex, $fileCount, $unreadableCount, $logFile);
            }
        }
    }

    /**
     * Processa i file nella root della directory app
     */
    protected function processAppRootFiles(
        string $appPath,
        string $outputPath,
        Filesystem $filesystem,
        array &$semanticIndex,
        int &$fileCount,
        int &$unreadableCount,
        string $logFile
    ): void {
        $finder = Finder::create()->files()
            ->in($appPath)
            ->depth(0) // Solo file nella root, non nelle sottocartelle
            ->name('*.php');
        
        if ($finder->count() > 0) {
            $category = 'App/Root';
            $categoryContent = "######## EGI - Categoria: $category ########\n\n";
            
            foreach ($finder as $file) {
                $fileCount++;
                $relativePath = 'app/' . $file->getRelativePathname();
                
                if (!$file->isReadable()) {
                    $unreadableCount++;
                    $errorMessage = "\n[WARNING] File non leggibile, saltato: $relativePath";
                    $this->warn(substr($errorMessage, 1));
                    $filesystem->append($logFile, $errorMessage);
                    continue;
                }
                
                $content = $filesystem->get($file->getRealPath());
                $hash = hash_file('sha256', $file->getRealPath());
                
                $categoryContent .= "\n######## File: $relativePath ########\n\n$content\n\n";
                
                $semanticIndex[] = [
                    "file" => $relativePath,
                    "category" => $category,
                    "hash" => $hash,
                ];
                
                $filesystem->append($logFile, "\nFile aggiunto a [$category]: $relativePath");
            }
            
            $categoryFilename = $outputPath . '/app/EGI_app_root_files.txt';
            $filesystem->put($categoryFilename, $categoryContent);
            $filesystem->append($logFile, "\nScritto file categoria: $categoryFilename");
        }
    }

    /**
     * Processa una specifica sottodirectory di app
     */
    protected function processAppSubdirectory(
        string $subDirName,
        string $subDirPath,
        string $projectPath,
        string $outputPath,
        Filesystem $filesystem,
        array &$semanticIndex,
        int &$fileCount,
        int &$unreadableCount,
        string $logFile
    ): void {
        $finder = Finder::create()->files()
            ->in($subDirPath)
            ->exclude(['node_modules', 'vendor'])
            ->name('*.php');
        
        if ($finder->count() > 0) {
            $category = 'App/' . $subDirName;
            $categoryContent = "######## EGI - Categoria: $category ########\n\n";
            
            foreach ($finder as $file) {
                $fileCount++;
                $relativePath = $this->getRelativePath($file->getRealPath(), $projectPath);
                
                if (!$file->isReadable()) {
                    $unreadableCount++;
                    $errorMessage = "\n[WARNING] File non leggibile, saltato: $relativePath";
                    $this->warn(substr($errorMessage, 1));
                    $filesystem->append($logFile, $errorMessage);
                    continue;
                }
                
                $content = $filesystem->get($file->getRealPath());
                $hash = hash_file('sha256', $file->getRealPath());
                
                $categoryContent .= "\n######## File: $relativePath ########\n\n$content\n\n";
                
                $semanticIndex[] = [
                    "file" => $relativePath,
                    "category" => $category,
                    "hash" => $hash,
                ];
                
                $filesystem->append($logFile, "\nFile aggiunto a [$category]: $relativePath");
            }
            
            $safeSubDirName = strtolower(str_replace([' ', '/'], '_', $subDirName));
            $categoryFilename = $outputPath . '/app/EGI_app_' . $safeSubDirName . '_code.txt';
            $filesystem->put($categoryFilename, $categoryContent);
            $filesystem->append($logFile, "\nScritto file categoria: $categoryFilename");
        }
    }

    /**
     * Ottiene il percorso relativo di un file rispetto alla directory del progetto
     */
    protected function getRelativePath(string $realPath, string $basePath): string
    {
        return ltrim(Str::after($realPath, $basePath), '/');
    }
}
