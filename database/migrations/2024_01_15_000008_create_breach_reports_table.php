<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: Breach Reports Table
 * 🎯 Purpose: User-reported security breaches and incidents
 * 🛡️ Privacy: Secure breach reporting with proper investigation tracking
 * 🧱 Core Logic: Breach report lifecycle with DPO integration
 *
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
return new class extends Migration
{
    /**
     * Run the migrations
     *
     * @return void
     * @privacy-safe Creates breach reporting table
     */
    public function up(): void
    {
        Schema::create('breach_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Report classification
            $table->enum('category', [
                'data_leak',           // Unauthorized data disclosure
                'unauthorized_access', // Account compromise
                'system_breach',       // Platform security breach
                'phishing',           // Phishing attempts
                'other'               // Other security concerns
            ]);

            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', [
                'reported',           // Initial report received
                'acknowledged',       // Report acknowledged by team
                'investigating',      // Under investigation
                'resolved',          // Investigation completed
                'dismissed',         // Report dismissed (false positive)
                'escalated'          // Escalated to authorities
            ])->default('reported');

            // Report content
            $table->text('description'); // User's description of the incident
            $table->timestamp('incident_date')->nullable(); // When incident occurred
            $table->json('affected_data')->nullable(); // Types of data potentially affected

            // Investigation details
            $table->json('report_data')->nullable(); // Additional structured data
            $table->text('investigation_notes')->nullable(); // DPO/admin notes
            $table->json('actions_taken')->nullable(); // Actions taken in response

            // Response tracking
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('investigation_started_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

            // Communication
            $table->boolean('user_notified')->default(false);
            $table->timestamp('user_notified_at')->nullable();
            $table->text('response_message')->nullable(); // Response sent to user

            // Timestamps
            $table->timestamps();

            // Indexes for breach management
            $table->index(['user_id', 'status']);
            $table->index(['category', 'severity']);
            $table->index(['status', 'created_at']);
            $table->index(['severity', 'status']);
            $table->index('incident_date');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('breach_reports');
    }
};
