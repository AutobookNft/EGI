<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

/**
 * @package App\Console\Commands
 * @author AI Partner OS2.0-Compliant for Fabio Cherici  
 * @version 1.0.0 (FlorenceEGI MVP - Testing Time Tracker)
 * @os2-pillars Explicit,Coherent,Simple,Secure
 *
 * Comando per tracciare il tempo speso in testing empirico della piattaforma.
 * Risolve il problema del "tempo invisibile" non tracciato da WakaTime.
 */
class TestingTimeTracker extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testing:time {action : start, stop, report, status, or manual} {--note= : Nota opzionale per la sessione} {--duration= : Durata in minuti per sessione manuale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Traccia il tempo speso in testing empirico della piattaforma';

    private $logFile;
    private $activeFile;

    public function __construct() {
        parent::__construct();
        $this->logFile = storage_path('logs/testing_time.log');
        $this->activeFile = storage_path('logs/.testing_active');
    }

    /**
     * Execute the console command.
     */
    public function handle() {
        $action = $this->argument('action');

        switch ($action) {
            case 'start':
                return $this->startTesting();
            case 'stop':
                return $this->stopTesting();
            case 'report':
                return $this->showReport();
            case 'status':
                return $this->showStatus();
            case 'manual':
                return $this->addManualSession();
            default:
                $this->error('Azione non valida. Usa: start, stop, report, status, manual');
                return 1;
        }
    }

    private function startTesting() {
        if ($this->isTestingActive()) {
            $this->warn('⚠️  Una sessione di testing è già attiva!');
            return 1;
        }

        $now = Carbon::now();
        $note = $this->option('note') ?? 'Testing session';

        // Crea file di sessione attiva
        File::put($this->activeFile, json_encode([
            'start_time' => $now->toISOString(),
            'note' => $note,
            'git_branch' => trim(exec('git branch --show-current')),
            'git_commit' => trim(exec('git rev-parse --short HEAD'))
        ]));

        // Log inizio sessione
        $this->logActivity('TESTING_START', $note, $now);

        $this->info('🚀 Sessione di testing iniziata: ' . $now->format('H:i:s'));
        $this->info('📝 Nota: ' . $note);

        return 0;
    }

    private function stopTesting() {
        if (!$this->isTestingActive()) {
            $this->warn('⚠️  Nessuna sessione di testing attiva!');
            return 1;
        }

        $sessionData = json_decode(File::get($this->activeFile), true);
        $startTime = Carbon::parse($sessionData['start_time']);
        $endTime = Carbon::now();
        $duration = abs($endTime->diffInMinutes($startTime));

        // Log fine sessione
        $this->logActivity('TESTING_END', $sessionData['note'], $endTime, $duration);

        // Rimuovi file sessione attiva
        File::delete($this->activeFile);

        $this->info('⏹️  Sessione di testing terminata: ' . $endTime->format('H:i:s'));
        $this->info('⏱️  Durata: ' . $this->formatDuration($duration));
        $this->info('📝 Nota: ' . $sessionData['note']);

        return 0;
    }

    private function showStatus() {
        if ($this->isTestingActive()) {
            $sessionData = json_decode(File::get($this->activeFile), true);
            $startTime = Carbon::parse($sessionData['start_time']);
            $currentDuration = Carbon::now()->diffInMinutes($startTime);

            $this->info('🟢 Sessione di testing ATTIVA');
            $this->info('⏰ Iniziata: ' . $startTime->format('H:i:s'));
            $this->info('⏱️  Durata corrente: ' . $this->formatDuration($currentDuration));
            $this->info('📝 Nota: ' . $sessionData['note']);
            $this->info('🌿 Branch: ' . $sessionData['git_branch']);
        } else {
            $this->info('🔴 Nessuna sessione di testing attiva');
            $this->info('💡 Usa: php artisan testing:time start --note="Descrizione test"');
        }

        return 0;
    }

    private function showReport() {
        if (!File::exists($this->logFile)) {
            $this->warn('📊 Nessun dato di testing disponibile');
            return 1;
        }

        $this->info('📊 Report Testing Time - Ultimi 10 giorni');
        $this->info('==========================================');

        $logs = collect(explode("\n", File::get($this->logFile)))
            ->filter()
            ->map(function ($line) {
                return json_decode($line, true);
            })
            ->filter()
            ->filter(function ($log) {
                return Carbon::parse($log['timestamp'])->isAfter(Carbon::now()->subDays(10));
            });

        $sessions = $logs->where('action', 'TESTING_END');

        if ($sessions->isEmpty()) {
            $this->warn('📊 Nessuna sessione completata negli ultimi 10 giorni');
            return 1;
        }

        $totalMinutes = $sessions->sum('duration');
        $avgMinutes = $sessions->avg('duration');

        $this->table(
            ['Data', 'Inizio', 'Fine', 'Durata', 'Nota'],
            $sessions->map(function ($session) {
                $timestamp = Carbon::parse($session['timestamp']);
                $duration = abs($session['duration']); // Usa valore assoluto
                $startTime = $timestamp->subMinutes($duration);

                return [
                    $timestamp->format('d/m/Y'),
                    $startTime->format('H:i'),
                    $timestamp->format('H:i'),
                    $this->formatDuration($duration), // Usa durata assoluta
                    substr($session['note'], 0, 30) . (strlen($session['note']) > 30 ? '...' : '')
                ];
            })->toArray()
        );

        $totalMinutes = $sessions->sum(function ($session) {
            return abs($session['duration']);
        });
        $avgMinutes = $sessions->avg(function ($session) {
            return abs($session['duration']);
        });

        $this->info('📈 Statistiche:');
        $this->info('  • Sessioni totali: ' . $sessions->count());
        $this->info('  • Tempo totale: ' . $this->formatDuration($totalMinutes));
        $this->info('  • Media sessione: ' . $this->formatDuration($avgMinutes));
        $this->info('  • Tempo/giorno: ' . $this->formatDuration($totalMinutes / 10));

        return 0;
    }

    private function isTestingActive() {
        return File::exists($this->activeFile);
    }

    private function addManualSession() {
        $duration = $this->option('duration');
        $note = $this->option('note') ?? 'Manual testing session';

        if (!$duration || !is_numeric($duration) || $duration <= 0) {
            $this->error('❌ Devi specificare una durata valida in minuti con --duration=N');
            $this->info('💡 Esempio: php artisan testing:time manual --duration=22 --note="Test sistema notifiche"');
            return 1;
        }

        $duration = (int) $duration;
        $endTime = Carbon::now();
        $startTime = $endTime->copy()->subMinutes($duration);

        // Log sessione manuale come coppia start/stop
        $this->logActivity('TESTING_START', $note . ' (retroattivo)', $startTime);
        $this->logActivity('TESTING_END', $note . ' (retroattivo)', $endTime, $duration);

        $this->info('✅ Sessione di testing retroattiva registrata!');
        $this->info('🕐 Durata: ' . $this->formatDuration($duration));
        $this->info('📝 Nota: ' . $note);
        $this->info('⏰ Periodo: ' . $startTime->format('H:i:s') . ' - ' . $endTime->format('H:i:s'));

        return 0;
    }

    private function logActivity($action, $note, $timestamp, $duration = null) {
        $logData = [
            'timestamp' => $timestamp->toISOString(),
            'action' => $action,
            'note' => $note,
            'git_branch' => trim(exec('git branch --show-current')),
            'git_commit' => trim(exec('git rev-parse --short HEAD'))
        ];

        if ($duration !== null) {
            $logData['duration'] = $duration;
        }

        File::append($this->logFile, json_encode($logData) . "\n");
    }

    private function formatDuration($minutes) {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $mins);
        }

        return sprintf('%dm', $mins);
    }
}
