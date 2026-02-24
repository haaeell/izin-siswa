<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Perizinan Siswa</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
            margin: 30px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        h2 {
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1f2937;
            margin: 0;
            font-size: 18px;
        }

        .info-box {
            background: #f9fafb;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            display: inline-block;
            min-width: 250px;
        }

        .info-box table {
            border: none;
            width: auto;
        }

        .info-box td {
            border: none;
            padding: 1px 10px 1px 0;
        }

        .label-filter {
            color: #6b7280;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background-color: #4f46e5;
            color: white;
            text-align: left;
            padding: 12px 10px;
            text-transform: uppercase;
            font-size: 10px;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .date-range {
            white-space: nowrap;
            font-weight: bold;
        }

        .reason-text {
            color: #4b5563;
            font-style: italic;
        }

        .student-name {
            font-weight: bold;
            color: #111827;
        }

        .nis-text {
            color: #4b5563;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Laporan Perizinan Siswa</h2>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td class="label-filter">Periode</td>
                <td>: <strong>{{ $request->start_date && $request->end_date
                    ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($request->end_date)->format('d/m/Y')
                    : 'Semua Tanggal' }}</strong>
                </td>
            </tr>
            <tr>
                <td class="label-filter">Status</td>
                <td>: <strong>
                    @if ($request->status === 'approved') Disetujui
                    @elseif ($request->status === 'rejected') Ditolak
                    @elseif ($request->status === 'pending') Pending
                    @else Semua Status
                    @endif
                </strong></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="11%">NIS</th>
                <th width="16%">Nama Siswa</th>
                <th width="9%">Kelas</th>
                <th width="8%">Jenis</th>
                <th width="18%">Tanggal</th>
                <th width="20%">Alasan</th>
                <th width="14%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permissions as $i => $p)
                <tr>
                    <td align="center">{{ $i + 1 }}</td>
                    <td class="nis-text">{{ $p->student->nis ?? '-' }}</td>
                    <td class="student-name">{{ $p->student->name }}</td>
                    <td>{{ $p->student->class->name ?? '-' }}</td>
                    <td>{{ ucfirst($p->type) }}</td>
                    <td class="date-range">
                        {{ \Carbon\Carbon::parse($p->start_at)->format('d M Y') }}
                        —
                        {{ \Carbon\Carbon::parse($p->end_at)->format('d M Y') }}
                    </td>
                    <td class="reason-text">"{{ $p->reason ?: '-' }}"</td>
                    <td>
                        @if ($p->status === 'approved')
                            <span style="background:#dcfce7; color:#15803d; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:10px; white-space:nowrap;">
                                Disetujui
                            </span>
                        @elseif ($p->status === 'rejected')
                            <span style="background:#fee2e2; color:#b91c1c; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:10px; white-space:nowrap;">
                                Ditolak
                            </span>
                            @if ($p->reject_reason)
                                <div style="font-size:10px; color:#b91c1c; margin-top:4px; font-style:italic;">
                                    {{ $p->reject_reason }}
                                </div>
                            @endif
                        @else
                            <span style="background:#fef9c3; color:#854d0e; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:10px; white-space:nowrap;">
                                Pending
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" align="center" style="padding:30px; color:#9ca3af;">
                        Tidak ada data perizinan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>