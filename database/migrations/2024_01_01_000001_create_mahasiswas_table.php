<?php
// database/migrations/2024_01_01_000001_create_mahasiswas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 10)->unique();
            $table->string('nama', 100);
            $table->string('jurusan', 50);
            $table->string('angkatan', 4);
            $table->string('email', 100)->unique();
            $table->string('hp', 15)->nullable();
            $table->enum('status', ['Aktif', 'Cuti', 'Lulus'])->default('Aktif');
            $table->timestamps();

            // Index untuk mempercepat pencarian
            $table->index(['nama', 'jurusan', 'angkatan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
