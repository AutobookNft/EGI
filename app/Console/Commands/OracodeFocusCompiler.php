<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File; // Use File facade
use Illuminate\Support\Str;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException; // For specific exception handling

class OracodeFocusCompiler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Accepts one or more file paths as arguments
    protected $signature = 'oracode:focus {module : The name of the Ultra module (e.g., UltraConfigManager)} {files* : Relative path(s) to the file(s) within the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts specific file contents from an Ultra module into a single focus file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        $relativeFiles = $this->argument('files'); // This is an array of file paths

        // Basic module name validation
        if (!Str::startsWith($module, 'Ultra')) {
            $this->error("Invalid module name: '$module'. Must start with 'Ultra'.");
            return self::FAILURE;
        }
        if (empty($relativeFiles)) {
             $this->error("No file paths provided.");
             return self::FAILURE;
        }

        $projectCode = strtoupper(preg_replace('/^Ultra/', '', $module));
        $basePath = "/home/fabio/libraries" . DIRECTORY_SEPARATOR . $module; // Module source path
        $outputPathDir = "/home/fabio/libraries" . DIRECTORY_SEPARATOR . "{$projectCode}_FocusOutput"; // Dedicated output directory
        $outputFileName = $outputPathDir . DIRECTORY_SEPARATOR . "{$projectCode}_focus_update.txt"; // Output file name

        // Ensure module directory exists
        if (!File::isDirectory($basePath)) {
            $this->error("Module directory not found: $basePath");
            return self::FAILURE;
        }

        // Ensure output directory exists
        File::ensureDirectoryExists($outputPathDir);

        $this->info("Starting Oracode Focus compilation for module: $projectCode");
        $this->line("Module Path: $basePath");
        $this->line("Output File: $outputFileName");
        $this->line("Requested Files:");
        foreach ($relativeFiles as $relFile) {
             $this->line("- " . $relFile);
        }

        // --- Prepare Output Content ---
        $outputContent = "######## Oracode Focus Update - $projectCode ########\n";
        $outputContent .= "# Timestamp: " . now()->format('Y-m-d H:i:s') . "\n";
        $outputContent .= "# Requested Files: " . implode(', ', $relativeFiles) . "\n\n";

        $filesProcessed = 0;
        $filesNotFound = 0;
        $filesUnreadable = 0;

        // Process each requested file
        foreach ($relativeFiles as $relativeFile) {
            $fullPath = $basePath . DIRECTORY_SEPARATOR . ltrim($relativeFile, DIRECTORY_SEPARATOR);

            // Check if file exists
            if (!File::exists($fullPath) || !File::isFile($fullPath)) {
                $warningMsg = "[WARNING] File not found or is not a file: $relativeFile";
                $this->warn($warningMsg);
                $outputContent .= "######## File: $relativeFile ########\n\n";
                $outputContent .= "$warningMsg\n\n";
                $filesNotFound++;
                continue; // Skip to the next file
            }

            // Check if file is readable (though File::get handles this generally)
            if (!File::isReadable($fullPath)) {
                 $warningMsg = "[WARNING] File not readable: $relativeFile";
                 $this->warn($warningMsg);
                 $outputContent .= "######## File: $relativeFile ########\n\n";
                 $outputContent .= "$warningMsg\n\n";
                 $filesUnreadable++;
                 continue; // Skip to the next file
            }

            // Read file content
            try {
                $content = File::get($fullPath);
                $outputContent .= "######## File: $relativeFile ########\n\n";
                $outputContent .= $content;
                $outputContent .= "\n\n"; // Add spacing between files
                $filesProcessed++;
            } catch (\Exception $e) {
                 // Catch potential read errors although File::get is usually robust
                 $errorMsg = "[ERROR] Failed to read file: $relativeFile - " . $e->getMessage();
                 $this->error($errorMsg);
                 $outputContent .= "######## File: $relativeFile ########\n\n";
                 $outputContent .= "$errorMsg\n\n";
                 $filesUnreadable++; // Count as unreadable/error
            }
        }

        // Write the combined content to the output file
        try {
             File::put($outputFileName, $outputContent);
        } catch (\Exception $e) {
             $this->error("Failed to write output file: $outputFileName - " . $e->getMessage());
             return self::FAILURE;
        }


        // Final Summary
        $this->info("\nOracode Focus compilation completed.");
        $this->line(" - Files Processed: $filesProcessed");
        if ($filesNotFound > 0) {
             $this->warn(" - Files Not Found: $filesNotFound");
        }
        if ($filesUnreadable > 0) {
             $this->warn(" - Unreadable/Errored Files: $filesUnreadable");
        }
        $this->comment("Output file generated: $outputFileName");

        return self::SUCCESS;
    }
}
