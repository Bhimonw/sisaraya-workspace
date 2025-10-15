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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('evaluable_type'); // Polymorphic: Project atau ProjectEvent
            $table->unsignedBigInteger('evaluable_id');
            $table->foreignId('researcher_id')->constrained('users')->onDelete('cascade');
            $table->text('notes'); // Catatan evaluasi
            $table->string('status')->default('draft'); // draft, published
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
            
            $table->index(['evaluable_type', 'evaluable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
