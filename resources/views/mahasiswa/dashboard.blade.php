@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('topbar-actions')
    {{-- Tombol bawaan --}}
    <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus"></i> Tambah Mahasiswa
    </a>
    {{-- Tombol Broadcast Baru --}}
    <button class="btn btn-primary btn-sm" id="btn-open-broadcast" style="background-color: #4f46e5; border-color: #4f46e5; margin-left: 8px;">
        <i class="ti ti-send"></i> Kirim Email
    </button>
@endsection

@section('content')

{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-label">TOTAL MAHASISWA</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-meta">Terdaftar</div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">AKTIF</div>
        <div class="stat-value">{{ $stats['aktif'] }}</div>
        <div class="stat-meta">Status aktif</div>
    </div>
    <div class="stat-card yellow">
        <div class="stat-label">JURUSAN</div>
        <div class="stat-value">{{ $stats['jurusan'] }}</div>
        <div class="stat-meta">Program studi</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">ANGKATAN</div>
        <div class="stat-value">{{ $stats['angkatan'] }}</div>
        <div class="stat-meta">Tahun berbeda</div>
    </div>
</div>

<div class="grid-2col">

    {{-- Recent Data Table --}}
    <div class="table-wrap" style="grid-column: span 2">
        <div class="table-header">
            <span class="table-title">Data Terbaru</span>
            <a href="{{ route('mahasiswa.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
        </div>

        {{-- Selection bar buat kirim email massal --}}
        <div class="selection-bar" id="sel-bar" style="display:none;align-items:center;justify-content:space-between;background:rgba(59,130,246,.08);border:0.5px solid rgba(59,130,246,.2);border-radius:8px;padding:10px 14px;margin: 0 16px 12px 16px;">
            <span style="font-size:13px">
                <span style="color:#3b82f6;font-weight:500" id="sel-count">0</span>
                mahasiswa dipilih
            </span>
            <button class="btn btn-primary btn-sm" id="btn-send-selected" style="background-color: #4f46e5; border-color: #4f46e5;">
                <i class="ti ti-send"></i> Kirim Email ke Terpilih
            </button>
        </div>

        <table>
            <thead>
                <tr>
                    {{-- Checkbox Header --}}
                    <th style="width:40px; text-align:center;">
                        <input type="checkbox" id="check-all" style="cursor: pointer;">
                    </th>
                    <th>NIM</th>
                    <th>NAMA</th>
                    <th>JURUSAN</th>
                    <th>ANGKATAN</th>
                    <th>STATUS</th>
                    {{-- Tombol Aksi Email --}}
                    <th style="width:60px; text-align:center;">EMAIL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $m)
                    <tr data-id="{{ $m->id }}" data-nama="{{ $m->nama }}">
                        {{-- Checkbox Row --}}
                        <td style="text-align:center;">
                            <input type="checkbox" class="row-check" value="{{ $m->id }}" style="cursor: pointer;">
                        </td>
                        <td class="nim">{{ $m->nim }}</td>
                        <td class="name">{{ $m->nama }}</td>
                        <td><span class="badge badge-blue">{{ $m->jurusan }}</span></td>
                        <td><span class="mono">{{ $m->angkatan }}</span></td>
                        <td>@include('components.status-badge', ['status' => $m->status])</td>
                        {{-- Tombol Kirim Satuan --}}
                        <td style="text-align:center;">
                            <button class="btn btn-ghost btn-sm btn-icon-mail" data-id="{{ $m->id }}" data-nama="{{ $m->nama }}" title="Kirim email ke {{ $m->nama }}" style="padding: 4px 6px;">
                                <i class="ti ti-mail" style="font-size: 16px; color:#94a3b8;"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="ti ti-database-off"></i>
                                <p>Belum ada data mahasiswa.</p>
                                <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary btn-sm" style="margin-top:10px">Tambah Sekarang</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Activity Log Panel --}}
<div class="log-panel" style="margin-top:16px">
    <div class="log-head">
        <span class="log-title">// ACTIVITY LOG</span>
        <a href="{{ route('log.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
    </div>
    <div class="log-list">
        @forelse($logs as $log)
            <div class="log-item">
                <span class="log-time">{{ $log->created_at->format('H:i:s') }}</span>
                <span class="log-msg">
                    {{ $log->keterangan }}
                    <span class="log-tag {{ $log->aksi }}">{{ strtoupper($log->aksi) }}</span>
                </span>
            </div>
        @empty
            <div class="log-item">
                <span class="log-msg" style="color:var(--text3)">Belum ada aktivitas.</span>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal Kirim Email --}}
<div id="email-modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:999;align-items:center;justify-content:center;backdrop-filter:blur(2px);">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modal-heading">

        <div class="modal-header">
            <h2 id="modal-heading" style="font-size:16px;font-weight:500;margin:0">Kirim Email</h2>
            <button class="btn btn-ghost btn-sm modal-close-btn" aria-label="Tutup" style="padding:4px;">
                <i class="ti ti-x" style="font-size:18px;"></i>
            </button>
        </div>

        <div style="display:flex;gap:6px;margin-bottom:1.25rem">
            <button class="type-tab active" data-type="pengumuman">
                <i class="ti ti-speakerphone"></i> Pengumuman
            </button>
            <button class="type-tab" data-type="nilai">
                <i class="ti ti-clipboard-check"></i> Nilai
            </button>
            <button class="type-tab" data-type="tagihan">
                <i class="ti ti-receipt"></i> Tagihan
            </button>
        </div>

        <div class="recip-box" style="margin-bottom:1rem">
            <div style="font-size:11px;color:#94a3b8;margin-bottom:6px;letter-spacing:.04em">PENERIMA</div>
            <div id="recip-display" style="display:flex;flex-wrap:wrap;gap:5px"></div>
        </div>

        <input type="hidden" id="modal-type" value="pengumuman">
        <input type="hidden" id="modal-mode" value="broadcast">
        <input type="hidden" id="modal-ids" value="">

        <div style="margin-bottom:12px">
            <label class="form-label">Subjek Email</label>
            <input type="text" id="inp-subject" class="form-input" placeholder="Masukkan subjek email...">
        </div>
        <div style="margin-bottom:12px">
            <label class="form-label">Isi Pesan</label>
            <textarea id="inp-message" class="form-input" rows="4" placeholder="Tulis pesan untuk mahasiswa..."></textarea>
        </div>
        <div style="margin-bottom:12px">
            <label class="form-label">URL Aksi <span style="color:#94a3b8">(opsional)</span></label>
            <input type="text" id="inp-url" class="form-input" placeholder="https://...">
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:1rem;border-top:1px solid #333;margin-top:1rem;">
            <button class="btn btn-ghost modal-close-btn">Batal</button>
            <button class="btn btn-primary" id="btn-submit-email" style="background-color: #4f46e5; border-color: #4f46e5;">
                <i class="ti ti-send"></i>
                <span id="btn-submit-label">Kirim Sekarang</span>
            </button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* CSS Tambahan Modal menyesuaikan Dark Theme */
.modal-box {
    background: #1e1e24; /* menyesuaikan tema dashboard */
    border: 1px solid #333;
    border-radius: 12px;
    width: 520px;
    max-width: 95vw;
    padding: 1.5rem;
    color: #e2e8f0;
}
.modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
.type-tab {
    flex: 1; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer;
    border: 1px solid #333; background: transparent; color: #94a3b8; font-family: inherit;
    display: inline-flex; align-items: center; justify-content: center; gap: 5px; transition: all .15s;
}
.type-tab.active { background: rgba(79,70,229,.15); border-color: #4f46e5; color: #818cf8; }
.type-tab:hover:not(.active) { background: rgba(255,255,255,0.05); }
.recip-box { background: rgba(0,0,0,0.2); border: 1px solid #333; border-radius: 8px; padding: 10px 12px; }
.recip-chip {
    background: rgba(79,70,229,.15); color: #818cf8; border: 1px solid rgba(79,70,229,.3);
    border-radius: 6px; padding: 2px 8px; font-size: 12px;
}
.form-label { display: block; font-size: 12px; color: #94a3b8; font-weight: 500; margin-bottom: 5px; }
.form-input {
    width: 100%; background: rgba(0,0,0,0.2); border: 1px solid #333; border-radius: 8px;
    padding: 8px 12px; font-size: 13px; color: #e2e8f0; outline: none; resize: vertical; box-sizing: border-box;
}
.form-input:focus { border-color: #4f46e5; }
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin .8s linear infinite; }
</style>
@endpush

@push('scripts')
<script>
    // Pastikan Laravel Echo sudah ter-setup di resources/js/bootstrap.js
    window.Echo.channel('activity-logs')
        .listen('LogCreated', (e) => {
            const logList = document.querySelector('.log-list');
            const newLog = `
                <div class="log-item">
                    <span class="log-time">${new Date().toLocaleTimeString()}</span>
                    <span class="log-msg">
                        ${e.log.keterangan}
                        <span class="log-tag ${e.log.aksi}">${e.log.aksi.toUpperCase()}</span>
                    </span>
                </div>`;
            logList.insertAdjacentHTML('afterbegin', newLog);
        });
</script>
<script>
(function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const CSRF = csrfMeta ? csrfMeta.content : '';
    const overlay = document.getElementById('email-modal-overlay');
    if(!overlay) return;

    function openModal() { overlay.style.display = 'flex'; document.getElementById('inp-subject').focus(); }
    function closeModal() { overlay.style.display = 'none'; resetForm(); }
    
    function resetForm() {
        document.getElementById('inp-subject').value = '';
        document.getElementById('inp-message').value = '';
        document.getElementById('inp-url').value = '';
        document.getElementById('modal-ids').value = '';
        setType('pengumuman');
    }

    function setType(type) {
        document.getElementById('modal-type').value = type;
        document.querySelectorAll('.type-tab').forEach(t => t.classList.toggle('active', t.dataset.type === type));
        const subjects = {
            pengumuman: 'Pengumuman Akademik – ',
            nilai: 'Informasi Nilai Mata Kuliah',
            tagihan: 'Reminder: Tagihan SPP Belum Dibayar',
        };
        document.getElementById('inp-subject').value = subjects[type] ?? '';
    }

    function setRecipients(chips) {
        document.getElementById('recip-display').innerHTML = chips.map(c => `<span class="recip-chip">${c}</span>`).join('');
    }

    function updateSelectionBar() {
        const checked = document.querySelectorAll('.row-check:checked');
        const bar = document.getElementById('sel-bar');
        document.getElementById('sel-count').textContent = checked.length;
        bar.style.display = checked.length > 0 ? 'flex' : 'none';
        const all = document.getElementById('check-all');
        const total = document.querySelectorAll('.row-check').length;
        all.indeterminate = checked.length > 0 && checked.length < total;
        all.checked = total > 0 && checked.length === total;
    }

    const checkAllBtn = document.getElementById('check-all');
    if(checkAllBtn) {
        checkAllBtn.addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
            updateSelectionBar();
        });
    }

    document.querySelectorAll('.row-check').forEach(c => c.addEventListener('change', updateSelectionBar));

    const btnOpenBroadcast = document.getElementById('btn-open-broadcast');
    if(btnOpenBroadcast) {
        btnOpenBroadcast.addEventListener('click', () => {
            document.getElementById('modal-mode').value = 'broadcast';
            document.getElementById('modal-heading').textContent = 'Kirim Email ke Semua Mahasiswa';
            setRecipients(['<i class="ti ti-users" style="font-size:11px"></i> Semua Mahasiswa']);
            setType('pengumuman');
            openModal();
        });
    }

    const btnSendSelected = document.getElementById('btn-send-selected');
    if(btnSendSelected) {
        btnSendSelected.addEventListener('click', () => {
            const checked = document.querySelectorAll('.row-check:checked');
            const ids = [...checked].map(c => c.value);
            const names = [...checked].map(c => c.closest('tr').dataset.nama);
            document.getElementById('modal-mode').value = 'individual';
            document.getElementById('modal-ids').value = JSON.stringify(ids);
            document.getElementById('modal-heading').textContent = 'Kirim Email ke Mahasiswa Terpilih';
            setRecipients(names);
            setType('pengumuman');
            openModal();
        });
    }

    document.querySelectorAll('.btn-icon-mail').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const nama = btn.dataset.nama;
            document.getElementById('modal-mode').value = 'individual';
            document.getElementById('modal-ids').value = JSON.stringify([id]);
            document.getElementById('modal-heading').textContent = 'Kirim Email ke Mahasiswa';
            setRecipients([nama]);
            setType('pengumuman');
            openModal();
        });
    });

    document.querySelectorAll('.type-tab').forEach(tab => tab.addEventListener('click', () => setType(tab.dataset.type)));
    document.querySelectorAll('.modal-close-btn').forEach(btn => btn.addEventListener('click', closeModal));
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

    const btnSubmitEmail = document.getElementById('btn-submit-email');
    if(btnSubmitEmail) {
        btnSubmitEmail.addEventListener('click', async () => {
            const subject = document.getElementById('inp-subject').value.trim();
            const message = document.getElementById('inp-message').value.trim();

            if (!subject) { document.getElementById('inp-subject').focus(); showToast('Subjek email wajib diisi.', 'error'); return; }
            if (!message) { document.getElementById('inp-message').focus(); showToast('Isi pesan wajib diisi.', 'error'); return; }

            const mode = document.getElementById('modal-mode').value;
            const url = mode === 'broadcast' ? '{{ route("email.broadcast") }}' : '{{ route("email.individual") }}';
            const payload = { type: document.getElementById('modal-type').value, subject, message, url: document.getElementById('inp-url').value.trim() || null };
            if (mode === 'individual') payload.ids = JSON.parse(document.getElementById('modal-ids').value || '[]');

            btnSubmitEmail.disabled = true;
            document.getElementById('btn-submit-label').innerHTML = '<span class="spin"><i class="ti ti-loader"></i></span> Mengirim...';

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (data.success) { closeModal(); showToast(data.message, 'success'); } 
                else { showToast(data.message ?? 'Terjadi kesalahan.', 'error'); }
            } catch (err) {
                showToast('Gagal terhubung ke server.', 'error');
            } finally {
                btnSubmitEmail.disabled = false;
                document.getElementById('btn-submit-label').textContent = 'Kirim Sekarang';
            }
        });
    }

    function showToast(msg, type = 'success') {
        const container = document.getElementById('toast-container');
        if(!container) { alert(msg); return; } // Fallback
        const icons = { success: 'ti-circle-check', error: 'ti-alert-circle', info: 'ti-info-circle' };
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `<i class="ti ${icons[type] ?? 'ti-info-circle'}"></i><span class="toast-msg">${msg}</span><button class="toast-close" onclick="this.parentElement.remove()"><i class="ti ti-x"></i></button>`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
})();
</script>
@endpush