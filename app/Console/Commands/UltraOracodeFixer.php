<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class UltraOracodeFixer extends Command
{
    protected $signature = 'ultra:oracode:fix {--module=UltraConfigManager}';

    protected $description = 'Apply Oracode UDP placeholders and fix issues detected by oracode:check';

    public function handle(): int
    {
        $module = $this->option('module');
        $basePath = "/home/fabio/libraries/{$module}";

        $this->info("Fixing Oracode compliance in module: $module");

        $files = collect(Finder::create()->files()
            ->in($basePath)
            ->exclude(['vendor', 'node_modules', 'storage', 'public', 'lang', 'tests/Browser'])
            ->ignoreDotFiles(true)
            ->name('*.php')
        );

        foreach ($files as $file) {
            $path = $file->getRealPath();
            $content = File::get($path);
            $lines = explode("\n", $content);
            $newLines = [];

            for ($i = 0; $i < count($lines); $i++) {
                $line = $lines[$i];
                $trimmed = trim($line);

                if (preg_match('/function\s+(\w+)\s*\(/', $line, $matches)) {
                    $method = $matches[1];
                    $hasDocblock = false;

                    for ($j = $i - 1; $j >= max(0, $i - 5); $j--) {
                        if (Str::contains(trim($lines[$j]), '/**')) {
                            $hasDocblock = true;
                            break;
                        }
                    }

                    // Inserisci docblock mancante
                    if (! $hasDocblock) {
                        $newLines[] = "/**";
                        $newLines[] = " * TODO: [UDP] Describe purpose of '{$method}'";
                        $newLines[] = " *";
                        $newLines[] = " * Semantic placeholder auto-inserted by Oracode.";
                        $newLines[] = " */";
                    } elseif (! Str::contains($lines[$i - 1], '@')) {
                        $newLines[] = "// TODO: Add semantic annotations (@param, @return) to '{$method}'";
                    }

                    if (Str::contains($method, 'test') && ! Str::contains($lines[$i - 1], '⛓️')) {
                        $newLines[] = "// TODO: ⛓️ Add Oracular signature to test '{$method}'";
                    }
                }

                $newLines[] = $line;
            }

            $newContent = implode("\n", $newLines);
            $backupPath = $path . '.oracode.php';
            File::put($backupPath, $newContent);

            $this->line("✔️ Annotated copy saved: $backupPath");
        }

        $this->info("Fix completato. Tutte le modifiche sono in file .oracode.php");
        return self::SUCCESS;
    }
}
