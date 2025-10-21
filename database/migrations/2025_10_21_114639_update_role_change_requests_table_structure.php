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
        Schema::table('role_change_requests', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['request_type', 'role_name', 'review_notes']);
            
            // Add new JSON columns
            $table->json('requested_roles')->after('user_id');
            $table->json('current_roles')->nullable()->after('requested_roles');
            
            // Update reason to be required
            $table->text('reason')->nullable(false)->change();
            
            // Rename review_notes to review_note (already dropped above, add new one)
            $table->text('review_note')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_change_requests', function (Blueprint $table) {
            // Restore old columns
            $table->string('request_type')->after('user_id');
            $table->string('role_name')->after('request_type');
            $table->text('review_notes')->nullable()->after('reviewed_by');
            
            // Drop new columns
            $table->dropColumn(['requested_roles', 'current_roles', 'review_note']);
            
            // Revert reason to nullable
            $table->text('reason')->nullable()->change();
        });
    }
};
