<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rabs', function (Blueprint $table) {
            $table->string('funds_status')->default('idle')->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('rabs', function (Blueprint $table) {
            $table->dropColumn('funds_status');
        });
    }
};
