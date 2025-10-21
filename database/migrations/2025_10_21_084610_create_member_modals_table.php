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
        Schema::create('member_modals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('jenis', ['uang', 'alat']); // Jenis modal
            $table->string('nama_item'); // Nama alat atau "Modal Uang"
            $table->decimal('jumlah_uang', 15, 2)->nullable(); // Untuk modal uang
            $table->text('deskripsi')->nullable(); // Deskripsi alat atau catatan
            $table->boolean('dapat_dipinjam')->default(true); // Apakah dapat dipinjamkan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_modals');
    }
};
