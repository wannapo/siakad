@extends('layouts.app')

@section('title', 'Import / Export')
@section('page-title', 'Import / Export')

@section('content')

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

    {{-- IMPORT --}}
    <div class="section-card">
        <div class="section-label">IMPORT DATA</div>

        <div class="tab-bar" id="import-tabs">
            <div class="tab active" onclick="switchTab('csv', this)">CSV</div>
            <div class="tab" onclick="switchTab('json', this)">JSON</div>
        </div>

        @if($errors->has('import'))
            <div class="alert alert-error" style="margin-bottom:12px">
                <i class="ti ti-alert-circle"></i>
                <span>{{ $errors->first('import') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('import-export.import') }}" enctype="multipart/form-data" id="import-form">
            @csrf
            <input type="hidden" name="format" id="import-format" value="csv">

            <div class="import-zone" onclick="document.getElementById('file-input').click()" id="drop-zone">
                <i class="ti ti-cloud-upload"></i>
                <p>Klik atau seret file ke sini</p>
                <span id="format-hint">Format: CSV (nim,nama,jurusan,angkatan,email,status)</span>
            </div>
            <input
                type="file"
                name="file"
                id="file-input"
                style="display:none"
                accept=".csv,.json"
                onchange="previewFile(event)"
            >

            <div id="import-preview" style="display:none;margin-top:12px">
                <div class="section-label">PREVIEW (5 baris pertama)</div>
                <div id="preview-content" style="background:var(--bg3);border-radius:var(--radius);padding:10px;font-family:var(--font-mono);font-size:11px;color:var(--text2);overflow-x:auto;max-height:120px;overflow-y:auto;white-space:pre"></div>
                <div id="preview-info" style="font-size:11px;color:var(--text3);margin-top:6px"></div>
                <div style="display:flex;gap:8px;margin-top:10px">
                    <button type="submit" class="btn btn-success btn-sm" id="import-btn">
                        <i class="ti ti-check"></i> <span id="import-btn-text">Import</span>
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="cancelImport()">
                        <i class="ti ti-x"></i> Batal
                    </button>
                </div>
            </div>
        </form>

        <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border)">
            <div class="section-label">FORMAT YANG DITERIMA</div>
            <div style="font-family:var(--font-mono);font-size:11px;color:var(--text3);background:var(--bg3);border-radius:var(--radius);padding:10px;margin-top:6px">
                <div style="color:var(--accent);margin-bottom:4px">// CSV Header (wajib urut):</div>
                nim,nama,jurusan,angkatan,email,hp,status
                <div style="color:var(--accent);margin:8px 0 4px">// JSON Array:</div>
                [{"nim":"...","nama":"...","jurusan":"...","angkatan":"...","email":"...","status":"..."}]
            </div>
        </div>
    </div>

    {{-- EXPORT --}}
    <div class="section-card">
        <div class="section-label">EXPORT DATA</div>

        <div style="display:flex;flex-direction:column;gap:10px">
            <div style="background:var(--bg3);border-radius:var(--radius);padding:14px">
                <div style="font-size:13px;font-weight:500;margin-bottom:4px">Export sebagai CSV</div>
                <div style="font-size:11px;color:var(--text3);margin-bottom:10px">Kompatibel dengan Excel, Google Sheets</div>
                <a href="{{ route('import-export.export', 'csv') }}" class="btn btn-ghost btn-sm">
                    <i class="ti ti-download"></i> Download CSV
                </a>
            </div>
            <div style="background:var(--bg3);border-radius:var(--radius);padding:14px">
                <div style="font-size:13px;font-weight:500;margin-bottom:4px">Export sebagai JSON</div>
                <div style="font-size:11px;color:var(--text3);margin-bottom:10px">Format API-ready, human-readable</div>
                <a href="{{ route('import-export.export', 'json') }}" class="btn btn-ghost btn-sm">
                    <i class="ti ti-download"></i> Download JSON
                </a>
            </div>
            <div style="background:var(--bg3);border-radius:var(--radius);padding:14px">
                <div class="section-label" style="margin-bottom:8px">STATISTIK DATA</div>
                <div style="display:flex;flex-direction:column;gap:4px">
                    <div class="info-row"><span class="key">Total</span><span class="val">{{ $stats['total'] }} mahasiswa</span></div>
                    <div class="info-row"><span class="key">Aktif</span><span class="val">{{ $stats['aktif'] }}</span></div>
                    <div class="info-row"><span class="key">Jurusan</span><span class="val">{{ $stats['jurusan'] }} prodi</span></div>
                    <div class="info-row"><span class="key">Angkatan</span><span class="val">{{ $stats['angkatan'] }} tahun</span></div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
let currentFormat = 'csv';

function switchTab(fmt, el) {
    currentFormat = fmt;
    document.getElementById('import-format').value = fmt;
    document.querySelectorAll('.tab-bar .tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    const hints = {
        csv: 'Format: CSV (nim,nama,jurusan,angkatan,email,hp,status)',
        json: 'Format: JSON Array [{nim, nama, jurusan, angkatan, email, status}]'
    };
    document.getElementById('format-hint').textContent = hints[fmt];
    cancelImport();
}

function previewFile(e) {
    const file = e.target.files[0];
    if (!file) return;
    const ext = file.name.split('.').pop().toLowerCase();
    if (ext !== currentFormat) {
        showToastJs('error', `File harus berformat .${currentFormat}, bukan .${ext}`);
        e.target.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = function(ev) {
        try {
            const content = ev.target.result;
            let preview = '', count = 0;
            if (currentFormat === 'csv') {
                const lines = content.trim().split('\n');
                count = lines.length - 1;
                preview = lines.slice(0, 6).join('\n');
            } else {
                const data = JSON.parse(content);
                if (!Array.isArray(data)) throw new Error('Harus berupa JSON array.');
                count = data.length;
                preview = JSON.stringify(data.slice(0, 3), null, 2);
            }
            document.getElementById('preview-content').textContent = preview;
            document.getElementById('preview-info').textContent = `Total: ${count} baris data siap diimport`;
            document.getElementById('import-btn-text').textContent = `Import ${count} Data`;
            document.getElementById('import-preview').style.display = 'block';
        } catch(err) {
            showToastJs('error', 'Format file tidak valid: ' + err.message);
            e.target.value = '';
        }
    };
    reader.readAsText(file);
}

function cancelImport() {
    document.getElementById('import-preview').style.display = 'none';
    document.getElementById('file-input').value = '';
}

function showToastJs(type, msg) {
    const icons = {success:'ti-circle-check', error:'ti-alert-circle', info:'ti-info-circle'};
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="ti ${icons[type]}"></i><span class="toast-msg">${msg}</span><button class="toast-close" onclick="this.parentElement.remove()"><i class="ti ti-x"></i></button>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// Drag and drop
const dz = document.getElementById('drop-zone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('drag-over'); });
dz.addEventListener('dragleave', () => dz.classList.remove('drag-over'));
dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) {
        document.getElementById('file-input').files = e.dataTransfer.files;
        previewFile({ target: { files: e.dataTransfer.files, value: file.name } });
    }
});
</script>
@endpush
