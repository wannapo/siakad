{{-- resources/views/dashboard/index.blade.php --}}
{{-- Ganti / sesuaikan dengan dashboard lo yang sudah ada --}}

@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

{{-- Tombol di topbar --}}
@section('topbar-actions')
    <button class="btn btn-primary btn-sm" id="btn-open-broadcast">
        <i class="ti ti-send"></i> Kirim Email
    </button>
@endsection

@section('content')

{{-- ================================================================
     STATISTIK RINGKAS (sesuaikan data dari controller)
     ================================================================ --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-label">Total Mahasiswa</div>
        <div class="stat-value">{{ $totalMahasiswa ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Mahasiswa Aktif</div>
        <div class="stat-value">{{ $aktif ?? 0 }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Email Terkirim Hari Ini</div>
        <div class="stat-value" id="email-sent-count">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Queue Pending</div>
        <div class="stat-value" id="queue-count">—</div>
    </div>
</div>

{{-- ================================================================
     TABEL DATA TERBARU
     ================================================================ --}}
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-header">
        <span class="card-title">DATA TERBARU</span>
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
    </div>

    {{-- Selection bar — muncul kalau ada yang dicentang --}}
    <div class="selection-bar" id="sel-bar" style="display:none;align-items:center;justify-content:space-between;background:rgba(59,130,246,.08);border:0.5px solid rgba(59,130,246,.2);border-radius:8px;padding:10px 14px;margin-bottom:12px">
        <span style="font-size:13px">
            <span style="color:#3b82f6;font-weight:500" id="sel-count">0</span>
            mahasiswa dipilih
        </span>
        <button class="btn btn-primary btn-sm" id="btn-send-selected">
            <i class="ti ti-send"></i> Kirim Email ke Terpilih
        </button>
    </div>

    <div style="overflow-x:auto">
        <table class="table" id="tbl-mahasiswa">
            <thead>
                <tr>
                    <th style="width:36px">
                        <input type="checkbox" id="check-all" class="checkbox">
                    </th>
                    <th>NIM</th>
                    <th>NAMA</th>
                    <th>JURUSAN</th>
                    <th>ANGKATAN</th>
                    <th>STATUS</th>
                    <th style="width:80px">EMAIL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswaTerbaru ?? [] as $mhs)
                <tr data-id="{{ $mhs->id }}" data-nama="{{ $mhs->nama }}">
                    <td>
                        <input type="checkbox" class="checkbox row-check" value="{{ $mhs->id }}">
                    </td>
                    <td>
                        <a href="{{ route('mahasiswa.show', $mhs) }}" class="nim-link">
                            {{ $mhs->nim }}
                        </a>
                    </td>
                    <td>{{ $mhs->nama }}</td>
                    <td>
                        <span class="badge badge-outline">{{ $mhs->jurusan }}</span>
                    </td>
                    <td>{{ $mhs->angkatan }}</td>
                    <td>
                        @if($mhs->status === 'Aktif')
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-neutral">{{ $mhs->status }}</span>
                        @endif
                    </td>
                    <td>
                        {{-- Tombol kirim email per baris --}}
                        <button
                            class="btn btn-sm btn-icon-mail"
                            data-id="{{ $mhs->id }}"
                            data-nama="{{ $mhs->nama }}"
                            title="Kirim email ke {{ $mhs->nama }}"
                        >
                            <i class="ti ti-mail"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:var(--color-text-tertiary);padding:2rem">
                        Belum ada data mahasiswa.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================================================================
     ACTIVITY LOG
     ================================================================ --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">// ACTIVITY LOG</span>
        <a href="{{ route('log.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
    </div>
    @foreach($recentLogs ?? [] as $log)
    <div class="log-item">
        <span class="log-time">{{ $log->created_at->format('H:i:s') }}</span>
        <span class="log-desc">{{ $log->description }}</span>
        <span class="badge badge-outline" style="font-size:10px">{{ strtoupper($log->event ?? 'LOG') }}</span>
    </div>
    @endforeach
</div>

{{-- ================================================================
     MODAL KIRIM EMAIL
     ================================================================ --}}
<div id="email-modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:999;align-items:center;justify-content:center">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modal-heading">

        <div class="modal-header">
            <h2 id="modal-heading" style="font-size:16px;font-weight:500;margin:0">Kirim Email</h2>
            <button class="icon-btn modal-close-btn" aria-label="Tutup">
                <i class="ti ti-x"></i>
            </button>
        </div>

        {{-- Tipe notifikasi --}}
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

        {{-- Penerima --}}
        <div class="recip-box" style="margin-bottom:1rem">
            <div style="font-size:11px;color:var(--color-text-tertiary);margin-bottom:6px;letter-spacing:.04em">PENERIMA</div>
            <div id="recip-display" style="display:flex;flex-wrap:wrap;gap:5px"></div>
        </div>

        {{-- Form --}}
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
            <label class="form-label">URL Aksi <span style="color:var(--color-text-tertiary)">(opsional)</span></label>
            <input type="text" id="inp-url" class="form-input" placeholder="https://...">
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:1rem;border-top:0.5px solid var(--color-border-tertiary)">
            <button class="btn modal-close-btn">Batal</button>
            <button class="btn btn-primary" id="btn-submit-email">
                <i class="ti ti-send"></i>
                <span id="btn-submit-label">Kirim Sekarang</span>
            </button>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
/* ── Stat cards ── */
.stat-card {
    background: var(--color-background-secondary);
    border-radius: var(--border-radius-md);
    padding: 1rem;
}
.stat-label {
    font-size: 12px;
    color: var(--color-text-tertiary);
    margin-bottom: 6px;
}
.stat-value {
    font-size: 24px;
    font-weight: 500;
}

/* ── Table helpers ── */
.nim-link {
    font-family: 'Space Mono', monospace;
    font-size: 12px;
    color: #3b82f6;
    text-decoration: none;
}
.nim-link:hover { text-decoration: underline; }
.badge-success  { background: rgba(22,163,74,.15); color: #16a34a; font-size:11px; padding:2px 8px; border-radius:6px; font-weight:500; }
.badge-neutral  { background: var(--color-background-secondary); color: var(--color-text-secondary); font-size:11px; padding:2px 8px; border-radius:6px; }
.badge-outline  { background: transparent; border: 0.5px solid var(--color-border-secondary); color: var(--color-text-secondary); font-size:11px; padding:2px 8px; border-radius:6px; }

/* ── Log item ── */
.log-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 0.5px solid var(--color-border-tertiary);
    font-size: 13px;
}
.log-item:last-child { border-bottom: none; }
.log-time { font-family: 'Space Mono', monospace; font-size: 11px; color: var(--color-text-tertiary); flex-shrink: 0; }
.log-desc { flex: 1; color: var(--color-text-secondary); }

/* ── Modal ── */
.modal-box {
    background: var(--color-background-primary);
    border: 0.5px solid var(--color-border-secondary);
    border-radius: 12px;
    width: 520px;
    max-width: 95vw;
    padding: 1.5rem;
}
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}

/* ── Type tabs ── */
.type-tab {
    flex: 1;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    border: 0.5px solid var(--color-border-secondary);
    background: transparent;
    color: var(--color-text-secondary);
    font-family: inherit;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all .15s;
}
.type-tab.active {
    background: rgba(79,70,229,.15);
    border-color: #4f46e5;
    color: #818cf8;
}
.type-tab:hover:not(.active) { background: var(--color-background-secondary); }

/* ── Recipient chip ── */
.recip-box {
    background: var(--color-background-secondary);
    border: 0.5px solid var(--color-border-tertiary);
    border-radius: 8px;
    padding: 10px 12px;
}
.recip-chip {
    background: rgba(59,130,246,.12);
    color: #60a5fa;
    border: 0.5px solid rgba(59,130,246,.25);
    border-radius: 6px;
    padding: 2px 8px;
    font-size: 12px;
}

/* ── Form elements ── */
.form-label {
    display: block;
    font-size: 12px;
    color: var(--color-text-secondary);
    font-weight: 500;
    margin-bottom: 5px;
}
.form-input {
    width: 100%;
    background: var(--color-background-secondary);
    border: 0.5px solid var(--color-border-secondary);
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--color-text-primary);
    font-family: inherit;
    outline: none;
    resize: vertical;
}
.form-input:focus { border-color: #4f46e5; }

/* ── Spinner ── */
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin .8s linear infinite; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
    const overlay = document.getElementById('email-modal-overlay');

    // ── helpers ──────────────────────────────────────────────────────
    function openModal() {
        overlay.style.display = 'flex';
        document.getElementById('inp-subject').focus();
    }
    function closeModal() {
        overlay.style.display = 'none';
        resetForm();
    }
    function resetForm() {
        document.getElementById('inp-subject').value = '';
        document.getElementById('inp-message').value = '';
        document.getElementById('inp-url').value     = '';
        document.getElementById('modal-ids').value   = '';
        setType('pengumuman');
    }
    function setType(type) {
        document.getElementById('modal-type').value = type;
        document.querySelectorAll('.type-tab').forEach(t => {
            t.classList.toggle('active', t.dataset.type === type);
        });
        const subjects = {
            pengumuman: 'Pengumuman Akademik – ',
            nilai:      'Informasi Nilai Mata Kuliah',
            tagihan:    'Reminder: Tagihan SPP Belum Dibayar',
        };
        document.getElementById('inp-subject').value = subjects[type] ?? '';
    }
    function setRecipients(chips) {
        const box = document.getElementById('recip-display');
        box.innerHTML = chips.map(c => `<span class="recip-chip">${c}</span>`).join('');
    }
    function updateSelectionBar() {
        const checked = document.querySelectorAll('.row-check:checked');
        const bar     = document.getElementById('sel-bar');
        document.getElementById('sel-count').textContent = checked.length;
        bar.style.display = checked.length > 0 ? 'flex' : 'none';
        const all   = document.getElementById('check-all');
        const total = document.querySelectorAll('.row-check').length;
        all.indeterminate = checked.length > 0 && checked.length < total;
        all.checked       = total > 0 && checked.length === total;
    }

    // ── checkbox logic ───────────────────────────────────────────────
    document.getElementById('check-all').addEventListener('change', function () {
        document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
        updateSelectionBar();
    });
    document.querySelectorAll('.row-check').forEach(c => {
        c.addEventListener('change', updateSelectionBar);
    });

    // ── open broadcast ───────────────────────────────────────────────
    document.getElementById('btn-open-broadcast').addEventListener('click', () => {
        document.getElementById('modal-mode').value = 'broadcast';
        document.getElementById('modal-heading').textContent = 'Kirim Email ke Semua Mahasiswa';
        setRecipients(['<i class="ti ti-users" style="font-size:11px"></i> Semua Mahasiswa']);
        setType('pengumuman');
        openModal();
    });

    // ── open individual (selected rows) ─────────────────────────────
    document.getElementById('btn-send-selected').addEventListener('click', () => {
        const checked = document.querySelectorAll('.row-check:checked');
        const ids     = [...checked].map(c => c.value);
        const names   = [...checked].map(c => c.closest('tr').dataset.nama);
        document.getElementById('modal-mode').value = 'individual';
        document.getElementById('modal-ids').value  = JSON.stringify(ids);
        document.getElementById('modal-heading').textContent = 'Kirim Email ke Mahasiswa Terpilih';
        setRecipients(names);
        setType('pengumuman');
        openModal();
    });

    // ── open per-row mail button ─────────────────────────────────────
    document.querySelectorAll('.btn-icon-mail').forEach(btn => {
        btn.addEventListener('click', () => {
            const id   = btn.dataset.id;
            const nama = btn.dataset.nama;
            document.getElementById('modal-mode').value = 'individual';
            document.getElementById('modal-ids').value  = JSON.stringify([id]);
            document.getElementById('modal-heading').textContent = 'Kirim Email ke Mahasiswa';
            setRecipients([nama]);
            setType('pengumuman');
            openModal();
        });
    });

    // ── type tab clicks ──────────────────────────────────────────────
    document.querySelectorAll('.type-tab').forEach(tab => {
        tab.addEventListener('click', () => setType(tab.dataset.type));
    });

    // ── close modal ──────────────────────────────────────────────────
    document.querySelectorAll('.modal-close-btn').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });
    overlay.addEventListener('click', e => {
        if (e.target === overlay) closeModal();
    });

    // ── submit ───────────────────────────────────────────────────────
    document.getElementById('btn-submit-email').addEventListener('click', async () => {
        const subject = document.getElementById('inp-subject').value.trim();
        const message = document.getElementById('inp-message').value.trim();

        if (!subject) {
            document.getElementById('inp-subject').focus();
            showToast('Subjek email wajib diisi.', 'error');
            return;
        }
        if (!message) {
            document.getElementById('inp-message').focus();
            showToast('Isi pesan wajib diisi.', 'error');
            return;
        }

        const mode = document.getElementById('modal-mode').value;
        const url  = mode === 'broadcast'
            ? '{{ route("email.broadcast") }}'
            : '{{ route("email.individual") }}';

        const payload = {
            type:    document.getElementById('modal-type').value,
            subject,
            message,
            url:     document.getElementById('inp-url').value.trim() || null,
        };
        if (mode === 'individual') {
            payload.ids = JSON.parse(document.getElementById('modal-ids').value || '[]');
        }

        // Loading state
        const btn   = document.getElementById('btn-submit-email');
        const label = document.getElementById('btn-submit-label');
        btn.disabled  = true;
        label.innerHTML = '<span class="spin"><i class="ti ti-loader"></i></span> Mengirim...';

        try {
            const res  = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  CSRF,
                    'Accept':        'application/json',
                },
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (data.success) {
                closeModal();
                showToast(data.message, 'success');
            } else {
                showToast(data.message ?? 'Terjadi kesalahan.', 'error');
            }
        } catch (err) {
            showToast('Gagal terhubung ke server.', 'error');
        } finally {
            btn.disabled    = false;
            label.textContent = 'Kirim Sekarang';
        }
    });

    // ── toast helper (pakai sistem toast SISMAKA yang sudah ada) ─────
    function showToast(msg, type = 'success') {
        const container = document.getElementById('toast-container');
        const icons = { success: 'ti-circle-check', error: 'ti-alert-circle', info: 'ti-info-circle' };
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="ti ${icons[type] ?? 'ti-info-circle'}"></i>
            <span class="toast-msg">${msg}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="ti ti-x"></i>
            </button>`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
})();
</script>
@endpush
