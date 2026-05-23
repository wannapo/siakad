<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel activity_logs untuk mencatat semua aksi user
     * Termasuk pencarian, import, export, CRUD
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action', 50);                  // create, read, update, delete, search, import, export
            $table->string('target', 100)->nullable();     // target data (nim/nama yang dicari)
            $table->text('description');                   // deskripsi aksi
            $table->string('algorithm', 50)->nullable();   // algoritma yang dipakai (linear, binary, bubble, dll)
            $table->float('execution_time')->nullable();   // waktu eksekusi dalam detik
            $table->string('complexity', 20)->nullable();  // notasi Big-O: O(n), O(log n), dll
            $table->integer('data_count')->nullable();     // jumlah data saat aksi dilakukan
            $table->text('user_comment')->nullable();      // komentar dari user
            $table->string('ip_address', 45)->nullable();  // IP address
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
