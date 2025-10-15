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
        Schema::table('tickets', function (Blueprint $table) {
            // Add context: umum, event, proyek
            $table->enum('context', ['umum', 'event', 'proyek'])->default('proyek')->after('type');
            
            // Add project_event_id for event-related tickets
            $table->foreignId('project_event_id')->nullable()->constrained('project_events')->onDelete('cascade')->after('project_id');
            
            // Make project_id nullable since "umum" tickets don't belong to projects
            $table->foreignId('project_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['project_event_id']);
            $table->dropColumn(['context', 'project_event_id']);
        });
    }
};
