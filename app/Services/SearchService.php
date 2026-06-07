<?php

namespace App\Services;

/**
 * SearchService
 * Implementasi algoritma pencarian:
 * - Linear Search   O(n)
 * - Binary Search   O(log n)
 * - Sequential Search O(n)
 *
 * Setiap pencarian mengembalikan hasil, jumlah iterasi, dan waktu eksekusi.
 */
class SearchService
{
    /**
     * Entry point — pilih algoritma berdasarkan parameter.
     *
     * @param  array  $data   Array of associative arrays (data mahasiswa)
     * @param  string $query  Kata kunci pencarian
     * @param  string $algo   'linear' | 'binary' | 'sequential'
     * @return array  [results, iterations, time_ms, time_sec, algo_name, complexity, tc, total_data]
     */
    public static function search(array $data, string $query, string $algo = 'linear'): array
    {
        $query = strtolower(trim($query));

        $startTime = microtime(true);

        switch ($algo) {
            case 'binary':
                [$results, $iterations] = self::binarySearch($data, $query);
                $algoName   = 'Binary Search';
                $complexity = 'O(log n)';
                break;

            case 'sequential':
                [$results, $iterations] = self::sequentialSearch($data, $query);
                $algoName   = 'Sequential Search';
                $complexity = 'O(n)';
                break;

            default: // linear
                [$results, $iterations] = self::linearSearch($data, $query);
                $algoName   = 'Linear Search';
                $complexity = 'O(n)';
        }

        $endTime = microtime(true);
        $timeMs  = round(($endTime - $startTime) * 1000, 4);
        $timeSec = round($endTime - $startTime, 6);
        $n       = count($data);

        return [
            'results'    => $results,
            'iterations' => $iterations,
            'time_ms'    => $timeMs,
            'time_sec'   => $timeSec,
            'algo_name'  => $algoName,
            'complexity' => $complexity,
            'total_data' => $n,
            'tc'         => self::getTimeComplexity($algo, $n),
        ];
    }

    // ──────────────────────────────────────────────────
    //  LINEAR SEARCH — O(n)
    //  Cek satu per satu dari awal sampai akhir.
    // ──────────────────────────────────────────────────
    private static function linearSearch(array $data, string $query): array
    {
        $results    = [];
        $iterations = 0;

        foreach ($data as $item) {
            $iterations++;

            // Cek di beberapa field sekaligus
            if (
                str_contains(strtolower($item['nim']      ?? ''), $query) ||
                str_contains(strtolower($item['nama']     ?? ''), $query) ||
                str_contains(strtolower($item['jurusan']  ?? ''), $query) ||
                str_contains(strtolower($item['angkatan'] ?? ''), $query)
            ) {
                $results[] = array_merge($item, ['iteration' => $iterations]);
            }
        }

        return [$results, $iterations];
    }

    // ──────────────────────────────────────────────────
    //  BINARY SEARCH — O(log n)
    //  Data harus diurutkan terlebih dahulu (by NIM).
    //  Cocok untuk pencarian NIM exact, fallback ke linear
    //  untuk pencarian nama/jurusan.
    // ──────────────────────────────────────────────────
    private static function binarySearch(array $data, string $query): array
    {
        // Sort data by NIM dulu
        usort($data, fn($a, $b) => strcmp($a['nim'] ?? '', $b['nim'] ?? ''));

        $results    = [];
        $iterations = 0;
        $lo         = 0;
        $hi         = count($data) - 1;
        $found      = false;

        // Coba exact match pada NIM dulu
        while ($lo <= $hi) {
            $iterations++;
            $mid = (int) floor(($lo + $hi) / 2);
            $cmp = strcmp($data[$mid]['nim'] ?? '', $query);

            if ($cmp === 0) {
                $results[] = array_merge($data[$mid], ['iteration' => $iterations]);
                $found     = true;
                break;
            } elseif ($cmp < 0) {
                $lo = $mid + 1;
            } else {
                $hi = $mid - 1;
            }
        }

        // Jika tidak ketemu exact NIM, fallback ke linear untuk nama & jurusan
        if (!$found) {
            foreach ($data as $index => $item) {
                $iterations++;
                if (
                    str_contains(strtolower($item['nama']     ?? ''), $query) ||
                    str_contains(strtolower($item['jurusan']  ?? ''), $query) ||
                    str_contains(strtolower($item['angkatan'] ?? ''), $query)
                ) {
                    $results[] = array_merge($item, ['iteration' => $iterations]);
                }
            }
        }

        return [$results, $iterations];
    }

    // ──────────────────────────────────────────────────
    //  SEQUENTIAL SEARCH — O(n)
    //  Mirip linear, tapi berhenti di hasil pertama (first match).
    //  Untuk demo: menunjukkan perbedaan iterasi vs linear.
    // ──────────────────────────────────────────────────
    private static function sequentialSearch(array $data, string $query): array
    {
        $results    = [];
        $iterations = 0;

        foreach ($data as $item) {
            $iterations++;

            if (
                str_contains(strtolower($item['nim']      ?? ''), $query) ||
                str_contains(strtolower($item['nama']     ?? ''), $query) ||
                str_contains(strtolower($item['jurusan']  ?? ''), $query) ||
                str_contains(strtolower($item['angkatan'] ?? ''), $query)
            ) {
                $results[] = array_merge($item, ['iteration' => $iterations]);
                // Sequential search: stop at first match
                break;
            }
        }

        return [$results, $iterations];
    }

    // ──────────────────────────────────────────────────
    //  TIME COMPLEXITY INFO
    // ──────────────────────────────────────────────────
    private static function getTimeComplexity(string $algo, int $n): array
    {
        $logN = $n > 0 ? (int) ceil(log($n, 2)) : 1;

        $map = [
            'linear' => [
                'best'       => 'O(1)',
                'best_note'  => 'Data ditemukan di posisi pertama',
                'avg'        => 'O(n/2)',
                'avg_note'   => "≈ {$n} / 2 = " . (int) ceil($n / 2) . ' iterasi',
                'worst'      => 'O(n)',
                'worst_note' => "Max {$n} iterasi (data di akhir / tidak ada)",
            ],
            'binary' => [
                'best'       => 'O(1)',
                'best_note'  => 'Data tepat di posisi tengah',
                'avg'        => 'O(log n)',
                'avg_note'   => "≈ log₂({$n}) = {$logN} iterasi",
                'worst'      => 'O(log n)',
                'worst_note' => "Max {$logN} iterasi (data tidak ditemukan)",
            ],
            'sequential' => [
                'best'       => 'O(1)',
                'best_note'  => 'Data ditemukan di posisi pertama',
                'avg'        => 'O(n/2)',
                'avg_note'   => "≈ " . (int) ceil($n / 2) . ' iterasi (rata-rata)',
                'worst'      => 'O(n)',
                'worst_note' => "Max {$n} iterasi (tidak ditemukan)",
            ],
        ];

        return $map[$algo] ?? $map['linear'];
    }
}
