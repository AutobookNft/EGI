<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * @package App\Console\Commands
 * @author Padmin D. Curtis (AI Partner OS1.5.1-Compliant) for Fabio Cherici
 * @version 3.0.0 (FlorenceEGI MVP - Personal Data Domain)
 * @deadline 2025-06-30
 */
class UemLinterCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'uem:lint {--fix : Analizza e crea una copia con le correzioni semplici applicate}';

    /**
     * @var string
     */
    protected $description = 'Esegue una validazione strutturale e di valori su config/error-manager.php e opzionalmente corregge i problemi semplici.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("==============================================");
        $this->info(" UEM Linter & Fixer by Padmin D. Curtis (v3.0)");
        $this->info("==============================================\n");

        $configPath = config_path('error-manager.php');

        if (!File::exists($configPath)) {
            $this->error("File di configurazione non trovato in: $configPath");
            return Command::FAILURE;
        }

        // --- FASE 1: ANALISI COMPLETA ---
        $config = config('error-manager');
        $this->line("Caricato " . count($config['errors'] ?? []) . " codici di errore. Avvio analisi strutturale e di valori...");

        $issues = $this->analyzeConfiguration($config);

        $this->info("Analisi completata.\n");

        if (empty($issues)) {
            $this->info("✅ Perfetto! Nessuna non conformità strutturale o di valore trovata.");
        } else {
            $this->warn("⚠️  Rilevate " . count($issues) . " non conformità.");
            $this->table(
                ['Codice Errore', 'Anomalia Rilevata', 'Azione Suggerita'],
                $issues
            );
        }

        // --- FASE 2: CORREZIONE CHIRURGICA ---
        if ($this->option('fix')) {
            $this->line("\nInizio procedura di correzione chirurgica per i problemi semplici...");

            $originalContent = File::get($configPath);
            $correctedContent = $originalContent;

            $replacements = [
                "'blocking' => 'yes'" => "'blocking' => 'blocking'",
                "'blocking' => 'true'" => "'blocking' => 'blocking'",
                "'blocking' => '1'" => "'blocking' => 'blocking'",
                "'blocking' => 'no'" => "'blocking' => 'not'",
                "'blocking' => 'false'" => "'blocking' => 'not'",
                "'blocking' => '0'" => "'blocking' => 'not'",
            ];

            $correctionsCount = 0;
            foreach ($replacements as $search => $replace) {
                $correctedContent = str_replace($search, $replace, $correctedContent, $count);
                $correctionsCount += $count;
            }

            if ($correctionsCount > 0) {
                $outputPath = config_path('error-manager.corrected.php');
                File::put($outputPath, $correctedContent);
                $this->info("\n✅ Correzione completata. $correctionsCount sostituzioni effettuate.");
                $this->info("File corretto, con formattazione preservata, salvato in:");
                $this->comment($outputPath);
                $this->warn("\nRicorda: sono stati corretti solo i valori. Gli errori strutturali richiedono intervento manuale.");
            } else {
                $this->info("\nNessuna correzione automatica semplice da applicare.");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Analizza la configurazione e restituisce un array di problemi.
     */
    private function analyzeConfiguration(array $config): array
    {
        $issues = [];
        $errors = $config['errors'] ?? [];
        $allowedKeys = $this->getAllowedKeys();
        $requiredKeys = $this->getRequiredKeys();

        foreach ($errors as $errorCode => $definition) {
            if (!is_array($definition)) {
                $issues[] = [$errorCode, 'La definizione non è un array.', 'Intervento Manuale'];
                continue;
            }

            $definitionKeys = array_keys($definition);

            // *** CONTROLLO CHIAVI MANCANTI (ECCOLO!) ***
            $missingKeys = array_diff($requiredKeys, $definitionKeys);
            if (!empty($missingKeys)) {
                $issues[] = [$errorCode, 'Chiavi obbligatorie mancanti: ' . implode(', ', $missingKeys), 'Intervento Manuale'];
            }

            // CONTROLLO CHIAVI SCONOSCIUTE
            $unknownKeys = array_diff($definitionKeys, $allowedKeys);
            if (!empty($unknownKeys)) {
                $issues[] = [$errorCode, 'Chiavi sconosciute: ' . implode(', ', $unknownKeys), 'Intervento Manuale'];
            }

            // CONTROLLO VALORI 'blocking'
            if (isset($definition['blocking'])) {
                $originalBlocking = $definition['blocking'];
                $lowerBlocking = is_string($originalBlocking) ? strtolower(trim($originalBlocking)) : $originalBlocking;
                if (in_array($lowerBlocking, ['yes', 'true', '1', 'no', 'false', '0'])) {
                    $issues[] = [$errorCode, "Valore 'blocking' non standard: '$originalBlocking'.", 'Correggibile con --fix'];
                }
            }
        }
        return $issues;
    }

    private function getRequiredKeys(): array
    {
        return ['type', 'blocking', 'dev_message_key', 'user_message_key', 'http_status_code', 'devTeam_email_need', 'notify_slack', 'msg_to'];
    }

    private function getAllowedKeys(): array
    {
        return array_merge($this->getRequiredKeys(), ['recovery_action', 'log_level', 'category', 'sensitive_keys', 'notifiable', 'notifications']);
    }
}
