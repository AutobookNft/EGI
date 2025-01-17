#!/usr/bin/env python3
import os
import re
import json
import hashlib
import logging
import time
from datetime import datetime
from typing import Dict, List, Optional
from pathlib import Path

class FileHasher:
    def __init__(self, max_retries: int = 3, retry_delay: float = 1.0):
        self.max_retries = max_retries
        self.retry_delay = retry_delay
        self._cache: Dict[str, str] = {}

    def calculate_hash(self, file_path: Path) -> Optional[str]:
        """
        Calcola l'hash di un file con retry in caso di errore
        """
        if str(file_path) in self._cache:
            return self._cache[str(file_path)]

        for attempt in range(self.max_retries):
            try:
                file_hash = hashlib.sha256()
                with file_path.open('rb') as f:
                    for block in iter(lambda: f.read(4096), b''):
                        file_hash.update(block)

                hash_value = file_hash.hexdigest()
                self._cache[str(file_path)] = hash_value
                return hash_value

            except (IOError, OSError) as e:
                if attempt == self.max_retries - 1:
                    logging.error(f"Failed to hash {file_path} after {self.max_retries} attempts: {e}")
                    return None
                time.sleep(self.retry_delay)

        return None

    def clear_cache(self) -> None:
        """Pulisce la cache degli hash"""
        self._cache.clear()

class CodeSensei:
    """
    Analizzatore di codice PHP e generatore di metriche
    """
    def __init__(self):
        # Directory di base e configurazione paths
        self.base_dir = Path("/home/fabio/EGI")
        self.app_dir = self.base_dir / "app"
        self.resources_dir = self.base_dir / "resources/views"
        self.resources_admin_dir = self.resources_dir / "admin"
        self.resources_livewire_dir = self.resources_dir / "livewire"
        self.resources_notifications_dir = self.resources_dir / "notifications"

        # File di output
        self.shared_dir = Path("/var/www/shared")
        self.output_file = Path("progetto_completo.php")
        self.log_file = Path("compilazione_log.txt")
        self.index_file = Path("indice.php")
        self.hash_file = Path("file_hashes.txt")
        self.prev_hash_file = Path("file_hashes_prev.txt")
        self.modified_files_file = self.shared_dir / "modified_files.txt"
        self.code_metrics_file = self.shared_dir / "code_metrics.md"

        # Inizializza FileHasher
        self.hasher = FileHasher(max_retries=3, retry_delay=1.0)

        # Configurazione logging
        logging.basicConfig(
            level=logging.INFO,
            format='%(asctime)s - %(message)s',
            handlers=[
                logging.FileHandler(self.log_file),
                logging.StreamHandler()
            ]
        )

        # Assicurati che la directory condivisa esista
        self.shared_dir.mkdir(parents=True, exist_ok=True)

    def calculate_complexity(self, file_path: Path) -> int:
        """Calcola la complessità ciclomatica del file"""
        patterns = [r'\bif\b', r'\belse\b', r'\bwhile\b', r'\bfor\b',
                   r'\bcase\b', r'&&', r'\|\|', r'\?:']
        try:
            content = file_path.read_text()
            return sum(len(re.findall(pattern, content)) for pattern in patterns)
        except Exception as e:
            logging.error(f"Errore nel calcolo della complessità per {file_path}: {e}")
            return 0

    def analyze_solid_principles(self, file_path: Path) -> Dict[str, int]:
        """Analizza i principi SOLID nel file"""
        try:
            content = file_path.read_text()
            return {
                'srp': 10 - min(9, content.count('class')),
                'ocp': 10 if re.search(r'extends|implements', content) else 0,
                'isp': 10 if 'interface' in content else 0,
                'dip': 10 if re.search(r'public function __construct.*?(Interface|Contract)', content) else 3
            }
        except Exception as e:
            logging.error(f"Errore nell'analisi SOLID per {file_path}: {e}")
            return {'srp': 0, 'ocp': 0, 'isp': 0, 'dip': 0}

    def assign_badges(self, file_path: Path) -> List[str]:
        """Assegna badge al file basati sulle caratteristiche"""
        badges = []
        try:
            content = file_path.read_text()
            if '/**' in content:
                badges.append("📚 Documentation Hero")
            if re.search(r'interface|extends|implements', content):
                badges.append("🏆 SOLID Master")
            if '⚡' in content:
                badges.append("⚡ Performance Optimizer")
        except Exception as e:
            logging.error(f"Errore nell'assegnazione badge per {file_path}: {e}")
        return badges

    def generate_hashes(self) -> Dict[str, str]:
        """Genera hash SHA256 per tutti i file PHP"""
        hashes = {}
        try:
            for path in self._get_php_files():
                hash_value = self.hasher.calculate_hash(path)
                if hash_value:
                    hashes[str(path)] = hash_value
                else:
                    logging.warning(f"Impossibile calcolare hash per: {path}")
        except Exception as e:
            logging.error(f"Errore nella generazione degli hash: {e}")
        return hashes

    def compare_hashes(self) -> List[Path]:
        """Confronta gli hash attuali con quelli precedenti per trovare i file modificati"""
        modified_files = []
        try:
            current_hashes = self.generate_hashes()
            logging.info(f"Hash generati per {len(current_hashes)} file")

            if self.prev_hash_file.exists():
                try:
                    with open(self.prev_hash_file, 'r') as f:
                        prev_hashes = json.load(f)
                    logging.info(f"Hash precedenti trovati: {len(prev_hashes)}")

                    modified_files = [
                        Path(file) for file, hash_value in current_hashes.items()
                        if file not in prev_hashes or prev_hashes[file] != hash_value
                    ]
                except json.JSONDecodeError as e:
                    logging.error(f"Errore nel leggere gli hash precedenti: {e}")
                    modified_files = [Path(file) for file in current_hashes.keys()]
            else:
                logging.info("Nessun file di hash precedenti trovato")
                modified_files = [Path(file) for file in current_hashes.keys()]

            # Salva gli hash correnti
            with open(self.hash_file, 'w') as f:
                json.dump(current_hashes, f, indent=2)

            # Backup per il prossimo confronto
            if self.hash_file.exists():
                import shutil
                shutil.copy2(self.hash_file, self.prev_hash_file)

            # Log dei file modificati
            with open(self.modified_files_file, 'w') as f:
                for file_path in modified_files:
                    f.write(f"{file_path}\n")

        except Exception as e:
            logging.error(f"Errore nel confronto degli hash: {e}")

        return modified_files

    def generate_summary(self) -> None:
        """Genera il sommario delle metriche del codice"""
        try:
            summary = [
                "# 📊 SOMMARIO SETTIMANALE",
                f"Data: {datetime.now().strftime('%Y-%m-%d %H:%M')}"
            ]

            # Statistiche generali
            total_files = len(list(self._get_php_files()))
            total_badges = sum(len(self.assign_badges(f)) for f in self._get_php_files())

            summary.extend([
                "\n## 📈 Metriche Generali",
                f"- Files Analizzati: {total_files}",
                f"- Badge Totali: {total_badges}",
            ])

            self.code_metrics_file.write_text('\n'.join(summary))
        except Exception as e:
            logging.error(f"Errore nella generazione del sommario: {e}")

    def process_files(self) -> None:
        """Processa tutti i file PHP modificati"""
        try:
            modified_files = self.compare_hashes()
            logging.info(f"File modificati trovati: {len(modified_files)}")

            if not modified_files:
                logging.warning("Nessun file modificato trovato")
                return

            with self.output_file.open('w', encoding='utf-8') as out:
                for file_path in modified_files:
                    logging.info(f"Elaborazione file: {file_path}")

                    try:
                        # Verifica che il file esista
                        if not file_path.exists():
                            logging.error(f"File non trovato: {file_path}")
                            continue

                        # Calcola metriche
                        complexity = self.calculate_complexity(file_path)
                        solid_scores = self.analyze_solid_principles(file_path)
                        badges = self.assign_badges(file_path)

                        # Scrivi nel file di output
                        out.write(f"\n<?php /* #### Inizio File: {file_path} #### */ ?>\n")
                        content = file_path.read_text(encoding='utf-8')
                        out.write(content)
                        logging.info(f"File scritto nell'output: {file_path}")

                        # Aggiorna metriche
                        with self.code_metrics_file.open('a', encoding='utf-8') as metrics:
                            metrics.write(f"\n\n## Analisi per {file_path.name}\n")
                            metrics.write(f"- Complessità: {complexity}\n")
                            for principle, score in solid_scores.items():
                                metrics.write(f"- {principle.upper()}: {score}/10\n")
                            if badges:
                                metrics.write(f"- Badge: {', '.join(badges)}\n")
                    except Exception as e:
                        logging.error(f"Errore nell'elaborazione del file {file_path}: {e}")
                        continue

            self.generate_summary()
            logging.info("Elaborazione completata con successo!")
        except Exception as e:
            logging.error(f"Errore durante l'elaborazione dei file: {e}")

    def _get_php_files(self) -> List[Path]:
        """Recupera tutti i file PHP nelle directory specificate"""
        php_files = []
        directories = [
            self.app_dir,
            self.resources_admin_dir,
            self.resources_livewire_dir,
            self.resources_notifications_dir
        ]

        logging.debug(f"Cercando file PHP in: {directories}")

        for directory in directories:
            if directory.exists():
                files = list(directory.glob('**/*.php'))
                php_files.extend(files)
                logging.debug(f"Trovati {len(files)} file in {directory}")

        logging.info(f"Totale file PHP trovati: {len(php_files)}")
        return php_files

    def run(self) -> None:
        """Esegue l'intero processo di analisi"""
        logging.info(f"Inizio analisi: {datetime.now()}")
        try:
            self.process_files()
            logging.info("Analisi completata con successo!")
        except Exception as e:
            logging.error(f"Errore durante l'esecuzione: {e}")

if __name__ == "__main__":
    sensei = CodeSensei()
    sensei.run()
