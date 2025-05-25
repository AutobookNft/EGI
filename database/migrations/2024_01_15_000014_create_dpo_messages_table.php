<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @Oracode Migration: DPO Messages Table
 * 🎯 Purpose: Direct communication with Data Protection Officer
 * 🛡️ Privacy: Secure DPO communication channel
 * 🧱 Core Logic: DPO request and response management
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
     * @privacy-safe Creates DPO communication table
     */
    public function up(): void
    {
        Schema::create('dpo_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Message details
            $table->string('subject', 255);
            $table->text('message');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('request_type', [
                'information',        // General information request
                'complaint',          // Privacy complaint
                'access_request',     // Formal access request
                'other'              // Other DPO-related request
            ]);

            // Communication status
            $table->enum('status', [
                'sent',              // Message sent to DPO
                'acknowledged',      // DPO acknowledged receipt
                'in_progress',       // DPO working on request
                'responded',         // DPO has responded
                'closed'            // Communication closed
            ])->default('sent');

            // Response tracking
            $table->text('dpo_response')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');

            // Follow-up
            $table->boolean('requires_followup')->default(false);
            $table->timestamp('followup_due_at')->nullable();
            $table->text('internal_notes')->nullable(); // DPO internal notes

            // Message metadata
            $table->json('metadata')->nullable(); // Request context, attachments, etc.

            // Timestamps
            $table->timestamps();

            // Indexes for DPO workflow
            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['request_type', 'status']);
            $table->index(['handled_by', 'status']);
            $table->index('acknowledged_at');
            $table->index('followup_due_at');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('dpo_messages');
    }
};
