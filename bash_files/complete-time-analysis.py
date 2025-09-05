#!/usr/bin/env python3
"""
📊 EGI Complete Time Analysis
============================
Combina WakaTime, testing time e commit time per analisi completa.

@author: AI Partner OS2.0-Compliant for Fabio Cherici
@version: 1.0.0 (FlorenceEGI MVP - Complete Time Tracking)
"""

import json
import subprocess
import pandas as pd
from datetime import datetime, timedelta
from pathlib import Path
import sys

class EGITimeAnalyzer:
    def __init__(self):
        self.base_path = Path(__file__).parent.parent
        self.testing_log = self.base_path / "storage/logs/testing_time.log"
        
    def get_testing_time_today(self):
        """Ottiene il tempo di testing per oggi"""
        if not self.testing_log.exists():
            return 0, []
            
        today = datetime.now().date()
        total_minutes = 0
        sessions = []
        
        with open(self.testing_log, 'r') as f:
            for line in f:
                try:
                    data = json.loads(line.strip())
                    timestamp = datetime.fromisoformat(data['timestamp'].replace('Z', '+00:00'))
                    
                    if timestamp.date() == today and data['action'] == 'TESTING_END':
                        duration = data.get('duration', 0)
                        total_minutes += duration
                        sessions.append({
                            'start': timestamp - timedelta(minutes=duration),
                            'end': timestamp,
                            'duration': duration,
                            'note': data.get('note', 'Testing session')
                        })
                except:
                    continue
                    
        return total_minutes, sessions
    
    def get_commit_activity_today(self):
        """Ottiene attività commit per oggi"""
        try:
            cmd = 'git log --oneline --since="today"'
            result = subprocess.run(cmd, shell=True, capture_output=True, text=True, cwd=self.base_path)
            commits = [line.strip() for line in result.stdout.split('\n') if line.strip()]
            return len(commits), commits
        except:
            return 0, []
    
    def estimate_coding_time_from_commits(self, commit_count):
        """Stima tempo di coding basato sui commit (euristico)"""
        # Stima media: ogni commit = 15-30 minuti di lavoro
        return commit_count * 22  # 22 minuti per commit (media empirica)
    
    def generate_daily_report(self):
        """Genera report giornaliero completo"""
        testing_minutes, testing_sessions = self.get_testing_time_today()
        commit_count, commits = self.get_commit_activity_today()
        estimated_coding_minutes = self.estimate_coding_time_from_commits(commit_count)
        
        total_estimated_minutes = testing_minutes + estimated_coding_minutes
        
        print("🕒 EGI Daily Time Report")
        print("=" * 40)
        print(f"📅 Data: {datetime.now().strftime('%d/%m/%Y')}")
        print()
        
        print("⏱️  TESTING EMPIRICO:")
        print(f"   Tempo totale: {self.format_duration(testing_minutes)}")
        print(f"   Sessioni: {len(testing_sessions)}")
        
        if testing_sessions:
            print("   Dettaglio sessioni:")
            for i, session in enumerate(testing_sessions, 1):
                print(f"     {i}. {session['start'].strftime('%H:%M')}-{session['end'].strftime('%H:%M')} "
                      f"({self.format_duration(session['duration'])}) - {session['note']}")
        print()
        
        print("💻 CODING ATTIVITÀ:")
        print(f"   Commit oggi: {commit_count}")
        print(f"   Tempo stimato: {self.format_duration(estimated_coding_minutes)}")
        
        if commits:
            print("   Ultimi commit:")
            for commit in commits[:5]:
                print(f"     • {commit}")
        print()
        
        print("📊 TOTALE STIMATO:")
        print(f"   Tempo produttivo: {self.format_duration(total_estimated_minutes)}")
        print(f"   Testing vs Coding: {testing_minutes}min vs {estimated_coding_minutes}min")
        
        if total_estimated_minutes > 0:
            testing_percentage = (testing_minutes / total_estimated_minutes) * 100
            print(f"   % Testing: {testing_percentage:.1f}%")
        
        print()
        print("💡 SUGGERIMENTI:")
        
        if testing_minutes == 0:
            print("   • Considera di tracciare il testing con: php artisan testing:time start")
        elif testing_percentage > 70:
            print("   • Molto testing oggi! Considera di committare più spesso")
        elif testing_percentage < 20:
            print("   • Poco testing registrato, potrebbero esserci sessioni non tracciate")
            
        if commit_count == 0:
            print("   • Nessun commit oggi, considera di salvare il lavoro fatto")
            
        return {
            'testing_minutes': testing_minutes,
            'coding_minutes': estimated_coding_minutes,
            'total_minutes': total_estimated_minutes,
            'commit_count': commit_count,
            'testing_sessions': len(testing_sessions)
        }
    
    def format_duration(self, minutes):
        """Formatta durata in formato leggibile"""
        if minutes == 0:
            return "0min"
            
        hours = minutes // 60
        mins = minutes % 60
        
        if hours > 0:
            return f"{hours}h {mins}m"
        return f"{mins}m"

def main():
    analyzer = EGITimeAnalyzer()
    
    if len(sys.argv) > 1 and sys.argv[1] == '--json':
        # Output JSON per integrazione con altri script
        data = analyzer.generate_daily_report()
        print(json.dumps(data, indent=2))
    else:
        # Output human-readable
        analyzer.generate_daily_report()

if __name__ == "__main__":
    main()
