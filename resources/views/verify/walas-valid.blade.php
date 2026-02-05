<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Surat Wali Kelas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
            color: #0f172a;
        }

        .card {
            max-width: 520px;
            margin: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, .08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #0f766e, #0d9488);
            color: #fff;
            text-align: center;
            padding: 22px;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin-top: 6px;
            font-size: 13px;
            opacity: .9;
        }

        .status {
            margin: 20px;
            padding: 14px;
            border-radius: 12px;
            text-align: center;
            font-weight: 700;
            background: #dcfce7;
            color: #166534;
        }

        .content {
            padding: 0 20px 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        td {
            padding: 8px 0;
            vertical-align: top;
        }

        td.label {
            width: 140px;
            color: #64748b;
        }

        .footer {
            border-top: 1px dashed #e5e7eb;
            padding: 15px 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }

        .badge {
            display: inline-block;
            margin-top: 6px;
            padding: 4px 10px;
            font-size: 11px;
            border-radius: 999px;
            background: #ccfbf1;
            color: #065f46;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="header">
            <h2>Verifikasi Surat Wali Kelas</h2>
            <p>{{ config('app.name', 'Sistem Perizinan Sekolah') }}</p>
        </div>

        <div class="status">
            ✔ SURAT RESMI & VALID
        </div>

        <div class="content">
            <table>
                <tr>
                    <td class="label">Nama Siswa</td>
                    <td>: {{ $permission->student->name }}</td>
                </tr>
                <tr>
                    <td class="label">Kelas</td>
                    <td>: {{ $permission->student->class->name }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Izin</td>
                    <td>: {{ ucfirst($permission->type) }}</td>
                </tr>
                <tr>
                    <td class="label">Waktu</td>
                    <td>
                        :
                        {{ $permission->start_at->translatedFormat('d F Y H:i') }}
                        <br>
                        s/d
                        {{ $permission->end_at->translatedFormat('d F Y H:i') }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Wali Kelas</td>
                    <td>: {{ $permission->waliKelas->name }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Surat ini diverifikasi secara digital
            <div class="badge">
                Wali Kelas
            </div>
        </div>
    </div>

</body>

</html>