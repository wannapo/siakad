@php
    $map = ['Aktif' => 'badge-green', 'Cuti' => 'badge-yellow', 'Lulus' => 'badge-purple'];
    $class = $map[$status] ?? 'badge-blue';
@endphp
<span class="badge {{ $class }}">{{ $status }}</span>
