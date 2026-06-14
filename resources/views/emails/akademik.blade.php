{{-- resources/views/emails/akademik.blade.php --}}
{{-- Template email markdown Laravel default, customized untuk SISMAKA --}}

@component('mail::message')

# {{ $judul ?? 'Notifikasi Akademik' }}

{{ $pesan }}

@if(isset($actionUrl) && $actionUrl)
@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
{{ $actionLabel ?? 'Lihat Detail' }}
@endcomponent
@endif

---

Jika tombol di atas tidak berfungsi, salin link berikut ke browser:
`{{ $actionUrl ?? config('app.url') }}`

Terima kasih,
**Tim SISMAKA**
{{ config('app.name') }}

@endcomponent
