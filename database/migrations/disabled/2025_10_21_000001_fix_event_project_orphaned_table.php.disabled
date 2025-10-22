<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration fixes the orphaned event_project table that was created
     * before the events table existed, causing foreign key constraint failures.
     */
    public function up(): void
    {
        // Drop the orphaned event_project table if it exists
        // We'll recreate it properly after the events table exists
        Schema::dropIfExists('event_project');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do here - the proper event_project table
        // will be recreated by the original migration
    }
};
