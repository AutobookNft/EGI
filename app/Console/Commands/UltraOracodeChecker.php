<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\File;

class UltraOracodeChecker extends Command
{
    protected $signature = 'ultra:oracode:check {--module=UltraConfigManager}';

    protected $description = 'Check PHP classes and methods for Oracode documentation compliance';

    public function handle(): int
    {
        $module = $this->option('module');
        $basePath = "/home/fabio/libraries/{$module}";

        $this->info("Checking Oracode compliance in module: $module");

        $files = collect(Finder::create()->files()
            ->in($basePath)
            ->exclude(['vendor', 'node_modules', 'storage', 'public', 'lang', 'tests/Browser'])
            ->ignoreDotFiles(true)
            ->name('*.php')
        );

        $issues = [];

        foreach ($files as $file) {
            $path = $file->getRealPath();
            $content = File::get($path);
            $lines = explode("\n", $content);
            $lineCount = count($lines);

            for ($i = 0; $i < $lineCount; $i++) {
                if (preg_match('/function\s+(\w+)\s*\(/', $lines[$i], $matches)) {
                    $methodName = $matches[1];
                    $hasDocblock = false;

                    for ($j = $i - 1; $j >= max(0, $i - 5); $j--) {
                        if (Str::contains(trim($lines[$j]), '/**')) {
                            $hasDocblock = true;
                            break;
                        }
                    }

                    if (! $hasDocblock) {
                        $issues[] = [
                            'file' => $path,
                            'line' => $i + 1,
                            'method' => $methodName,
                            'issue' => 'Missing docblock',
                        ];
                    } elseif (! Str::contains($lines[$i - 1], '@')) {
                        $issues[] = [
                            'file' => $path,
                            'line' => $i,
                            'method' => $methodName,
                            'issue' => 'Docblock without semantic annotation (@ or symbol)',
                        ];
                    }

                    if (Str::contains($lines[$i], 'test') && ! Str::contains($lines[$i - 1], '⛓️')) {
                        $issues[] = [
                            'file' => $path,
                            'line' => $i,
                            'method' => $methodName,
                            'issue' => 'Test method missing ⛓️ Oracular signature',
                        ];
                    }
                }
            }
        }

        if (empty($issues)) {
            $this->info('✅ Tutte le classi e i metodi rispettano i requisiti Oracode.');
        } else {
            $this->warn("Sono stati trovati " . count($issues) . " problemi:");
            foreach ($issues as $issue) {
                $this->line("[{$issue['file']}:{$issue['line']}] Method '{$issue['method']}': {$issue['issue']}");
            }
        }

        return self::SUCCESS;
    }
}
