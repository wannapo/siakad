<?php

namespace App\Services;

/**
 * SortService
 * Implementasi algoritma sorting:
 * - Bubble Sort    O(n²)
 * - Selection Sort O(n²)
 *
 * Mengembalikan data terurut + info waktu eksekusi + kompleksitas.
 */
class SortService
{
    /**
     * Entry point — pilih algoritma sort.
     *
     * @param  array  $data      Array of associative arrays
     * @param  string $field     Field yang jadi kunci sort (nim, nama, angkatan, dll)
     * @param  string $algo      'bubble' | 'selection'
     * @param  string $direction 'asc' | 'desc'
     * @return array  [sorted_data, algo_name, complexity, time_ms, comparisons]
     */
    public static function sort(array $data, string $field = 'nama', string $algo = 'bubble', string $direction = 'asc'): array
    {
        $startTime  = microtime(true);
        $comparisons = 0;

        switch ($algo) {
            case 'selection':
                [$sorted, $comparisons] = self::selectionSort($data, $field, $direction);
                $algoName   = 'Selection Sort';
                $complexity = 'O(n²)';
                break;

            default: // bubble
                [$sorted, $comparisons] = self::bubbleSort($data, $field, $direction);
                $algoName   = 'Bubble Sort';
                $complexity = 'O(n²)';
        }

        $endTime = microtime(true);
        $timeMs  = round(($endTime - $startTime) * 1000, 4);
        $n       = count($data);

        return [
            'sorted_data' => $sorted,
            'algo'        => $algo,
            'algo_name'   => $algoName,
            'complexity'  => $complexity,
            'time_ms'     => $timeMs,
            'comparisons' => $comparisons,
            'n'           => $n,
        ];
    }

    // ──────────────────────────────────────────────────
    //  BUBBLE SORT — O(n²)
    //  Bandingkan elemen bertetangga, tukar jika salah urutan.
    //  Ulangi sampai tidak ada pertukaran.
    // ──────────────────────────────────────────────────
    private static function bubbleSort(array $arr, string $field, string $direction): array
    {
        $n           = count($arr);
        $comparisons = 0;

        for ($i = 0; $i < $n - 1; $i++) {
            $swapped = false;

            for ($j = 0; $j < $n - 1 - $i; $j++) {
                $comparisons++;
                $a = strtolower((string) ($arr[$j][$field]     ?? ''));
                $b = strtolower((string) ($arr[$j + 1][$field] ?? ''));

                $shouldSwap = $direction === 'asc' ? ($a > $b) : ($a < $b);

                if ($shouldSwap) {
                    [$arr[$j], $arr[$j + 1]] = [$arr[$j + 1], $arr[$j]];
                    $swapped = true;
                }
            }

            // Optimasi: hentikan jika sudah tidak ada swap (sudah terurut)
            if (!$swapped) break;
        }

        return [$arr, $comparisons];
    }

    // ──────────────────────────────────────────────────
    //  SELECTION SORT — O(n²)
    //  Cari minimum (atau maximum), taruh di posisi yang benar.
    //  Ulangi untuk sisa array.
    // ──────────────────────────────────────────────────
    private static function selectionSort(array $arr, string $field, string $direction): array
    {
        $n           = count($arr);
        $comparisons = 0;

        for ($i = 0; $i < $n - 1; $i++) {
            $targetIdx = $i;

            for ($j = $i + 1; $j < $n; $j++) {
                $comparisons++;
                $a = strtolower((string) ($arr[$j][$field]          ?? ''));
                $b = strtolower((string) ($arr[$targetIdx][$field]   ?? ''));

                $found = $direction === 'asc' ? ($a < $b) : ($a > $b);
                if ($found) {
                    $targetIdx = $j;
                }
            }

            // Tukar jika ada yang lebih kecil/besar ditemukan
            if ($targetIdx !== $i) {
                [$arr[$i], $arr[$targetIdx]] = [$arr[$targetIdx], $arr[$i]];
            }
        }

        return [$arr, $comparisons];
    }
}
