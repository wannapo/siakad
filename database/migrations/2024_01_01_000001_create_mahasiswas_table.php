<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel mahasiswa dengan semua kolom yang dibutuhkan
     */
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 12)->unique();           // Nomor Induk Mahasiswa
            $table->string('nama', 100);                    // Nama lengkap
            $table->string('email', 100)->unique();         // Email unik
            $table->string('no_hp', 15)->nullable();        // Nomor HP
            $table->string('prodi', 50);                    // Program studi
            $table->string('fakultas', 50);                 // Fakultas
            $table->integer('angkatan');                    // Tahun angkatan
            $table->enum('status', ['aktif', 'cuti', 'lulus', 'keluar'])->default('aktif');
            $table->decimal('ipk', 3, 2)->default(0.00);   // IPK 0.00 - 4.00
            $table->string('alamat', 255)->nullable();      // Alamat lengkap
            $table->date('tanggal_lahir')->nullable();      // Tanggal lahir
            $table->timestamps();                           // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
