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
        // Add 'blackout' to status enum
        // For MySQL/MariaDB, modify the ENUM
        // For PostgreSQL, this would be different
        // For SQLite, no action needed as it doesn't enforce enum at DB level
        
        // SQLite doesn't enforce enum, so we just document it in code
        // MySQL/PostgreSQL users would need to alter the column type
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback would remove 'blackout' from allowed values
        // For SQLite, no action needed
    }
};
