<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Gagal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(180deg, #fef2f2, #fff);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            color: #111827;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px;
            padding: 28px 24px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .08);
        }

        .icon {
            font-size: 42px;
            margin-bottom: 12px;
        }

        h2 {
            margin: 0 0 10px;
            font-size: 20px;
            font-weight: 600;
            color: #991b1b;
        }

        p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: #4b5563;
        }

        @media (min-width: 768px) {
            h2 {
                font-size: 22px;
            }

            p {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="icon">❌</div>
        <h2>Surat Tidak Valid</h2>
        <p>
            QR Code tidak terdaftar atau<br>
            surat sudah tidak berlaku.
        </p>
    </div>

</body>

</html>