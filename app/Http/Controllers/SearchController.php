<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Services\SearchService;
use App\Services\LogService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * GET /search — Halaman pencarian + hasil.
     */
    public function index(Request $request)
    {
        $searchResult = null;

        if ($q = $request->input('q')) {
            try {
                $algo = $request->input('algo', 'linear');
                $data = Mahasiswa::all()->toArray();

                $searchResult = SearchService::search($data, $q, $algo);

                LogService::search(
                    "{$searchResult['algo_name']}: \"{$q}\" — {$searchResult['iterations']} iterasi, " .
                    count($searchResult['results']) . " hasil ditemukan",
                    $searchResult['time_ms']
                );

            } catch (\Exception $e) {
                LogService::error('pencarian: ' . $e->getMessage());
                return back()->with('error', 'Pencarian gagal: ' . $e->getMessage());
            }
        }

        return view('search.index', compact('searchResult'));
    }
}
