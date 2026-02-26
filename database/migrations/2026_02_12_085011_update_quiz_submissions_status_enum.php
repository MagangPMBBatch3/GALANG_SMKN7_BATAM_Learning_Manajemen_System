<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the status enum to include new values
        Schema::table('quiz_submissions', function (Blueprint $table) {
            // Change column definition to include all status values
            $table->enum('status', [
                'in_progress',
                'completed', 
                'pending_review',
                'graded',
                'passed',
                'failed'
            ])->default('in_progress')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_submissions', function (Blueprint $table) {
            // Revert to original enum values
            $table->enum('status', [
                'in_progress',
                'completed',
                'graded'
            ])->default('in_progress')->change();
        });
    }
};
