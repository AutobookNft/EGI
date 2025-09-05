#!/usr/bin/env python3
"""
📊 EGI Commit Statistics to Excel Converter
===========================================

Converte le statistiche dei commit in un file Excel ben formattato.
Analizza i commit per settimana dal 19 agosto 2025 (introduzione TAG system).

@author: AI Partner OS2.0-Compliant for Fabio Cherici
@version: 1.0.0 (FlorenceEGI MVP - Excel Export System)
@os2-pillars: Explicit,Coherent,Simple,Secure
"""

import subprocess
import pandas as pd
import re
from datetime import datetime, timedelta
import sys
import os
from pathlib import Path

class EGICommitStatsExporter:
    def __init__(self):
        self.git_repo_path = Path(__file__).parent.parent
        self.output_file = self.git_repo_path / "commit_statistics.xlsx"
        self.tag_patterns = {
            'FEAT': r'\[FEAT\]',
            'FIX': r'\[FIX\]', 
            'REFACTOR': r'\[REFACTOR\]',
            'DOC': r'\[DOC\]',
            'TEST': r'\[TEST\]',
            'CHORE': r'\[CHORE\]'
        }
        
    def run_git_command(self, command):
        """Esegue un comando git e ritorna l'output"""
        try:
            result = subprocess.run(
                command, 
                shell=True, 
                cwd=self.git_repo_path,
                capture_output=True, 
                text=True, 
                check=True
            )
            return result.stdout.strip()
        except subprocess.CalledProcessError as e:
            print(f"❌ Errore git command: {e}")
            return ""
    
    def get_commits_for_period(self, start_date, end_date):
        """Ottieni tutti i commit per un periodo specifico"""
        cmd = f'git log --oneline --since="{start_date}" --until="{end_date} 23:59:59"'
        output = self.run_git_command(cmd)
        
        if not output:
            return []
            
        commits = []
        for line in output.split('\n'):
            if line.strip():
                commits.append(line.strip())
        
        return commits
    
    def analyze_commits(self, commits):
        """Analizza i commit e categorizza per TAG"""
        stats = {
            'total_commits': len(commits),
            'tagged_commits': 0,
            'untagged_commits': 0,
            'tags': {tag: 0 for tag in self.tag_patterns.keys()}
        }
        
        for commit in commits:
            has_tag = False
            for tag, pattern in self.tag_patterns.items():
                if re.search(pattern, commit):
                    stats['tags'][tag] += 1
                    has_tag = True
                    break
            
            if has_tag:
                stats['tagged_commits'] += 1
            else:
                stats['untagged_commits'] += 1
        
        # Calcola percentuali
        if stats['total_commits'] > 0:
            stats['tag_coverage'] = round((stats['tagged_commits'] / stats['total_commits']) * 100, 1)
        else:
            stats['tag_coverage'] = 0
            
        return stats
    
    def get_daily_commits(self, start_date, end_date):
        """Ottieni commit giornalieri per il periodo"""
        daily_stats = []
        current_date = datetime.strptime(start_date, '%Y-%m-%d')
        end_dt = datetime.strptime(end_date, '%Y-%m-%d')
        
        while current_date <= end_dt:
            date_str = current_date.strftime('%Y-%m-%d')
            next_date = current_date + timedelta(days=1)
            next_date_str = next_date.strftime('%Y-%m-%d')
            
            cmd = f'git log --oneline --since="{date_str}" --until="{next_date_str}"'
            output = self.run_git_command(cmd)
            
            commits = [line.strip() for line in output.split('\n') if line.strip()]
            commit_count = len(commits)
            
            daily_stats.append({
                'date': date_str,
                'day_name': current_date.strftime('%A'),
                'commits': commit_count
            })
            
            current_date += timedelta(days=1)
            
        return daily_stats
    
    def generate_weekly_data(self):
        """Genera dati settimanali dal 19 agosto 2025"""
        weeks = [
            {
                'name': 'Settimana 1',
                'period': '19-25 Agosto 2025',
                'start_date': '2025-08-19',
                'end_date': '2025-08-25',
                'description': 'Introduzione TAG system'
            },
            {
                'name': 'Settimana 2', 
                'period': '26 Ago - 1 Set 2025',
                'start_date': '2025-08-26',
                'end_date': '2025-09-01',
                'description': 'Stabilizzazione'
            },
            {
                'name': 'Settimana 3',
                'period': '2-8 Settembre 2025',
                'start_date': '2025-09-02',
                'end_date': '2025-09-08',
                'description': 'Consolidamento (in corso)'
            }
        ]
        
        weekly_data = []
        all_daily_data = []
        
        for week in weeks:
            # Analisi settimanale
            commits = self.get_commits_for_period(week['start_date'], week['end_date'])
            stats = self.analyze_commits(commits)
            
            weekly_data.append({
                'Settimana': week['name'],
                'Periodo': week['period'],
                'Descrizione': week['description'],
                'Commit Totali': stats['total_commits'],
                'Commit con TAG': stats['tagged_commits'],
                'Commit senza TAG': stats['untagged_commits'],
                'Copertura TAG %': stats['tag_coverage'],
                'FEAT': stats['tags']['FEAT'],
                'FIX': stats['tags']['FIX'],
                'REFACTOR': stats['tags']['REFACTOR'],
                'DOC': stats['tags']['DOC'],
                'TEST': stats['tags']['TEST'],
                'CHORE': stats['tags']['CHORE'],
                'TAG Dominante': max(stats['tags'], key=stats['tags'].get) if any(stats['tags'].values()) else 'Nessuno'
            })
            
            # Dati giornalieri
            daily_data = self.get_daily_commits(week['start_date'], week['end_date'])
            for day in daily_data:
                day['settimana'] = week['name']
                all_daily_data.append(day)
        
        return weekly_data, all_daily_data
    
    def create_excel_file(self):
        """Crea il file Excel con tutti i dati"""
        print("📊 Generazione statistiche commit per Excel...")
        
        # Genera dati
        weekly_data, daily_data = self.generate_weekly_data()
        
        # Crea DataFrames
        df_weekly = pd.DataFrame(weekly_data)
        df_daily = pd.DataFrame(daily_data)
        
        # Dati di riepilogo
        total_commits = sum(week['Commit Totali'] for week in weekly_data)
        total_tagged = sum(week['Commit con TAG'] for week in weekly_data)
        avg_coverage = round(sum(week['Copertura TAG %'] for week in weekly_data) / len(weekly_data), 1)
        
        summary_data = [{
            'Metrica': 'Commit Totali',
            'Valore': total_commits,
            'Note': 'Dal 19 agosto 2025'
        }, {
            'Metrica': 'Commit con TAG',
            'Valore': total_tagged,
            'Note': f'{round((total_tagged/total_commits)*100, 1)}% del totale'
        }, {
            'Metrica': 'Giorni con TAG System',
            'Valore': (datetime.now() - datetime(2025, 8, 19)).days + 1,
            'Note': 'Dal 19 agosto 2025'
        }, {
            'Metrica': 'Copertura TAG Media',
            'Valore': f'{avg_coverage}%',
            'Note': 'Media delle 3 settimane'
        }]
        
        df_summary = pd.DataFrame(summary_data)
        
        # Scrivi file Excel
        with pd.ExcelWriter(self.output_file, engine='openpyxl') as writer:
            # Sheet 1: Riepilogo
            df_summary.to_excel(writer, sheet_name='Riepilogo', index=False)
            
            # Sheet 2: Dati Settimanali  
            df_weekly.to_excel(writer, sheet_name='Statistiche Settimanali', index=False)
            
            # Sheet 3: Dati Giornalieri
            df_daily.to_excel(writer, sheet_name='Commit Giornalieri', index=False)
            
            # Formattazione
            self.format_excel_sheets(writer)
        
        print(f"✅ File Excel creato: {self.output_file}")
        print(f"📁 Percorso completo: {self.output_file.absolute()}")
        
        return str(self.output_file.absolute())
    
    def format_excel_sheets(self, writer):
        """Formatta i fogli Excel"""
        from openpyxl.styles import Font, PatternFill, Alignment
        from openpyxl.utils.dataframe import dataframe_to_rows
        
        # Stili
        header_font = Font(bold=True, color='FFFFFF')
        header_fill = PatternFill(start_color='366092', end_color='366092', fill_type='solid')
        
        # Formatta ogni sheet
        for sheet_name in writer.sheets:
            ws = writer.sheets[sheet_name]
            
            # Header styling
            for cell in ws[1]:
                cell.font = header_font
                cell.fill = header_fill
                cell.alignment = Alignment(horizontal='center')
            
            # Auto-size columns
            for column in ws.columns:
                max_length = 0
                column_letter = column[0].column_letter
                
                for cell in column:
                    try:
                        if len(str(cell.value)) > max_length:
                            max_length = len(str(cell.value))
                    except:
                        pass
                
                adjusted_width = min(max_length + 2, 50)
                ws.column_dimensions[column_letter].width = adjusted_width

def main():
    """Funzione principale"""
    print("🚀 EGI Commit Statistics Excel Exporter")
    print("=" * 50)
    
    exporter = EGICommitStatsExporter()
    
    try:
        output_path = exporter.create_excel_file()
        print(f"\n🎉 Export completato con successo!")
        print(f"📊 File salvato in: {output_path}")
        
        # Verifica se il file esiste
        if os.path.exists(output_path):
            file_size = os.path.getsize(output_path)
            print(f"📁 Dimensione file: {file_size} bytes")
        
    except Exception as e:
        print(f"❌ Errore durante l'export: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
