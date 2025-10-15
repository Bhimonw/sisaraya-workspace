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
        Schema::table('project_user', function (Blueprint $table) {
            // Remove old single event_role column
            $table->dropColumn('event_role');
            // Add new event_roles JSON column for multiple roles
            $table->json('event_roles')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_user', function (Blueprint $table) {
            $table->dropColumn('event_roles');
            // Restore old single event_role column
            $table->string('event_role')->nullable()->after('role');
        });
    }
};
