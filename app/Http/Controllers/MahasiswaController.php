<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Mahasiswa;
use App\Services\SearchService;
use App\Services\SortService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class MahasiswaController
 * 
 * Controller utama untuk manajemen data mahasiswa.
 * Menangani CRUD, Import/Export CSV & JSON, Search, Sort.
 * Dilengkapi validasi Regex, try-catch error handling,
 * dan pencatatan activity log + time complexity.
 */
class MahasiswaController extends Controller
{
    public function __construct(
        private SearchService $searchService,
        private SortService   $sortService
    ) {}

    // =========================================================
    //  READ — Daftar Mahasiswa (dengan search & sort)
    // =========================================================

    /**
     * Tampilkan daftar mahasiswa dengan fitur pencarian dan pengurutan.
     */
    public function index(Request $request)
    {
        try {
            $query = Mahasiswa::query();

            // Filter status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter prodi
            if ($request->filled('prodi')) {
                $query->where('prodi', $request->prodi);
            }

            // Pagination dasar dulu — untuk search algoritma, load semua ke array
            $allData    = $query->get()->map->toExportArray()->toArray();
            $totalData  = count($allData);
            $searchMeta = null;
            $sortMeta   = null;

            // ---- SEARCH ALGORITMA ----
            if ($request->filled('keyword')) {
                $algorithm = $request->get('search_algo', 'linear');
                $field     = $request->get('search_field', 'nama');
                $keyword   = $request->keyword;

                $searchResult = match($algorithm) {
                    'binary'     => $this->searchService->binarySearch($allData, $keyword, $field),
                    'sequential' => $this->searchService->sequentialSearch($allData, $keyword, $field),
                    default      => $this->searchService->linearSearch($allData, $keyword, $field),
                };

                $allData    = $searchResult['results'];
                $searchMeta = $searchResult;

                // Catat log pencarian
                ActivityLog::record('search', "Mencari '{$keyword}' pada field {$field} menggunakan {$searchResult['algorithm']}", [
                    'target'         => $keyword,
                    'algorithm'      => $searchResult['algorithm'],
                    'execution_time' => $searchResult['time'],
                    'complexity'     => $searchResult['complexity'],
                    'data_count'     => $totalData,
                ]);
            }

            // ---- SORT ALGORITMA ----
            if ($request->filled('sort_algo')) {
                $sortAlgo  = $request->sort_algo;
                $sortField = $request->get('sort_field', 'nama');
                $sortOrder = $request->get('sort_order', 'asc');

                $sortResult = match($sortAlgo) {
                    'selection' => $this->sortService->selectionSort($allData, $sortField, $sortOrder),
                    'insertion' => $this->sortService->insertionSort($allData, $sortField, $sortOrder),
                    default     => $this->sortService->bubbleSort($allData, $sortField, $sortOrder),
                };

                $allData  = $sortResult['data'];
                $sortMeta = $sortResult;
            }

            // Estimasi waktu jika belum search
            $estimate = $this->searchService->estimateSearchTime(
                $totalData,
                $request->get('search_algo', 'linear')
            );

            // Ambil daftar prodi untuk filter dropdown
            $prodis = Mahasiswa::select('prodi')->distinct()->orderBy('prodi')->pluck('prodi');

            return view('mahasiswa.index', compact(
                'allData', 'searchMeta', 'sortMeta', 'totalData', 'estimate', 'prodis'
            ));

        } catch (\Throwable $e) {
            // Penanganan error global
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  CREATE
    // =========================================================

    /** Tampilkan form tambah mahasiswa */
    public function create()
    {
        return view('mahasiswa.create');
    }

    /**
     * Simpan data mahasiswa baru ke database.
     * Validasi input menggunakan Regex.
     */
    public function store(Request $request)
    {
        // ---- VALIDASI dengan REGEX ----
        $validator = Validator::make($request->all(), [
            'nim'           => ['required', 'regex:/^[0-9]{8,12}$/', 'unique:mahasiswas,nim'],
            'nama'          => ['required', 'regex:/^[a-zA-Z\s\'.,-]{3,100}$/'],
            'email'         => ['required', 'email', 'unique:mahasiswas,email'],
            'no_hp'         => ['nullable', 'regex:/^(\+62|62|0)[0-9]{8,13}$/'],
            'prodi'         => ['required', 'string', 'max:50'],
            'fakultas'      => ['required', 'string', 'max:50'],
            'angkatan'      => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'status'        => ['required', Rule::in(['aktif', 'cuti', 'lulus', 'keluar'])],
            'ipk'           => ['required', 'numeric', 'min:0', 'max:4'],
            'alamat'        => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
        ], [
            'nim.regex'     => 'NIM harus berupa 8–12 digit angka.',
            'nama.regex'    => 'Nama hanya boleh mengandung huruf, spasi, dan tanda baca dasar.',
            'no_hp.regex'   => 'No. HP harus format Indonesia (0xx / +62xx).',
            'nim.unique'    => 'NIM sudah terdaftar di sistem.',
            'email.unique'  => 'Email sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal. Periksa kembali isian Anda.');
        }

        try {
            $mahasiswa = Mahasiswa::create($validator->validated());

            // Catat log
            ActivityLog::record('create', "Mahasiswa baru ditambahkan: {$mahasiswa->nama} ({$mahasiswa->nim})", [
                'target'     => $mahasiswa->nim,
                'data_count' => Mahasiswa::count(),
            ]);

            return redirect()->route('mahasiswa.index')
                ->with('success', "✅ Data mahasiswa <strong>{$mahasiswa->nama}</strong> berhasil ditambahkan!");

        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', '❌ Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  READ DETAIL
    // =========================================================

    public function show(Mahasiswa $mahasiswa)
    {
        $logs = ActivityLog::where('target', $mahasiswa->nim)
            ->orWhere('description', 'like', "%{$mahasiswa->nim}%")
            ->latest()
            ->take(10)
            ->get();

        return view('mahasiswa.show', compact('mahasiswa', 'logs'));
    }

    // =========================================================
    //  UPDATE
    // =========================================================

    public function edit(Mahasiswa $mahasiswa)
    {
        return view('mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validator = Validator::make($request->all(), [
            'nim'           => ['required', 'regex:/^[0-9]{8,12}$/', Rule::unique('mahasiswas', 'nim')->ignore($mahasiswa->id)],
            'nama'          => ['required', 'regex:/^[a-zA-Z\s\'.,-]{3,100}$/'],
            'email'         => ['required', 'email', Rule::unique('mahasiswas', 'email')->ignore($mahasiswa->id)],
            'no_hp'         => ['nullable', 'regex:/^(\+62|62|0)[0-9]{8,13}$/'],
            'prodi'         => ['required', 'string', 'max:50'],
            'fakultas'      => ['required', 'string', 'max:50'],
            'angkatan'      => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'status'        => ['required', Rule::in(['aktif', 'cuti', 'lulus', 'keluar'])],
            'ipk'           => ['required', 'numeric', 'min:0', 'max:4'],
            'alamat'        => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
        ], [
            'nim.regex'   => 'NIM harus berupa 8–12 digit angka.',
            'nama.regex'  => 'Nama hanya boleh mengandung huruf dan spasi.',
            'no_hp.regex' => 'No. HP harus format Indonesia.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Validasi gagal. Periksa kembali isian Anda.');
        }

        try {
            $old = $mahasiswa->toArray();
            $mahasiswa->update($validator->validated());

            ActivityLog::record('update', "Data mahasiswa diperbarui: {$mahasiswa->nama} ({$mahasiswa->nim})", [
                'target'     => $mahasiswa->nim,
                'data_count' => Mahasiswa::count(),
            ]);

            return redirect()->route('mahasiswa.index')
                ->with('success', "✅ Data mahasiswa <strong>{$mahasiswa->nama}</strong> berhasil diperbarui!");

        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', '❌ Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  DELETE
    // =========================================================

    public function destroy(Mahasiswa $mahasiswa)
    {
        try {
            $nama = $mahasiswa->nama;
            $nim  = $mahasiswa->nim;
            $mahasiswa->delete();

            ActivityLog::record('delete', "Mahasiswa dihapus: {$nama} ({$nim})", [
                'target'     => $nim,
                'data_count' => Mahasiswa::count(),
            ]);

            return redirect()->route('mahasiswa.index')
                ->with('success', "✅ Data mahasiswa <strong>{$nama}</strong> berhasil dihapus.");

        } catch (\Throwable $e) {
            return back()->with('error', '❌ Gagal menghapus data: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  IMPORT CSV / JSON
    // =========================================================

    public function importForm()
    {
        return view('mahasiswa.import');
    }

    /**
     * Import data mahasiswa dari file CSV atau JSON.
     * Validasi format file dan isi data.
     */
    public function import(Request $request)
    {
        // Validasi file upload
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimes:csv,txt,json', 'max:5120'], // max 5MB
        ], [
            'file.mimes' => '❌ Format file tidak valid. Hanya CSV dan JSON yang diizinkan.',
            'file.max'   => '❌ Ukuran file terlalu besar (maksimal 5MB).',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)
                ->with('error', $validator->errors()->first());
        }

        try {
            $file      = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $content   = file_get_contents($file->getRealPath());

            // Parse berdasarkan ekstensi
            $rows = match($extension) {
                'json'       => $this->parseJson($content),
                'csv', 'txt' => $this->parseCsv($content),
                default      => throw new \InvalidArgumentException("Format {$extension} tidak didukung."),
            };

            if (empty($rows)) {
                throw new \RuntimeException('File kosong atau tidak ada data yang valid.');
            }

            // Validasi kolom wajib
            $requiredColumns = ['nim', 'nama', 'email', 'prodi', 'fakultas', 'angkatan'];
            $firstRow        = array_keys($rows[0]);
            $missingCols     = array_diff($requiredColumns, $firstRow);

            if (!empty($missingCols)) {
                throw new \RuntimeException('Kolom wajib tidak ditemukan: ' . implode(', ', $missingCols));
            }

            // Proses insert dengan validasi per baris
            $imported = 0;
            $skipped  = 0;
            $errors   = [];

            foreach ($rows as $i => $row) {
                $rowNum = $i + 2; // +2 karena baris 1 adalah header

                // Validasi per baris
                $rowValidator = Validator::make($row, [
                    'nim'      => 'required|regex:/^[0-9]{8,12}$/|unique:mahasiswas,nim',
                    'nama'     => 'required|regex:/^[a-zA-Z\s\'.,-]{3,100}$/',
                    'email'    => 'required|email|unique:mahasiswas,email',
                    'prodi'    => 'required|string',
                    'fakultas' => 'required|string',
                    'angkatan' => 'required|integer|min:2000|max:' . date('Y'),
                ]);

                if ($rowValidator->fails()) {
                    $skipped++;
                    $errors[] = "Baris {$rowNum} (NIM: " . ($row['nim'] ?? '?') . "): " . $rowValidator->errors()->first();
                    continue;
                }

                // Insert ke database
                Mahasiswa::create([
                    'nim'           => $row['nim'],
                    'nama'          => $row['nama'],
                    'email'         => $row['email'],
                    'no_hp'         => $row['no_hp'] ?? null,
                    'prodi'         => $row['prodi'],
                    'fakultas'      => $row['fakultas'],
                    'angkatan'      => (int)$row['angkatan'],
                    'status'        => $row['status'] ?? 'aktif',
                    'ipk'           => isset($row['ipk']) ? (float)$row['ipk'] : 0.00,
                    'alamat'        => $row['alamat'] ?? null,
                    'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                ]);

                $imported++;
            }

            // Catat log import
            ActivityLog::record('import', "Import file {$extension}: {$imported} berhasil, {$skipped} dilewati", [
                'data_count' => Mahasiswa::count(),
            ]);

            $msg = "✅ Import selesai: <strong>{$imported}</strong> data berhasil diimpor.";
            if ($skipped > 0) {
                $msg .= " <strong>{$skipped}</strong> baris dilewati karena error.";
            }

            return redirect()->route('mahasiswa.index')
                ->with('success', $msg)
                ->with('import_errors', $errors);

        } catch (\InvalidArgumentException $e) {
            return back()->with('error', '❌ Format tidak valid: ' . $e->getMessage());
        } catch (\JsonException $e) {
            return back()->with('error', '❌ File JSON tidak valid / format JSON rusak: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Terjadi kesalahan tak terduga: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  EXPORT CSV / JSON
    // =========================================================

    /**
     * Export semua data mahasiswa ke format CSV atau JSON.
     */
    public function export(Request $request)
    {
        try {
            $format    = $request->get('format', 'csv');
            $mahasiswas = Mahasiswa::all()->map->toExportArray()->toArray();

            ActivityLog::record('export', "Export data ke format {$format}: " . count($mahasiswas) . " data", [
                'data_count' => count($mahasiswas),
            ]);

            if ($format === 'json') {
                $json = json_encode($mahasiswas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return response($json, 200)
                    ->header('Content-Type', 'application/json')
                    ->header('Content-Disposition', 'attachment; filename="mahasiswa_' . date('Ymd_His') . '.json"');
            }

            // Default: CSV
            $csvLines   = [];
            $headers    = ['nim', 'nama', 'email', 'no_hp', 'prodi', 'fakultas', 'angkatan', 'status', 'ipk', 'alamat', 'tanggal_lahir'];
            $csvLines[] = implode(',', $headers);

            foreach ($mahasiswas as $m) {
                $row = array_map(fn($v) => '"' . str_replace('"', '""', (string)$v) . '"', array_values($m));
                $csvLines[] = implode(',', $row);
            }

            $csv = implode("\n", $csvLines);
            return response($csv, 200)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="mahasiswa_' . date('Ymd_His') . '.csv"');

        } catch (\Throwable $e) {
            return back()->with('error', '❌ Gagal export: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  ADD COMMENT ke Activity Log
    // =========================================================

    public function addComment(Request $request, ActivityLog $log)
    {
        $request->validate(['comment' => 'required|string|max:500']);

        try {
            $log->update(['user_comment' => $request->comment]);
            return back()->with('success', '✅ Komentar berhasil disimpan.');
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Gagal menyimpan komentar: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  PRIVATE HELPERS
    // =========================================================

    /**
     * Parse konten JSON menjadi array asosiatif
     * @throws \JsonException
     */
    private function parseJson(string $content): array
    {
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        // Support format: array langsung atau {"data": [...]}
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }

        if (!is_array($data)) {
            throw new \RuntimeException('Struktur JSON tidak valid. Harus berupa array atau {"data": [...]}');
        }

        return $data;
    }

    /**
     * Parse konten CSV menjadi array asosiatif
     * @throws \RuntimeException
     */
    private function parseCsv(string $content): array
    {
        $lines = explode("\n", trim($content));

        if (count($lines) < 2) {
            throw new \RuntimeException('File CSV harus memiliki minimal 1 baris header dan 1 baris data.');
        }

        // Baris pertama = header
        $headers = str_getcsv(trim($lines[0]));
        $headers = array_map('trim', $headers);
        $rows    = [];

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;

            $values = str_getcsv($line);
            if (count($values) !== count($headers)) {
                continue; // Lewati baris yang tidak sesuai kolom
            }

            $rows[] = array_combine($headers, array_map('trim', $values));
        }

        return $rows;
    }
}
