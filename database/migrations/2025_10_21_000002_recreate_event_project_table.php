<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration recreates the event_project pivot table AFTER the events table exists.
     * The original migration (2025_10_13_050223) ran before events table was created.
     */
    public function up(): void
    {
        // Only create if it doesn't exist (in case of partial migrations)
        if (!Schema::hasTable('event_project')) {
            Schema::create('event_project', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
                $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
                $table->timestamps();
                
                // Unique constraint to prevent duplicate associations
                $table->unique(['event_id', 'project_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_project');
    }
};
