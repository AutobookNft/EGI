<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * @package App\Rules
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP - Legal Domain Validation)
 * @date 2025-06-24
 *
 * @Oracode Rule: ValidLegalContent
 * 🎯 Purpose: Ensures that the legal content string is syntactically valid PHP and secure.
 * 🧱 Core Logic: Encapsulates syntax check (php -l) and forbidden function scanning.
 */
class ValidLegalContent implements Rule
{
    /**
     * @var string
     */
    private $errorMessage;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value The string content of the legal file.
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$this->isValidPHPSyntax($value)) {
            return false;
        }

        if ($this->containsDangerousFunctions($value)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage ?: 'Il contenuto fornito non è valido o sicuro.';
    }

    /**
     * Checks if the given string is valid PHP syntax.
     */
    private function isValidPHPSyntax(string $content): bool
    {
        // We assume the content is the body of a `return [...]` statement
        $fullContent = "<?php\n" . $content;
        $tempFile = tempnam(sys_get_temp_dir(), 'legal_syntax_check');
        file_put_contents($tempFile, $fullContent);

        exec("php -l {$tempFile} 2>&1", $output, $returnCode);

        unlink($tempFile);

        if ($returnCode !== 0) {
            $this->errorMessage = 'Errore di sintassi PHP nel contenuto: ' . implode(' ', $output);
            return false;
        }

        return true;
    }

    /**
     * Scans the content for a denylist of dangerous PHP functions.
     */
    private function containsDangerousFunctions(string $content): bool
    {
        $dangerousFunctions = [
            'exec', 'system', 'shell_exec', 'passthru', 'eval',
            'file_get_contents', 'file_put_contents', 'fopen', 'fwrite',
            'include', 'require', 'include_once', 'require_once',
            'popen', 'proc_open',
        ];

        foreach ($dangerousFunctions as $func) {
            // Use regex to avoid matching substrings within other words (e.g., 'eval' in 'evaluation')
            if (preg_match("/\b{$func}\b\s*\(/i", $content)) {
                $this->errorMessage = "Il contenuto include una funzione non permessa e potenzialmente pericolosa: {$func}().";
                return true;
            }
        }
        return false;
    }
}
