<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Serah Terima Aset - {{ $pemakai->nomor_serah_terima }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 13px; color: #1e293b; padding: 24px; }
        .waktu { font-size: 11px; color: #64748b; }
        .judul { text-align: right; font-size: 13px; color: #1e293b; }
        .kwitansi { max-width: 380px; margin: 24px auto; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 24px; }
        .center { text-align: center; }
        .brand { font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .nomor { color: #64748b; margin-top: 4px; }
        .tanggal { color: #94a3b8; font-size: 11px; margin-top: 2px; }
        hr { border: none; border-top: 1px dashed #cbd5e1; margin: 16px 0; }
        .row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .label { color: #64748b; width: 130px; flex-shrink: 0; }
        .value { font-weight: bold; text-align: right; }
        .catatan-label { color: #64748b; }
        .footer { text-align: center; color: #94a3b8; font-size: 10px; margin-top: 16px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="row" style="max-width:380px;margin:0 auto;">
        <span class="waktu">{{ now()->format('n/j/y, g:i A') }}</span>
    </div>
    <div class="judul" style="max-width:380px;margin:0 auto;">
        Bukti Serah Terima Aset - {{ $pemakai->nomor_serah_terima }}
    </div>

    <div class="kwitansi">
        <div class="center">
            <div class="brand">Marimas One</div>
            <div class="brand">Bukti Serah Terima Aset</div>
            <div class="nomor">{{ $pemakai->nomor_serah_terima }}</div>
            <div class="tanggal">{{ $pemakai->tanggal_serah->translatedFormat('d M Y') }}</div>
        </div>

        <hr>

        <div class="row">
            <span class="label">Aset</span>
            <span class="value">{{ $pemakai->aset->kode_aset }} &mdash; {{ $pemakai->aset->merek }} {{ $pemakai->aset->tipe }}</span>
        </div>
        <div class="row">
            <span class="label">Diserahkan Kepada</span>
            <span class="value">{{ $pemakai->penerima->name }}</span>
        </div>
        <div class="row">
            <span class="label">Nomor Penerimaan</span>
            <span class="value">{{ $pemakai->nomor_serah_terima }}</span>
        </div>

        @if ($pemakai->catatan_serah)
            <div style="margin-top: 12px;">
                <div class="catatan-label">Catatan</div>
                <div>{{ $pemakai->catatan_serah }}</div>
            </div>
        @endif

        <div class="footer">Dicetak otomatis oleh sistem Marimas One</div>
    </div>

    <div class="center no-print" style="margin-top: 16px;">
        <button onclick="window.print()" style="padding:8px 20px;background:#0f172a;color:#fff;border:none;border-radius:6px;cursor:pointer;">
            Cetak
        </button>
        <a href="{{ route('inventaris.aset.show', $pemakai->aset_id) }}" style="margin-left:8px;color:#64748b;text-decoration:none;">
            Kembali ke Aset
        </a>
    </div>

    <script>window.onload = () => window.print();</script>
</body>
</html>