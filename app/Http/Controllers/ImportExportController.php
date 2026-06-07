<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Services\ImportExportService;
use App\Services\LogService;
use Illuminate\Http\Request;

class ImportExportController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Mahasiswa::count(),
            'aktif'    => Mahasiswa::where('status', 'Aktif')->count(),
            'jurusan'  => Mahasiswa::distinct('jurusan')->count('jurusan'),
            'angkatan' => Mahasiswa::distinct('angkatan')->count('angkatan'),
        ];

        return view('import-export.index', compact('stats'));
    }

    /**
     * POST /import-export/import — Proses import file.
     */
    public function import(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                throw new \Exception('Tidak ada file yang diunggah.');
            }

            $file   = $request->file('file');
            $format = $request->input('format', 'csv');
            $ext    = strtolower($file->getClientOriginalExtension());

            // Validasi ekstensi
            if ($ext !== $format) {
                throw new \Exception("File harus berformat .{$format}, bukan .{$ext}. Periksa tab format yang dipilih.");
            }

            if (!in_array($ext, ['csv', 'json'])) {
                throw new \Exception("Format file tidak didukung: .{$ext}. Gunakan .csv atau .json");
            }

            $result = ImportExportService::import($file, $format);

            $msg = "Import {$format}: {$result['added']} data ditambahkan, {$result['skipped']} dilewati.";
            LogService::import($msg);

            $flashMsg = "Import berhasil! {$result['added']} data ditambahkan, {$result['skipped']} dilewati.";
            if (!empty($result['errors'])) {
                $flashMsg .= ' Beberapa baris dilewati: ' . implode('; ', array_slice($result['errors'], 0, 3));
            }

            return redirect()->route('import-export.index')->with('success', $flashMsg);

        } catch (\Exception $e) {
            LogService::error('import: ' . $e->getMessage());
            return back()
                ->withErrors(['import' => $e->getMessage()])
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * GET /import-export/export/{format} — Download export file.
     */
    public function export(string $format)
    {
        try {
            switch ($format) {
                case 'csv':
                    $content  = ImportExportService::exportCsv();
                    $filename = 'mahasiswa_' . date('Ymd_His') . '.csv';
                    $mime     = 'text/csv';
                    break;

                case 'json':
                    $content  = ImportExportService::exportJson();
                    $filename = 'mahasiswa_' . date('Ymd_His') . '.json';
                    $mime     = 'application/json';
                    break;

                default:
                    throw new \Exception("Format export tidak valid: {$format}");
            }

            LogService::import("Export {$format}: " . Mahasiswa::count() . " data");

            return response($content, 200, [
                'Content-Type'        => $mime,
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);

        } catch (\Exception $e) {
            LogService::error('export: ' . $e->getMessage());
            return redirect()->route('import-export.index')
                ->with('error', 'Export gagal: ' . $e->getMessage());
        }
    }
}
