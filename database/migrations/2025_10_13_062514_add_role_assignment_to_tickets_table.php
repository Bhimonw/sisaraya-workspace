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
            // Target role for ticket (who can claim this ticket)
            $table->string('target_role')->nullable()->after('type');
            
            // User who claimed/picked up this ticket
            $table->foreignId('claimed_by')->nullable()->constrained('users')->onDelete('set null')->after('target_role');
            
            // When the ticket was claimed
            $table->timestamp('claimed_at')->nullable()->after('claimed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['claimed_by']);
            $table->dropColumn(['target_role', 'claimed_by', 'claimed_at']);
        });
    }
};
