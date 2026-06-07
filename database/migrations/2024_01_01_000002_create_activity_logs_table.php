<?php
// database/migrations/2024_01_01_000002_create_activity_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('aksi', ['crud', 'search', 'sort', 'import', 'error'])->default('crud');
            $table->text('keterangan');
            $table->float('durasi_ms')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('aksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
