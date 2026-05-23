<?php

namespace App\Services;

/**
 * Class SearchService
 * 
 * Implementasi algoritma pencarian data mahasiswa:
 * - Linear Search     → O(n)
 * - Binary Search     → O(log n) — data harus terurut
 * - Sequential Search → O(n)     — mirip linear, iteratif ketat
 * 
 * Setiap metode mengembalikan hasil + waktu eksekusi + kompleksitas.
 */
class SearchService
{
    /**
     * LINEAR SEARCH
     * Mencari data satu per satu dari awal hingga akhir array.
     * Time Complexity : O(n)
     * Space Complexity: O(1)
     *
     * @param array  $data   Array data mahasiswa
     * @param string $keyword Kata kunci pencarian
     * @param string $field   Kolom yang dicari (nim, nama, email, dll)
     * @return array ['results', 'time', 'complexity', 'steps', 'algorithm']
     */
    public function linearSearch(array $data, string $keyword, string $field = 'nama'): array
    {
        $startTime = microtime(true); // Mulai hitung waktu
        $results   = [];
        $steps     = 0;
        $keyword   = strtolower(trim($keyword));

        // Iterasi satu per satu dari indeks 0 hingga n-1
        foreach ($data as $item) {
            $steps++; // Hitung langkah
            $value = strtolower((string)($item[$field] ?? ''));

            // Cocokkan keyword (contains)
            if (str_contains($value, $keyword)) {
                $results[] = $item;
            }
        }

        $endTime       = microtime(true);
        $executionTime = round($endTime - $startTime, 6); // Dalam detik
        $n             = count($data);

        return [
            'results'    => $results,
            'found'      => count($results),
            'time'       => $executionTime,
            'time_ms'    => round($executionTime * 1000, 4), // Dalam milidetik
            'complexity' => 'O(n)',
            'steps'      => $steps,
            'algorithm'  => 'Linear Search',
            'n'          => $n,
            'description'=> "Memeriksa {$steps} dari {$n} data secara berurutan",
        ];
    }

    /**
     * BINARY SEARCH
     * Mencari data dengan membagi array menjadi dua bagian.
     * SYARAT: Data harus sudah terurut berdasarkan field yang dicari.
     * Time Complexity : O(log n)
     * Space Complexity: O(1)
     *
     * @param array  $data    Array data mahasiswa (HARUS TERURUT by $field)
     * @param string $keyword Kata kunci pencarian EXACT (untuk binary search)
     * @param string $field   Kolom yang dicari
     * @return array
     */
    public function binarySearch(array $data, string $keyword, string $field = 'nim'): array
    {
        $startTime = microtime(true);
        $steps     = 0;
        $keyword   = strtolower(trim($keyword));

        // Sort data terlebih dahulu berdasarkan field (prerequisite binary search)
        usort($data, fn($a, $b) => strtolower($a[$field] ?? '') <=> strtolower($b[$field] ?? ''));

        $left    = 0;
        $right   = count($data) - 1;
        $results = [];

        // Loop biner: bagi dua setiap iterasi
        while ($left <= $right) {
            $steps++;
            $mid      = (int)(($left + $right) / 2);
            $midValue = strtolower((string)($data[$mid][$field] ?? ''));

            if ($midValue === $keyword) {
                // Ditemukan — cari juga elemen duplikat di sekitarnya
                $results[] = $data[$mid];

                // Cek kiri (duplikat)
                $i = $mid - 1;
                while ($i >= 0 && strtolower((string)($data[$i][$field] ?? '')) === $keyword) {
                    $results[] = $data[$i];
                    $i--;
                    $steps++;
                }

                // Cek kanan (duplikat)
                $j = $mid + 1;
                while ($j < count($data) && strtolower((string)($data[$j][$field] ?? '')) === $keyword) {
                    $results[] = $data[$j];
                    $j++;
                    $steps++;
                }
                break;

            } elseif ($midValue < $keyword) {
                $left = $mid + 1; // Cari di bagian kanan
            } else {
                $right = $mid - 1; // Cari di bagian kiri
            }
        }

        $endTime       = microtime(true);
        $executionTime = round($endTime - $startTime, 6);
        $n             = count($data);
        $logN          = $n > 0 ? round(log($n, 2), 2) : 0;

        return [
            'results'    => $results,
            'found'      => count($results),
            'time'       => $executionTime,
            'time_ms'    => round($executionTime * 1000, 4),
            'complexity' => 'O(log n)',
            'steps'      => $steps,
            'algorithm'  => 'Binary Search',
            'n'          => $n,
            'log_n'      => $logN,
            'description'=> "Hanya memeriksa {$steps} langkah dari {$n} data (log₂{$n} ≈ {$logN})",
        ];
    }

    /**
     * SEQUENTIAL SEARCH
     * Pencarian urut ketat dengan kondisi berhenti lebih awal (early exit).
     * Efisien untuk data terurut karena bisa berhenti saat melewati nilai target.
     * Time Complexity : O(n) worst, lebih cepat di praktik jika data terurut
     * Space Complexity: O(1)
     *
     * @param array  $data
     * @param string $keyword
     * @param string $field
     * @return array
     */
    public function sequentialSearch(array $data, string $keyword, string $field = 'nama'): array
    {
        $startTime = microtime(true);
        $results   = [];
        $steps     = 0;
        $keyword   = strtolower(trim($keyword));

        // Sort dulu untuk manfaatkan early exit
        usort($data, fn($a, $b) => strtolower($a[$field] ?? '') <=> strtolower($b[$field] ?? ''));

        foreach ($data as $item) {
            $steps++;
            $value = strtolower((string)($item[$field] ?? ''));

            if (str_contains($value, $keyword)) {
                $results[] = $item;
            } elseif ($value > $keyword && !empty($results)) {
                // Early exit: sudah melewati keyword di data terurut
                break;
            }
        }

        $endTime       = microtime(true);
        $executionTime = round($endTime - $startTime, 6);
        $n             = count($data);

        return [
            'results'    => $results,
            'found'      => count($results),
            'time'       => $executionTime,
            'time_ms'    => round($executionTime * 1000, 4),
            'complexity' => 'O(n)',
            'steps'      => $steps,
            'algorithm'  => 'Sequential Search',
            'n'          => $n,
            'description'=> "Memeriksa {$steps} dari {$n} data dengan early-exit optimization",
        ];
    }

    /**
     * Estimasi waktu pencarian berdasarkan ukuran data (per detik)
     * Berguna untuk menampilkan prediksi sebelum pencarian dilakukan
     *
     * @param int    $n         Jumlah data
     * @param string $algorithm linear|binary|sequential
     * @return array
     */
    public function estimateSearchTime(int $n, string $algorithm = 'linear'): array
    {
        // Konstanta waktu per operasi (dalam mikrodetik, dikalibrasi empiris)
        $timePerOp = 0.0001; // 0.1 ms per operasi

        $estimatedOps = match($algorithm) {
            'binary'     => log($n, 2),
            'sequential' => $n / 2, // rata-rata
            default      => $n,     // linear worst case
        };

        $estimatedTime = $estimatedOps * $timePerOp;

        return [
            'algorithm'      => $algorithm,
            'n'              => $n,
            'estimated_ops'  => round($estimatedOps),
            'estimated_time' => round($estimatedTime, 6),
            'estimated_ms'   => round($estimatedTime * 1000, 4),
            'complexity'     => match($algorithm) {
                'binary' => 'O(log n)',
                default  => 'O(n)',
            },
        ];
    }
}
