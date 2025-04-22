<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ConvertPhpUnitTests extends Command
{
    protected $signature = 'ultra:convert-tests {--path=tests}';
    protected $description = 'Convert PHPUnit test classes to use attributes instead of deprecated docblocks (oracular style)';

    public function handle(): int
    {
        $basePath = base_path($this->option('path'));
        $this->info("Converting tests in: {$basePath}");

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));

        $count = 0;
        foreach ($rii as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') continue;

            $path = $file->getPathname();
            $original = file_get_contents($path);
            $converted = $this->convertTestContent($original);

            if ($converted !== $original) {
                file_put_contents($path, $converted);
                $this->info("✔ Converted: {$path}");
                $count++;
            }
        }

        $this->info("Conversion complete. Modified {$count} files.");
        return Command::SUCCESS;
    }

    protected function convertTestContent(string $content): string
    {
        // Remove deprecated docblock metadata
        $patterns = [
            '/@coversNothing\n?/',
            '/@covers\s+[^\n]*\n?/',
            '/@internal\n?/',
            '/@test\n?/',
        ];
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }

        // Remove empty docblocks
        $content = preg_replace('/\/\*\*\s*\*\/\s*/', '', $content);

        // Verificare gli use statements esistenti
        $useStatements = [
            'Test' => preg_match('/use\s+PHPUnit\\\\Framework\\\\Attributes\\\\Test;/', $content),
            'CoversNothing' => preg_match('/use\s+PHPUnit\\\\Framework\\\\Attributes\\\\CoversNothing;/', $content),
            'Group' => preg_match('/use\s+PHPUnit\\\\Framework\\\\Attributes\\\\Group;/', $content),
        ];

        // Flags per le modifiche da fare
        $useToAdd = [];
        $hasFullPathAttributes = false;
        $hasExpectException = preg_match('/\$this->expectException\(/', $content);
        $hasUltraErrorManager = preg_match('/UltraErrorManager|TestingConditions|UltraError/', $content);

        // Se ci sono expectException, dobbiamo aggiungere CoversNothing
        if ($hasExpectException && !$useStatements['CoversNothing']) {
            $useToAdd[] = 'CoversNothing';
        }

        // Se c'è UltraErrorManager, dobbiamo aggiungere Group
        if ($hasUltraErrorManager && !$useStatements['Group']) {
            $useToAdd[] = 'Group';
        }

        // Dividi il contenuto in linee
        $lines = explode("\n", $content);
        $newLines = [];
        $inClassDefinition = false;
        $classHasAttributes = false;

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            // Controlla se ci sono attributi nel formato completo
            if (preg_match('/#\[\\\\PHPUnit\\\\Framework\\\\Attributes\\\\Test\]/', $line)) {
                $hasFullPathAttributes = true;

                // Se abbiamo già l'use statement, convertiamo in #[Test]
                if ($useStatements['Test']) {
                    $line = str_replace('#[\PHPUnit\Framework\Attributes\Test]', '#[Test]', $line);
                }
            }

            // Controlla se siamo nella definizione della classe
            if (preg_match('/^\s*(final\s+)?class\s+(\w+Test)\s+extends/', $line)) {
                $inClassDefinition = true;

                // Rendi la classe final se non lo è già
                if (!preg_match('/^\s*final\s+class/', $line)) {
                    $line = preg_replace('/class\s+/', 'final class ', $line);
                }

                // Controlla se ci sono attributi sopra la classe
                $previousLine = $i > 0 ? trim($lines[$i-1]) : '';
                $classHasAttributes = preg_match('/#\[/', $previousLine);

                // Se non ci sono attributi e abbiamo bisogno di aggiungere attributi alla classe
                if (!$classHasAttributes) {
                    if ($hasExpectException && !$useStatements['CoversNothing']) {
                        array_push($newLines, "#[CoversNothing]");
                    }

                    if ($hasUltraErrorManager && !$useStatements['Group']) {
                        array_push($newLines, "#[Group('oracular')]");
                    }
                }
            }

            // Cerca metodi di test senza attributi
            if (preg_match('/^\s*public function (test\w+)\s*\((.*?)\)/', $line, $matches)) {
                // Controlla le linee precedenti per vedere se c'è già un attributo Test
                $previousLine = $i > 0 ? trim($lines[$i-1]) : '';
                $hasTestAttribute = preg_match('/#\[(?:\\\\PHPUnit\\\\Framework\\\\Attributes\\\\)?Test\]/', $previousLine);

                if (!$hasTestAttribute && !preg_match('/#\[/', $previousLine)) {
                    // Aggiungi l'attributo Test nella forma corretta
                    if ($useStatements['Test']) {
                        $newLines[] = "    #[Test]";
                    } else {
                        $newLines[] = "    #[\\PHPUnit\\Framework\\Attributes\\Test]";
                        if (!in_array('Test', $useToAdd)) {
                            $useToAdd[] = 'Test';
                        }
                    }
                }
            }

            $newLines[] = $line;
        }

        $content = implode("\n", $newLines);

        // Se abbiamo attributi nel formato completo e non c'è l'use statement
        if ($hasFullPathAttributes && !$useStatements['Test']) {
            if (!in_array('Test', $useToAdd)) {
                $useToAdd[] = 'Test';
            }

            // Dopo che abbiamo aggiunto l'use statement, possiamo convertire gli attributi
            if (in_array('Test', $useToAdd)) {
                $content = str_replace('#[\PHPUnit\Framework\Attributes\Test]', '#[Test]', $content);
            }
        }

        // Aggiungiamo tutti gli use statements necessari
        if (!empty($useToAdd)) {
            $useStatementBlock = '';
            foreach ($useToAdd as $use) {
                $useStatementBlock .= "use PHPUnit\\Framework\\Attributes\\{$use};\n";
            }

            // Inserisci gli use statements dopo il namespace
            if (preg_match('/namespace\s+[^;]+;/', $content, $matches)) {
                $namespaceStatement = $matches[0];
                $content = str_replace(
                    $namespaceStatement,
                    $namespaceStatement . "\n\n" . $useStatementBlock,
                    $content
                );
            } else {
                // Se non c'è namespace, aggiungi dopo <?php
                $content = preg_replace(
                    '/^<\?php\s+/s',
                    "<?php\n\n" . $useStatementBlock . "\n",
                    $content,
                    1
                );
            }
        }

        return $content;
    }
}
