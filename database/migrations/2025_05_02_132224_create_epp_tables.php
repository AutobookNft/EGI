<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the main EPPs table
        Schema::create('epps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 10)->index(); // ARF, APR, BPE
            $table->text('description');
            $table->string('image_path', 1024)->nullable();
            $table->string('status', 20)->default('active')->index(); // active, completed, pending
            $table->decimal('total_funds', 20, 2)->default(0);
            $table->decimal('target_funds', 20, 2)->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Create the EPP transactions table
        Schema::create('epp_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epp_id')->constrained('epps')->onDelete('cascade');
            $table->foreignId('egi_id')->nullable()->constrained('egis')->nullOnDelete();
            $table->foreignId('collection_id')->nullable()->constrained('collections')->nullOnDelete();
            $table->string('transaction_type', 20)->index(); // mint, rebind
            $table->decimal('amount', 20, 2);
            $table->string('blockchain_tx_id', 255)->nullable()->unique();
            $table->string('status', 20)->default('pending')->index(); // pending, confirmed, failed
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['epp_id', 'transaction_type']);
            $table->index(['epp_id', 'status']);
        });

        // Create the EPP milestones table
        Schema::create('epp_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epp_id')->constrained('epps')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('type', 20)->index(); // accomplishment, target, update
            $table->string('status', 20)->default('planned')->index(); // completed, in_progress, planned
            $table->decimal('target_value', 20, 2)->nullable();
            $table->decimal('current_value', 20, 2)->default(0);
            $table->string('evidence_url', 1024)->nullable();
            $table->string('evidence_type', 50)->nullable();
            $table->json('media')->nullable();
            $table->date('target_date')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['epp_id', 'status']);
            $table->index(['epp_id', 'type']);
        });

        // Add EPP relation to collections if not exists
        if (!Schema::hasColumn('collections', 'epp_id')) {
            Schema::table('collections', function (Blueprint $table) {
                $table->foreignId('epp_id')->nullable()->constrained('epps')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove EPP relation from collections if it was added
        if (Schema::hasColumn('collections', 'epp_id')) {
            Schema::table('collections', function (Blueprint $table) {
                $table->dropForeign(['epp_id']);
                $table->dropColumn('epp_id');
            });
        }

        // Drop the tables in reverse order of creation
        Schema::dropIfExists('epp_milestones');
        Schema::dropIfExists('epp_transactions');
        Schema::dropIfExists('epps');
    }
};
