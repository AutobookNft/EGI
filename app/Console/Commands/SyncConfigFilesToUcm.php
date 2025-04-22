<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Usiamo il logger standard di Laravel per semplicità nel comando
use Throwable;
use Ultra\UltraConfigManager\Constants\GlobalConstants;
use Ultra\UltraConfigManager\Dao\ConfigDaoInterface; // Importa l'interfaccia del DAO
use Ultra\UltraConfigManager\Exceptions\DuplicateKeyException;
use Ultra\UltraConfigManager\Exceptions\PersistenceException;
use Ultra\UltraConfigManager\Models\UltraConfigModel; // Necessario per type hint/check

/**
 * Comando Artisan per sincronizzare i file di configurazione con il database UCM.
 *
 * Legge i file .php dalla directory `config`, estrae le chiavi e i valori,
 * e li salva/aggiorna nel database utilizzando il DAO specificato (EloquentConfigDao).
 * Gli array vengono codificati in JSON prima del salvataggio.
 * Crea una nuova versione per ogni salvataggio/aggiornamento.
 */
class SyncConfigFilesToUcm extends Command
{
    /**
     * La firma del comando console.
     *
     * @var string
     */
    protected $signature = 'ucm:sync-config-files
                            {--category=application : La categoria UCM in cui salvare le configurazioni (es. application, system).}
                            {--force : Forza la sovrascrittura dei valori esistenti anche se non sono cambiati (NON implementato nel DAO per il check, aggiorna sempre).}';
                            // Nota: L'opzione force viene passata ma l'attuale DAO non la usa per *evitare* l'update se il valore è uguale. Aggiorna comunque.

    /**
     * La descrizione del comando console.
     *
     * @var string
     */
    protected $description = 'Sincronizza i file di configurazione standard di Laravel con il database di UltraConfigManager (UCM).';

    /**
     * Istanza del DAO per interagire con lo storage UCM.
     * @var ConfigDaoInterface
     */
    protected ConfigDaoInterface $configDao; // Usa l'interfaccia

    /**
     * Contatori per il riepilogo finale.
     */
    protected int $addedCount = 0;
    protected int $updatedCount = 0;
    protected int $skippedCount = 0; // Incrementato per chiavi non valide o valori non salvabili
    protected int $invalidKeyCount = 0;
    protected int $errorCount = 0;
    protected int $unchangedCount = 0; // Contatore per valori non cambiati (se implementato check)

    /**
     * Crea una nuova istanza del comando.
     *
     * Inietta il Config DAO necessario per la persistenza.
     *
     * @param ConfigDaoInterface $configDao Istanza del DAO che implementa ConfigDaoInterface.
     */
    public function __construct(ConfigDaoInterface $configDao) // Inietta l'interfaccia
    {
        parent::__construct();
        $this->configDao = $configDao;
    }

    /**
     * Esegue il comando console.
     *
     * @return int Codice di uscita del comando (0 per successo).
     */
    public function handle(): int
    {
        $this->info('🚀 Inizio sincronizzazione file di configurazione con UCM...');

        // 1. Ottieni e valida la categoria
        $category = $this->option('category');
        if (!$category || !in_array($category, ['application', 'system'])) {
            $this->error("❌ Categoria '--category' non valida. Usare 'application' o 'system'.");
            return Command::FAILURE;
        }
        $this->info("📁 Categoria selezionata: {$category}");

        // 2. Ottieni il percorso della directory config
        $configPath = config_path();
        if (!File::isDirectory($configPath)) {
            $this->error("❌ Directory di configurazione non trovata: {$configPath}");
            return Command::FAILURE;
        }
        $this->info("🔍 Scansione directory: {$configPath}");

        // 3. Trova tutti i file .php nella directory config
        $files = File::files($configPath);
        $phpFiles = array_filter($files, fn($file) => $file->getExtension() === 'php');

        if (empty($phpFiles)) {
            $this->warn("⚠️ Nessun file .php trovato nella directory di configurazione.");
            return Command::SUCCESS;
        }

        // Inizializza la barra di progresso
        $progressBar = $this->output->createProgressBar(count($phpFiles));
        $progressBar->start();

        // 4. Itera sui file e processali
        foreach ($phpFiles as $file) {
            $sourceFileName = $file->getFilename(); // Es: "app.php"
            $filePath = $file->getRealPath();
            $configKeyPrefix = $file->getFilenameWithoutExtension(); // Es: "app"

            $this->line(''); // Riga vuota per spaziatura
            $this->info("   📄 Processando file: {$sourceFileName} (Prefisso chiave: '{$configKeyPrefix}')");

            try {
                // Carica l'array di configurazione dal file
                // Usiamo @ per sopprimere errori se il file non è php valido, gestiamo dopo.
                $configArray = @include $filePath;

                if (!is_array($configArray)) {
                    $this->warn("   ⚠️ Il file '{$sourceFileName}' non restituisce un array valido. Saltato.");
                    $this->skippedCount++;
                    $progressBar->advance(); // Avanza comunque la barra
                    continue; // Salta al prossimo file
                }

                // Processa l'array ricorsivamente
                $this->processConfigArray($configArray, $configKeyPrefix, $category, $sourceFileName);

            } catch (Throwable $e) {
                $this->error("   ❌ Errore durante il processamento del file '{$sourceFileName}': " . $e->getMessage());
                Log::error("[ucm:sync] Errore processando {$sourceFileName}", [
                    'exception' => $e,
                    'file' => $filePath
                ]);
                $this->errorCount++;
            }

            $progressBar->advance(); // Avanza la barra di progresso
        }

        $progressBar->finish();
        $this->line(''); // Riga vuota finale

        // 5. Riepilogo
        $this->info('🏁 Sincronizzazione completata!');
        $this->info("   ✅ Aggiunte: {$this->addedCount}");
        $this->info("   🔄 Aggiornate: {$this->updatedCount}");
        $this->info("   ➖ Invariate: {$this->unchangedCount} (Nota: il check valore invariato dipende dal DAO)");
        $this->info("   ⏭️ Saltate (non array/scalari): {$this->skippedCount}");
        $this->info("   🚫 Chiavi non valide: {$this->invalidKeyCount}");
        $this->info("   ❌ Errori: {$this->errorCount}");

        return $this->errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Processa ricorsivamente un array di configurazione e salva i valori scalari o JSON nel DB.
     *
     * @param array $configArray L'array di configurazione da processare.
     * @param string $prefix Il prefisso corrente per le chiavi (es. 'app' o 'app.providers').
     * @param string $category La categoria UCM (es. 'application').
     * @param string $sourceFile Il nome del file di origine (es. 'app.php').
     * @return void
     */
    protected function processConfigArray(array $configArray, string $prefix, string $category, string $sourceFile): void
    {
        foreach ($configArray as $key => $value) {
            // Costruisce la chiave completa in notazione "dot"
            $fullKey = $prefix ? $prefix . '.' . $key : (string)$key;

             // Salta chiavi con caratteri problematici (es. '/')
             if (str_contains($fullKey, '/')) {
                 $this->warn("   ⚠️ Chiave '{$fullKey}' saltata: contiene caratteri non validi ('/').");
                 $this->invalidKeyCount++;
                 continue;
             }
             // Salta chiavi troppo lunghe (limite DB, es. 255)
             if (strlen($fullKey) > 255) {
                $this->warn("   ⚠️ Chiave '{$fullKey}' saltata: troppo lunga (> 255 caratteri).");
                $this->invalidKeyCount++;
                continue;
             }

            // **Logica Chiave: Gestione dei Tipi di Valore**
            $persistValue = null;
            $isPersistable = false;

            if (is_array($value)) {
                // --- SE È UN ARRAY ---
                // Codifica l'INTERO array come stringa JSON.
                $persistValue = json_encode($value);
                if ($persistValue === false) {
                     $this->warn("   ⚠️ Impossibile codificare in JSON il valore per la chiave '{$fullKey}'. Saltato. Errore: " . json_last_error_msg());
                     $this->skippedCount++;
                     continue;
                }
                $isPersistable = true;

            } elseif (is_scalar($value) || is_null($value)) {
                // --- SE È SCALARE (string, int, float, bool) o NULL ---
                $persistValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                $isPersistable = true;

            } else {
                // --- ALTRO TIPO (oggetto, risorsa, ecc.) ---
                $this->warn("   ⚠️ Valore per la chiave '{$fullKey}' non è scalare né array (tipo: " . gettype($value) . "). Saltato.");
                $this->skippedCount++;
                $isPersistable = false;
            }

            // Se il valore è persistibile, procedi al salvataggio/aggiornamento
            if ($isPersistable) {
                 // Prima di salvare, recupera il valore attuale per confronto (se non si usa --force)
                 // $forceUpdate = $this->option('force'); // Non usata attivamente dal DAO per il check
                 $existingModel = null;
                 $oldValue = null;

                 // Recupera il modello esistente per determinare l'azione (create/update) e il vecchio valore
                 // Non usiamo getConfigByKey perché potrebbe restituire null anche se esiste ma è soft-deleted
                 $existingModel = UltraConfigModel::withTrashed()->where('key', $fullKey)->first();
                 if ($existingModel) {
                     // Usiamo getOriginal per ottenere il valore prima del cast
                     // Nota: Questo valore potrebbe essere ancora criptato se il recupero non lo decripta
                     // Se il DAO gestisse il confronto internamente sarebbe meglio.
                     // Per ora, confrontiamo il valore in ingresso con quello che c'è (potenzialmente criptato)
                     // Il cast dovrebbe gestire la criptazione del nuovo valore prima del save
                     $oldValue = $existingModel->getOriginal('value');
                 }


                 // **DECISIONE IMPORTANTE:**
                 // L'attuale DAO `saveConfig` NON verifica se il valore è cambiato. Aggiorna sempre
                 // e crea versione/audit se richiesto. Questo va bene se vogliamo versionare
                 // ogni sync. Se vogliamo evitare versioni inutili, il DAO andrebbe modificato.
                 // Per ora, procediamo chiamando sempre saveConfig.
                 $valueChanged = true; // Assumiamo sempre cambiato per ora, o se $existingModel è null
                 // if ($existingModel && !$forceUpdate) {
                 //     // Qui andrebbe un confronto *affidabile* tra $persistValue e $oldValue (considerando crittografia)
                 //     // Questa logica è complessa qui, meglio nel DAO.
                 //     // $valueChanged = ($persistValue != $oldValue); // Confronto semplificato, probabilmente errato con crittografia
                 // }

                 if ($valueChanged) {
                    $this->persistValue($category, $fullKey, $persistValue, $sourceFile, $existingModel);
                 } else {
                     $this->line("      ↳ ➖ Invariata: '{$fullKey}'");
                     $this->unchangedCount++;
                 }
            }
        }
    }

    /**
     * Persiste un singolo valore di configurazione utilizzando il DAO iniettato.
     *
     * @param string $category Categoria UCM.
     * @param string $key Chiave di configurazione completa.
     * @param mixed $value Valore da salvare (scalare o stringa JSON per array).
     * @param string $sourceFile Nome del file di configurazione originale.
     * @param UltraConfigModel|null $existingModel Il modello esistente (anche trashed) o null se non esiste.
     * @return void
     */
    protected function persistValue(string $category, string $key, mixed $value, string $sourceFile, ?UltraConfigModel $existingModel): void
    {
        try {
            // Chiamiamo il metodo saveConfig del DAO
            // Passiamo null per userId (azione di sistema)
            // Passiamo true per createVersion (vogliamo versionare i cambiamenti dal sync)
            // Passiamo false per createAudit (il sync di per sé non è un'azione utente tracciata)
            // Passiamo null per oldValueForAudit (il DAO dovrebbe gestirlo se necessario, ma con createAudit=false non serve)
            $savedModel = $this->configDao->saveConfig(
                $key,
                $value,
                $category,
                $sourceFile,
                GlobalConstants::NO_USER,   // userId = null (system action)
                true,       // createVersion = true
                false,      // createAudit = false
                null        // oldValueForAudit = null (non serve con createAudit=false)
            );

            // Determina se è stato aggiunto o aggiornato in base a $existingModel
            if (!$existingModel) {
                $this->line("      ↳ ✅ Aggiunta: '{$key}' (ID: {$savedModel->id})");
                $this->addedCount++;
            } else {
                $this->line("      ↳ 🔄 Aggiornata: '{$key}' (ID: {$savedModel->id})");
                $this->updatedCount++;
            }

        } catch (DuplicateKeyException $e) {
             // Questa eccezione non dovrebbe verificarsi se il check `withTrashed()->where('key', ...)` funziona correttamente,
             // ma la gestiamo per sicurezza. Potrebbe indicare un race condition o un problema nel DAO.
             $this->error("   ❌ Errore: Chiave '{$key}' duplicata (gestione errore nel DAO potrebbe necessitare revisione). " . $e->getMessage());
             Log::error("[ucm:sync] Errore chiave duplicata inattesa per {$key}", ['exception' => $e]);
             $this->errorCount++;
        } catch (PersistenceException $e) {
             $this->error("   ❌ Errore DB durante salvataggio di '{$key}': " . $e->getMessage());
             Log::error("[ucm:sync] Errore DB salvando {$key}", ['exception' => $e]);
             $this->errorCount++;
        } catch (Throwable $e) {
             $this->error("   ❌ Errore critico durante il salvataggio della chiave '{$key}': " . $e->getMessage());
             Log::error("[ucm:sync] Errore critico salvando {$key}", ['exception' => $e]);
             $this->errorCount++;
        }
    }
}
