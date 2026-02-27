<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Perizinan Siswa</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5px;
            color: #111827;
            margin: 18px;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }

        h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .info-box {
            font-size: 9px;
            margin-bottom: 10px;
        }

        .info-box td {
            padding: 1px 8px 1px 0;
        }

        .label-filter {
            color: #555;
            width: 70px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #000;
            color: #fff;
            font-size: 8.5px;
            padding: 5px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        td {
            padding: 5px;
            border-bottom: 0.5px solid #ddd;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background: #f5f5f5;
        }

        .student-name {
            font-weight: bold;
        }

        .nis-text {
            font-size: 8.5px;
            color: #555;
        }

        .reason-text {
            font-size: 8.5px;
            line-height: 1.2;
        }

        .badge {
            padding: 1px 4px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 2px;
            display: inline-block;
        }

        .approved {
            background: #e6f4ea;
            color: #1b5e20;
        }

        .rejected {
            background: #fdecea;
            color: #b71c1c;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Laporan Perizinan Siswa</h2>
    </div>

    <div class="info-box" style="font-size:9px; margin-bottom:8px;">
        <strong>Filter:</strong>

        Periode:
        <span>
            {{ $request->start_date && $request->end_date
    ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') . ' - ' .
    \Carbon\Carbon::parse($request->end_date)->format('d/m/Y')
    : 'Semua Tanggal' }}
        </span>

        &nbsp; | &nbsp;

        @if($request->status)
            Status:
            <span>
                @if ($request->status === 'approved')
                    Disetujui
                @elseif ($request->status === 'rejected')
                    Ditolak
                @elseif ($request->status === 'pending')
                    Pending
                @else
                    Semua
                @endif
            </span>
        @endif


        @if($request->checkin_status)
            &nbsp; | &nbsp;
            Keberadaan:
            <span>
                @if($request->checkin_status === 'belum_checkout')
                    Belum Checkout
                @elseif($request->checkin_status === 'dirumah')
                    Dirumah
                @elseif($request->checkin_status === 'kembali')
                    Sudah Kembali
                @endif
            </span>
        @endif
        &nbsp; | &nbsp;

        Kelas: <span>{{ $kelas ?? 'Semua Kelas' }}</span>

    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="22%">Siswa</th>
                <th width="8%">Kelas</th>
                <th width="8%">Jenis</th>
                <th width="18%">Periode</th>
                <th width="22%">Alasan</th>
                <th width="18%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permissions as $i => $p)
                <tr>
                    <td align="center">{{ $i + 1 }}</td>

                    <td>
                        <div class="student-name">{{ $p->student->name }}</div>
                        <div class="nis-text">NIS: {{ $p->student->nis ?? '-' }}</div>
                    </td>

                    <td>{{ $p->student->class->name ?? '-' }}</td>

                    <td>{{ ucfirst($p->type) }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($p->start_at)->format('d/m/y') }}
                        -
                        {{ \Carbon\Carbon::parse($p->end_at)->format('d/m/y') }}
                    </td>

                    <td class="reason-text">
                        {{ \Illuminate\Support\Str::limit($p->reason, 80) }}
                    </td>

                    <td>
                        @if ($p->status === 'approved')
                            <span class="badge approved">Disetujui</span>

                        @elseif ($p->status === 'rejected')
                            <span class="badge rejected">Ditolak</span>

                            @if($p->reject_reason)
                                <div style="font-size:8px; color:#b71c1c; margin-top:2px; font-style:italic; line-height:1.2;">
                                    {{ \Illuminate\Support\Str::limit($p->reject_reason, 90) }}
                                </div>
                            @endif

                        @else
                            <span class="badge pending">Pending</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" align="center" style="padding:15px; color:#999;">
                        Tidak ada data.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>