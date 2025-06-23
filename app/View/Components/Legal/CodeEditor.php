<?php

namespace App\View\Components\Legal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CodeEditor extends Component
{
    public string $formattedContent;

    /**
     * Create a new component instance.
     *
     * @param array|null $content L'array dei termini da visualizzare.
     * @param string $name Il nome del campo del form (per la textarea).
     */
    public function __construct(
        public ?array $content = [],
        public string $name = 'content'
    ) {
        $this->formattedContent = $this->formatPhpArray($this->content ?? []);
    }

    /**
     * Formatta un array PHP in una stringa leggibile e ben indentata.
     *
     * @param array $data
     * @return string
     */
    private function formatPhpArray(array $data): string
    {
        // var_export ritorna una rappresentazione PHP valida di un array.
        // Lo processiamo per avere una formattazione più pulita.
        $exported = var_export($data, true);

        // Aggiusta la sintassi dell'array da array() a []
        $exported = preg_replace('/^array\s\(/m', '[', $exported);
        $exported = preg_replace('/^\)/m', ']', $exported);

        // Aggiusta l'indentazione
        $lines = explode("\n", $exported);
        $indentCount = 0;
        $result = '';
        foreach ($lines as $line) {
            if (str_contains($line, ']')) {
                $indentCount--;
            }
            $result .= str_repeat('    ', $indentCount) . ltrim($line) . "\n";
            if (str_contains($line, '[')) {
                $indentCount++;
            }
        }

        // Rimuove la prima riga e l'ultima parentesi graffa per avere solo l'array
        $result = trim(substr($result, strpos($result, '[') + 1));
        $result = trim(substr($result, 0, strrpos($result, ']')));


        return "return [\n" . trim($result) . "\n];";
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.legals.code-editor');
    }
}