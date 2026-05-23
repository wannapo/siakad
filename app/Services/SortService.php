<?php

namespace App\Services;

/**
 * Class SortService
 * 
 * Implementasi algoritma pengurutan data mahasiswa:
 * - Bubble Sort    → O(n²) — bandingkan dan tukar berulang
 * - Selection Sort → O(n²) — pilih minimum lalu tukar
 * - Insertion Sort → O(n²) worst / O(n) best — sisipkan ke posisi tepat
 * 
 * Setiap metode mengembalikan data terurut + metadata performa.
 */
class SortService
{
    /**
     * BUBBLE SORT
     * Membandingkan elemen berpasangan dan menukar jika tidak urut.
     * Proses diulang hingga tidak ada pertukaran lagi.
     * Time Complexity : O(n²) worst/average, O(n) best (sudah terurut)
     * Space Complexity: O(1)
     *
     * @param array  $data  Array data mahasiswa
     * @param string $field Kolom yang dijadikan kunci pengurutan
     * @param string $order 'asc' atau 'desc'
     * @return array
     */
    public function bubbleSort(array $data, string $field = 'nama', string $order = 'asc'): array
    {
        $startTime = microtime(true);
        $n         = count($data);
        $swaps     = 0;
        $comparisons = 0;

        // Outer loop: n-1 pass
        for ($i = 0; $i < $n - 1; $i++) {
            $swapped = false;

            // Inner loop: bandingkan pasangan bersebelahan
            for ($j = 0; $j < $n - $i - 1; $j++) {
                $comparisons++;
                $valA = strtolower((string)($data[$j][$field] ?? ''));
                $valB = strtolower((string)($data[$j + 1][$field] ?? ''));

                // Tentukan kondisi tukar berdasarkan order
                $shouldSwap = ($order === 'asc') ? ($valA > $valB) : ($valA < $valB);

                if ($shouldSwap) {
                    // Tukar posisi elemen
                    [$data[$j], $data[$j + 1]] = [$data[$j + 1], $data[$j]];
                    $swaps++;
                    $swapped = true;
                }
            }

            // Optimasi: jika tidak ada tukar, data sudah terurut
            if (!$swapped) break;
        }

        $endTime       = microtime(true);
        $executionTime = round($endTime - $startTime, 6);

        return [
            'data'        => $data,
            'algorithm'   => 'Bubble Sort',
            'field'       => $field,
            'order'       => $order,
            'time'        => $executionTime,
            'time_ms'     => round($executionTime * 1000, 4),
            'complexity'  => 'O(n²)',
            'comparisons' => $comparisons,
            'swaps'       => $swaps,
            'n'           => $n,
        ];
    }

    /**
     * SELECTION SORT
     * Setiap iterasi, cari elemen terkecil/terbesar lalu letakkan di posisi yang benar.
     * Time Complexity : O(n²) — selalu sama terlepas dari kondisi data
     * Space Complexity: O(1)
     *
     * @param array  $data
     * @param string $field
     * @param string $order
     * @return array
     */
    public function selectionSort(array $data, string $field = 'nama', string $order = 'asc'): array
    {
        $startTime   = microtime(true);
        $n           = count($data);
        $swaps       = 0;
        $comparisons = 0;

        for ($i = 0; $i < $n - 1; $i++) {
            $selectedIdx = $i; // Anggap elemen ke-i adalah yang terkecil

            // Cari elemen terkecil di sisa array
            for ($j = $i + 1; $j < $n; $j++) {
                $comparisons++;
                $valSelected = strtolower((string)($data[$selectedIdx][$field] ?? ''));
                $valJ        = strtolower((string)($data[$j][$field] ?? ''));

                $isSmaller = ($order === 'asc') ? ($valJ < $valSelected) : ($valJ > $valSelected);

                if ($isSmaller) {
                    $selectedIdx = $j; // Update indeks minimum/maksimum
                }
            }

            // Tukar elemen terpilih dengan posisi i
            if ($selectedIdx !== $i) {
                [$data[$i], $data[$selectedIdx]] = [$data[$selectedIdx], $data[$i]];
                $swaps++;
            }
        }

        $endTime       = microtime(true);
        $executionTime = round($endTime - $startTime, 6);

        return [
            'data'        => $data,
            'algorithm'   => 'Selection Sort',
            'field'       => $field,
            'order'       => $order,
            'time'        => $executionTime,
            'time_ms'     => round($executionTime * 1000, 4),
            'complexity'  => 'O(n²)',
            'comparisons' => $comparisons,
            'swaps'       => $swaps,
            'n'           => $n,
        ];
    }

    /**
     * INSERTION SORT
     * Ambil elemen satu per satu dan sisipkan ke posisi yang tepat di bagian yang sudah terurut.
     * Time Complexity : O(n²) worst, O(n) best (data hampir terurut)
     * Space Complexity: O(1)
     *
     * @param array  $data
     * @param string $field
     * @param string $order
     * @return array
     */
    public function insertionSort(array $data, string $field = 'nama', string $order = 'asc'): array
    {
        $startTime   = microtime(true);
        $n           = count($data);
        $shifts      = 0;
        $comparisons = 0;

        for ($i = 1; $i < $n; $i++) {
            $current = $data[$i]; // Elemen yang akan disisipkan
            $valCurr = strtolower((string)($current[$field] ?? ''));
            $j       = $i - 1;

            // Geser elemen ke kanan selama lebih besar dari current
            while ($j >= 0) {
                $comparisons++;
                $valJ    = strtolower((string)($data[$j][$field] ?? ''));
                $shouldShift = ($order === 'asc') ? ($valJ > $valCurr) : ($valJ < $valCurr);

                if ($shouldShift) {
                    $data[$j + 1] = $data[$j]; // Geser kanan
                    $shifts++;
                    $j--;
                } else {
                    break;
                }
            }

            $data[$j + 1] = $current; // Sisipkan di posisi yang tepat
        }

        $endTime       = microtime(true);
        $executionTime = round($endTime - $startTime, 6);

        return [
            'data'        => $data,
            'algorithm'   => 'Insertion Sort',
            'field'       => $field,
            'order'       => $order,
            'time'        => $executionTime,
            'time_ms'     => round($executionTime * 1000, 4),
            'complexity'  => 'O(n²) worst / O(n) best',
            'comparisons' => $comparisons,
            'shifts'      => $shifts,
            'n'           => $n,
        ];
    }
}
