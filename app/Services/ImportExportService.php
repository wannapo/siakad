<?php

namespace App\Services;

use App\Models\Mahasiswa;
use Illuminate\Http\UploadedFile;

/**
 * ImportExportService
 * Handle import dari CSV/JSON dan export ke CSV/JSON.
 */
class ImportExportService
{
    /**
     * Import dari file CSV atau JSON.
     *
     * @param  UploadedFile $file
     * @param  string       $format 'csv' | 'json'
     * @return array [added, skipped, errors]
     * @throws \Exception on format error
     */
    public static function import(UploadedFile $file, string $format): array
    {
        $content = file_get_contents($file->getRealPath());

        if (empty(trim($content))) {
            throw new \Exception('File kosong.');
        }

        $rows = match ($format) {
            'csv'   => self::parseCsv($content),
            'json'  => self::parseJson($content),
            default => throw new \Exception("Format tidak didukung: {$format}"),
        };

        return self::insertRows($rows);
    }

    // ──────────────────────────────────────────────────
    //  PARSE CSV
    // ──────────────────────────────────────────────────
    private static function parseCsv(string $content): array
    {
        $lines  = array_filter(explode("\n", trim($content)));
        $header = array_map('trim', str_getcsv(array_shift($lines)));

        // Validasi header
        if (!ValidationService::validateCsvHeader($header)) {
            throw new \Exception('Header CSV tidak valid. Pastikan ada kolom: nim, nama, jurusan, angkatan, email');
        }

        $rows = [];
        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $values = str_getcsv($line);
            if (count($values) < count($header)) {
                // Pad with empty strings jika kurang kolom
                $values = array_pad($values, count($header), '');
            }

            $row = array_combine($header, $values);
            $rows[] = array_map('trim', $row);
        }

        return $rows;
    }

    // ──────────────────────────────────────────────────
    //  PARSE JSON
    // ──────────────────────────────────────────────────
    private static function parseJson(string $content): array
    {
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON tidak valid: ' . json_last_error_msg());
        }

        $error = ValidationService::validateJsonImport($data);
        if ($error) {
            throw new \Exception($error);
        }

        return array_map(fn($item) => (array) $item, $data);
    }

    // ──────────────────────────────────────────────────
    //  INSERT ROWS KE DATABASE
    // ──────────────────────────────────────────────────
    private static function insertRows(array $rows): array
    {
        $added   = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($rows as $index => $row) {
            try {
                $nim = trim($row['nim'] ?? '');

                if (empty($nim) || empty($row['nama'] ?? '')) {
                    $skipped++;
                    continue;
                }

                // Skip jika NIM sudah ada
                if (Mahasiswa::where('nim', $nim)->exists()) {
                    $skipped++;
                    $errors[] = "Baris " . ($index + 2) . ": NIM {$nim} sudah terdaftar, dilewati.";
                    continue;
                }

                Mahasiswa::create([
                    'nim'      => $nim,
                    'nama'     => trim($row['nama']     ?? ''),
                    'jurusan'  => trim($row['jurusan']  ?? ''),
                    'angkatan' => trim($row['angkatan'] ?? ''),
                    'email'    => trim($row['email']    ?? ''),
                    'hp'       => trim($row['hp']       ?? ''),
                    'status'   => trim($row['status']   ?? 'Aktif'),
                ]);

                $added++;

            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return compact('added', 'skipped', 'errors');
    }

    // ──────────────────────────────────────────────────
    //  EXPORT CSV
    // ──────────────────────────────────────────────────
    public static function exportCsv(): string
    {
        $mahasiswas = Mahasiswa::all();
        $lines      = [];

        // Header
        $lines[] = 'nim,nama,jurusan,angkatan,email,hp,status';

        foreach ($mahasiswas as $m) {
            $lines[] = implode(',', [
                $m->nim,
                '"' . str_replace('"', '""', $m->nama) . '"',
                $m->jurusan,
                $m->angkatan,
                $m->email,
                $m->hp ?? '',
                $m->status,
            ]);
        }

        return implode("\n", $lines);
    }

    // ──────────────────────────────────────────────────
    //  EXPORT JSON
    // ──────────────────────────────────────────────────
    public static function exportJson(): string
    {
        $mahasiswas = Mahasiswa::all(['nim','nama','jurusan','angkatan','email','hp','status']);
        return json_encode($mahasiswas->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
