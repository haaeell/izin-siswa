<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Check-in Siswa</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: white;
            padding: 20px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 10px;
            color: #64748b;
            margin-top: 3px;
        }

        /* ── Filter info ── */
        .filter-info {
            font-size: 10px;
            color: #475569;
            margin-bottom: 12px;
            padding: 8px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }

        .filter-info strong {
            color: #1e293b;
        }

        .filter-sep {
            margin: 0 10px;
            color: #cbd5e1;
        }

        /* ── Stats: pakai table bukan grid ── */
        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 14px;
        }

        .stats-table td {
            width: 25%;
            text-align: center;
            padding: 10px 12px;
            border-radius: 6px;
            vertical-align: middle;
        }

        .stat-label {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .stat-num {
            font-size: 24px;
            font-weight: bold;
            line-height: 1.1;
        }

        .stat-sub {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .card-sudah-l {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .card-sudah-l .stat-num {
            color: #15803d;
        }

        .card-sudah-p {
            background: #fdf2f8;
            border: 1px solid #fbcfe8;
        }

        .card-sudah-p .stat-num {
            color: #be185d;
        }

        .card-belum-l {
            background: #fff7ed;
            border: 1px solid #fed7aa;
        }

        .card-belum-l .stat-num {
            color: #c2410c;
        }

        .card-belum-p {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .card-belum-p .stat-num {
            color: #dc2626;
        }

        /* ── Kelas header: pakai table bukan flex ── */
        .kelas-header-table {
            width: 100%;
            border-collapse: collapse;
            background: #1e3a8a;
            border-radius: 6px 6px 0 0;
            margin-top: 14px;
        }

        .kelas-header-table td {
            padding: 7px 10px;
            color: white;
            vertical-align: middle;
        }

        .kelas-name {
            font-size: 11px;
            font-weight: bold;
        }

        .kelas-badges-cell {
            text-align: right;
            white-space: nowrap;
        }

        .kbadge {
            display: inline-block;
            font-size: 9px;
            padding: 2px 8px;
            border-radius: 999px;
            font-weight: bold;
            margin-left: 4px;
        }

        .kbadge-l {
            background: #bfdbfe;
            color: #1e40af;
        }

        .kbadge-p {
            background: #fbcfe8;
            color: #9d174d;
        }

        .kbadge-ok {
            background: #bbf7d0;
            color: #14532d;
        }

        .kbadge-no {
            background: #fef9c3;
            color: #713f12;
        }

        .kbadge-tot {
            background: #ffffff33;
            color: white;
            border: 1px solid #ffffff44;
        }

        /* ── Data table ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 0;
        }

        .data-table thead th {
            background: #f1f5f9;
            padding: 7px 8px;
            text-align: left;
            border: 1px solid #cbd5e1;
            font-weight: bold;
            font-size: 10px;
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .data-table tbody tr.even {
            background: #f8fafc;
        }

        /* ── Badge ── */
        .badge-l {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-p {
            background: #fce7f3;
            color: #be185d;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: bold;
            white-space: nowrap;
            text-align: center;
        }

        .badge-sudah-ci {
            background: #dcfce7 !important;
            color: #475569 !important;
        }

        .badge-kembali {
            background: #f1f5f9 !important;
            color: #15803d !important;
        }

        .badge-belum {
            background: #fef9c3 !important;
            color: #a16207 !important;
        }

        .badge-l {
            background: #e0f2fe;
            color: #0369a1;
            padding: 2px 6px;
        }

        .badge-p {
            background: #fce7f3;
            color: #be185d;
            padding: 2px 6px;
        }

        .kep-sakit {
            background: #fee2e2;
            color: #dc2626;
        }

        .kep-pulang {
            background: #fff7ed;
            color: #ea580c;
        }

        .kep-pesiar {
            background: #f3e8ff;
            color: #9333ea;
        }

        .kep-perpulangan {
            background: #fefce8;
            color: #ca8a04;
        }

        /* ── Footer: pakai table bukan flex ── */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        .footer-table td {
            font-size: 9px;
            color: #94a3b8;
            padding-top: 6px;
        }

        .footer-right {
            text-align: right;
        }

        .empty {
            text-align: center;
            color: #94a3b8;
            padding: 20px;
            font-style: italic;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="header">
        <h1>Laporan Check-in Siswa</h1>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>

    {{-- Filter info --}}
    <div class="filter-info">
        @if($request->start_date || $request->end_date)
            <strong>Tanggal:</strong>
            {{ $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d M Y') : '—' }}
            s/d
            {{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d M Y') : '—' }}
            <span class="filter-sep">|</span>
        @endif
        @if($request->jam_mulai || $request->jam_akhir)
            <strong>Jam Check-in:</strong>
            {{ $request->jam_mulai ?? '00:00' }} – {{ $request->jam_akhir ?? '23:59' }}
            <span class="filter-sep">|</span>
        @endif
        @if(!$request->start_date && !$request->end_date && !$request->jam_mulai && !$request->jam_akhir)
            <strong>Filter:</strong> Semua data (tanpa filter tanggal/jam)
            <span class="filter-sep">|</span>
        @endif
        <strong>Total Data:</strong> {{ $records->count() }} siswa
    </div>

    {{-- Statistik Cards — pakai <table> agar Dompdf render 4 kolom sejajar --}}
        <table class="stats-table">
            <tr>
                <td class="card-sudah-l">
                    <div class="stat-label">Sudah Check-in</div>
                    <div class="stat-num">{{ $summary['sudah_l'] }}</div>
                    <div class="stat-sub">Laki-laki</div>
                </td>
                <td class="card-sudah-p">
                    <div class="stat-label">Sudah Check-in</div>
                    <div class="stat-num">{{ $summary['sudah_p'] }}</div>
                    <div class="stat-sub">Perempuan</div>
                </td>
                <td class="card-belum-l">
                    <div class="stat-label">Belum Check-in</div>
                    <div class="stat-num">{{ $summary['belum_l'] }}</div>
                    <div class="stat-sub">Laki-laki</div>
                </td>
                <td class="card-belum-p">
                    <div class="stat-label">Belum Check-in</div>
                    <div class="stat-num">{{ $summary['belum_p'] }}</div>
                    <div class="stat-sub">Perempuan</div>
                </td>
            </tr>
        </table>

        {{-- Data per Kelas --}}
        @php
            $recordsPerKelas = $records->groupBy(fn($p) => $p->student?->class?->name ?? 'Tidak Diketahui');
        @endphp

        @forelse ($recordsPerKelas as $namaKelas => $siswaList)
            @php
                $jmlL = $siswaList->filter(fn($p) => $p->student?->gender === 'L')->count();
                $jmlP = $siswaList->filter(fn($p) => $p->student?->gender === 'P')->count();
                $jmlSudah = $siswaList->filter(fn($p) => $p->checkin?->checkin_at)->count();
                $jmlBelum = $siswaList->filter(fn($p) => !$p->checkin?->checkin_at)->count();
            @endphp

            {{-- Header kelas — pakai table bukan div flex --}}
            <table class="kelas-header-table">
                <tr>
                    <td class="kelas-name">{{ $namaKelas }}</td>
                    <td class="kelas-badges-cell">
                        <span class="kbadge kbadge-l">L: {{ $jmlL }}</span>
                        <span class="kbadge kbadge-p">P: {{ $jmlP }}</span>
                        <span class="kbadge kbadge-ok">&#10003; Checkin: {{ $jmlSudah }}</span>
                        <span class="kbadge kbadge-no">&#10007; Belum: {{ $jmlBelum }}</span>
                        <span class="kbadge kbadge-tot">Total: {{ $siswaList->count() }}</span>
                    </td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:24px; text-align:center">#</th>
                        <th style="width:70px">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width:28px; text-align:center">JK</th>
                        <th>Alasan</th>
                        <th style="text-align:center; width:70px">Keperluan</th>
                        <th style="width:100px">Check-in</th>
                        <th style="width:100px">Check-out</th>
                        <th style="text-align:center; width:80px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siswaList as $i => $p)
                        @php
                            $gender = $p->student->gender ?? null;
                            $sudah = $p->checkin && $p->checkin->checkin_at;
                            $kembali = $sudah && $p->checkin->checkout_at;
                            $typeMap = [
                                'sakit' => ['label' => 'Sakit', 'class' => 'kep-sakit'],
                                'pulang' => ['label' => 'Pulang', 'class' => 'kep-pulang'],
                                'pesiar' => ['label' => 'Pesiar', 'class' => 'kep-pesiar'],
                                'perpulangan' => ['label' => 'Perpulangan', 'class' => 'kep-perpulangan'],
                            ];
                            $typeInfo = $typeMap[$p->type] ?? ['label' => ucfirst($p->type), 'class' => ''];
                        @endphp
                        <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                            <td style="text-align:center">{{ $i + 1 }}</td>
                            <td>{{ $p->student->nis ?? '-' }}</td>
                            <td>
                                <div style="font-weight: bold;">{{ $p->student->name ?? '-' }}</div>
                                <div style="font-size: 10px; color: #64748b;">
                                    {{ $p->student->dormitory->name ?? 'Tanpa Asrama' }}
                                </div>
                            </td>
                            <td style="text-align:center">
                                @if($gender === 'L')
                                    <span class="badge badge-l">L</span>
                                @elseif($gender === 'P')
                                    <span class="badge badge-p">P</span>
                                @else
                                    <span style="color:#94a3b8">&#8212;</span>
                                @endif
                            </td>
                            <td>{{ $p->reason ?? '-' }}</td>
                            <td style="text-align:center">
                                <span class="badge {{ $typeInfo['class'] }}">{{ $typeInfo['label'] }}</span>
                            </td>
                            <td>
                                @if($sudah)
                                    {{ \Carbon\Carbon::parse($p->checkin->checkin_at)->format('d M Y H:i') }}
                                @else
                                    <span style="color:#94a3b8">&#8212;</span>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($p->checkin->checkout_at)->format('d M Y H:i') }}
                            </td>
                            <td style="text-align:center">
                                @if($kembali)
                                    <span class="badge badge-kembali" style="white-space: nowrap;">Sudah Kembali</span>
                                @elseif($sudah)
                                    <span class="badge badge-sudah-ci" style="white-space: nowrap;">Sudah Check-in</span>
                                @else
                                    <span class="badge badge-belum" style="white-space: nowrap;">Belum Check-in</span>
                                @endif
                            </td>


                        </tr>
                    @endforeach
                </tbody>
            </table>

        @empty
            <p class="empty">Tidak ada data sesuai filter</p>
        @endforelse

        {{-- Footer — pakai table bukan flex --}}
        <table class="footer-table">
            <tr>
                <td>Sistem Perizinan Siswa</td>
                <td class="footer-right">Dicetak: {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

</body>

</html>