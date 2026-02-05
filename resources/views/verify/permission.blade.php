<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Surat Izin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 16px;
            color: #111827;
        }

        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .08);
            overflow: hidden;
        }

        .header {
            padding: 24px;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: #ffffff;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .header p {
            margin-top: 6px;
            font-size: 13px;
            opacity: .9;
        }

        .content {
            padding: 24px;
        }

        .status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            margin: 0 auto 20px;
            width: fit-content;
        }

        .status.approved {
            background: #dcfce7;
            color: #166534;
        }

        .status.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            font-size: 14px;
        }

        .item {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 8px;
        }

        .label {
            color: #6b7280;
        }

        .value {
            font-weight: 500;
        }

        .reject-box {
            margin-top: 16px;
            padding: 14px;
            border-radius: 12px;
            background: #fff1f2;
            color: #991b1b;
            font-size: 14px;
        }

        .footer {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px dashed #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }

        .badge {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 11px;
            background: #eef2ff;
            color: #1e3a8a;
            font-weight: 600;
        }

        @media (min-width: 768px) {
            body {
                padding: 24px;
            }

            .header h2 {
                font-size: 22px;
            }

            .details {
                gap: 16px;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <div class="card">

            <div class="header">
                <h2>Verifikasi Surat Izin Siswa</h2>
            </div>

            <div class="content">

                @if($permission->status === 'approved')
                    <div class="status approved">✅ Surat Disetujui</div>
                @elseif($permission->status === 'rejected')
                    <div class="status rejected">❌ Permohonan Ditolak</div>
                @endif

                <div class="details">
                    <div class="item">
                        <div class="label">Nama Siswa</div>
                        <div class="value">{{ $permission->student->name }}</div>
                    </div>

                    <div class="item">
                        <div class="label">Kelas</div>
                        <div class="value">{{ $permission->student->class->name ?? '-' }}</div>
                    </div>

                    <div class="item">
                        <div class="label">Jenis Izin</div>
                        <div class="value">{{ ucfirst($permission->type) }}</div>
                    </div>

                    <div class="item">
                        <div class="label">Waktu</div>
                        <div class="value">
                            {{ $permission->start_at->translatedFormat('d F Y H:i') }}<br>
                            s/d {{ $permission->end_at->translatedFormat('d F Y H:i') }}
                        </div>
                    </div>

                    <div class="item">
                        <div class="label">Diverifikasi</div>
                        <div class="value">{{ $permission->approver->name ?? '-' }}</div>
                    </div>
                </div>

                @if($permission->status === 'rejected')
                    <div class="reject-box">
                        <b>Alasan Penolakan:</b><br>
                        “{{ $permission->reject_reason }}”
                    </div>
                @endif

                <div class="footer">
                    Surat ini diverifikasi secara digital
                    <div class="badge">
                        Token: {{ $permission->qr_token }}
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>