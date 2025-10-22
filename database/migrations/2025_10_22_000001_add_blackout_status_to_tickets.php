<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to use raw SQL to modify enum
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('todo', 'doing', 'done', 'blackout') DEFAULT 'todo'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        // First, update any 'blackout' status to 'todo'
        DB::table('tickets')->where('status', 'blackout')->update(['status' => 'todo']);
        
        // Then modify the enum
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('todo', 'doing', 'done') DEFAULT 'todo'");
    }
};
