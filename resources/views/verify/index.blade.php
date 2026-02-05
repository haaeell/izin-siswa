<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Surat Izin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
            color: #111827;
        }

        .card {
            max-width: 480px;
            margin: auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 13px;
            opacity: .9;
        }

        .content {
            padding: 20px;
        }

        .status {
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .status.approved {
            background: #dcfce7;
            color: #166534;
        }

        .status.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table td {
            padding: 6px 0;
            vertical-align: top;
        }

        table td:first-child {
            width: 130px;
            color: #6b7280;
        }

        .footer {
            border-top: 1px dashed #e5e7eb;
            margin-top: 20px;
            padding-top: 15px;
            font-size: 12px;
            text-align: center;
            color: #6b7280;
        }

        .badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            background: #e0e7ff;
            color: #1e3a8a;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="header">
            <h2>Verifikasi Surat Izin Siswa</h2>
            <p>{{ config('app.name', 'Sistem Perizinan Sekolah') }}</p>
        </div>

        <div class="content">

            {{-- STATUS --}}
            @if($permission->status === 'approved')
                <div class="status approved">
                    ✅ SURAT SAH & DISETUJUI
                </div>
            @elseif($permission->status === 'rejected')
                <div class="status rejected">
                    ❌ PERMOHONAN DITOLAK
                </div>
            @endif

            {{-- DETAIL --}}
            <table>
                <tr>
                    <td>Nama Siswa</td>
                    <td>: {{ $permission->student->name }}</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>: {{ $permission->student->class->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Jenis Izin</td>
                    <td>: {{ ucfirst($permission->type) }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>
                        :
                        {{ $permission->start_at->translatedFormat('d F Y H:i') }}
                        <br>
                        s/d
                        {{ $permission->end_at->translatedFormat('d F Y H:i') }}
                    </td>
                </tr>
                <tr>
                    <td>Diverifikasi oleh</td>
                    <td>: {{ $permission->approver->name ?? '-' }}</td>
                </tr>
            </table>

            {{-- ALASAN TOLAK --}}
            @if($permission->status === 'rejected')
                <div style="margin-top:15px; color:#991b1b; font-size:14px;">
                    <b>Alasan Penolakan:</b><br>
                    “{{ $permission->reject_reason }}”
                </div>
            @endif

            <div class="footer">
                Surat ini diverifikasi secara digital<br>
                <span class="badge">
                    Token: {{ $permission->qr_token }}
                </span>
            </div>

        </div>
    </div>

</body>

</html>