<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ActivityLog;
use App\Services\ValidationService;
use App\Services\SortService;
use App\Services\LogService;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * GET /mahasiswa — List dengan filter, sort, pagination.
     */
    public function index(Request $request)
    {
        try {
            $query = Mahasiswa::query();

            // ── Filter ──
            if ($q = $request->input('q')) {
                $query->where(function ($qb) use ($q) {
                    $qb->where('nim',  'like', "%{$q}%")
                       ->orWhere('nama',    'like', "%{$q}%")
                       ->orWhere('jurusan', 'like', "%{$q}%")
                       ->orWhere('email',   'like', "%{$q}%");
                });
            }

            if ($jurusan = $request->input('jurusan')) {
                $query->where('jurusan', $jurusan);
            }
            if ($angkatan = $request->input('angkatan')) {
                $query->where('angkatan', $angkatan);
            }
            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

            // ── Sort pakai SortService ──
            $sortInfo = null;
            if ($sortField = $request->input('sort')) {
                $algo      = $request->input('algo', 'bubble');
                $direction = $request->input('dir', 'asc');

                // Ambil semua data dulu (sorting manual untuk demo algoritma)
                $allData = $query->get()->toArray();
                $result  = SortService::sort($allData, $sortField, $algo, $direction);

                LogService::sort(
                    "{$result['algo_name']} pada field '{$sortField}' ({$direction}), n={$result['n']}",
                    $result['time_ms']
                );

                // Paginate manual dari sorted array
                $page       = $request->input('page', 1);
                $perPage    = 10;
                $offset     = ($page - 1) * $perPage;
                $itemsArray = array_slice($result['sorted_data'], $offset, $perPage);

                // FIX: Mengubah kembali array mentah hasil sorting menjadi Objek Model Mahasiswa
                $items = collect($itemsArray)->map(function ($item) {
                    return (new Mahasiswa())->newFromBuilder($item);
                });

                $mahasiswas = new \Illuminate\Pagination\LengthAwarePaginator(
                    $items,
                    count($result['sorted_data']),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                $sortInfo = [
                    'algo'       => $result['algo_name'],
                    'complexity' => $result['complexity'],
                    'time_ms'    => $result['time_ms'],
                    'field'      => $sortField,
                ];
            } else {
                $mahasiswas = $query->orderBy('created_at', 'desc')->paginate(10);
            }

            $jurusanList  = Mahasiswa::distinct()->pluck('jurusan')->sort()->values();
            $angkatanList = Mahasiswa::distinct()->pluck('angkatan')->sort()->values();

            return view('mahasiswa.index', compact(
                'mahasiswas', 'jurusanList', 'angkatanList', 'sortInfo'
            ));

        } catch (\Exception $e) {
            LogService::error('index mahasiswa: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * GET /mahasiswa/create — Form tambah.
     */
    public function create()
    {
        return view('mahasiswa.create');
    }

    /**
     * POST /mahasiswa — Simpan data baru.
     */
    public function store(Request $request)
    {
        try {
            $data   = $request->only(['nim','nama','jurusan','angkatan','email','hp','status']);
            $errors = ValidationService::validateAll($data);

            // Cek duplikat NIM
            if (empty($errors['nim']) && Mahasiswa::where('nim', $data['nim'])->exists()) {
                $errors['nim'] = 'NIM sudah terdaftar.';
            }

            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            Mahasiswa::create($data);

            LogService::crud("Tambah mahasiswa: {$data['nama']} ({$data['nim']})");

            return redirect()->route('mahasiswa.index')
                ->with('success', "Mahasiswa {$data['nama']} berhasil ditambahkan!");

        } catch (\Exception $e) {
            LogService::error('store mahasiswa: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * GET /mahasiswa/{id}/edit — Form edit.
     */
    public function edit(int $id)
    {
        try {
            $mahasiswa = Mahasiswa::findOrFail($id);
            return view('mahasiswa.edit', compact('mahasiswa'));
        } catch (\Exception $e) {
            return redirect()->route('mahasiswa.index')
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }
    }

    /**
     * PUT /mahasiswa/{id} — Update data.
     */
    public function update(Request $request, int $id)
    {
        try {
            $mahasiswa = Mahasiswa::findOrFail($id);
            $data      = $request->only(['nim','nama','jurusan','angkatan','email','hp','status']);
            $errors    = ValidationService::validateAll($data, $id);

            // Cek duplikat NIM (kecuali diri sendiri)
            if (empty($errors['nim']) && Mahasiswa::where('nim', $data['nim'])->where('id', '!=', $id)->exists()) {
                $errors['nim'] = 'NIM sudah digunakan mahasiswa lain.';
            }

            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            $mahasiswa->update($data);

            LogService::crud("Edit mahasiswa: {$data['nama']} ({$data['nim']})");

            return redirect()->route('mahasiswa.index')
                ->with('success', "Data {$data['nama']} berhasil diperbarui!");

        } catch (\Exception $e) {
            LogService::error('update mahasiswa: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * DELETE /mahasiswa/{id} — Hapus data.
     */
    public function destroy(int $id)
    {
        try {
            $mahasiswa = Mahasiswa::findOrFail($id);
            $nama      = $mahasiswa->nama;
            $nim       = $mahasiswa->nim;
            $mahasiswa->delete();

            LogService::crud("Hapus mahasiswa: {$nama} ({$nim})");

            return redirect()->route('mahasiswa.index')
                ->with('success', "Data {$nama} berhasil dihapus.");

        } catch (\Exception $e) {
            LogService::error('destroy mahasiswa: ' . $e->getMessage());
            return redirect()->route('mahasiswa.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}