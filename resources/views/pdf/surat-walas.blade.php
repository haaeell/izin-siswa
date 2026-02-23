<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Rekomendasi Izin</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.2;
            margin: 40px 50px;
            color: #000;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .title .main {
            font-size: 14pt;
        }

        .title .sub {
            font-size: 13pt;
            margin-top: 5px;
        }

        hr {
            border: none;
            border-top: 2px solid #000;
            margin: 10px 0 25px;
        }

        p {
            margin: 0 0 12px;
            text-align: justify;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 15px;
        }

        table.data td {
            padding: 3px 0;
            vertical-align: top;
        }

        table.data td.label {
            width: 220px;
        }

        table.data td.separator {
            width: 10px;
        }

        .section-title {
            margin-top: 20px;
            font-weight: bold;
        }

        .signature {
            margin-top: 40px;
            width: 40%;
            margin-left: auto;
            text-align: center;
        }

        .signature .name {
            margin-top: 10px;
            /* dari 60px → 10px, karena QR sudah mengisi ruang TTD */
            font-weight: bold;
            text-decoration: underline;
        }

        .small {
            font-size: 11pt;
        }

        .checkbox {
            font-family: DejaVu Sans;
        }

        .data-indent {
            margin-left: 40px;
        }
    </style>
</head>

<body>

    <!-- JUDUL -->
    <div class="title">
        <div class="main">SURAT REKOMENDASI</div>
        <div class="sub">PERMOHONAN IZIN BERMALAM (IB) / PESIAR</div>
    </div>

    <hr>

    <!-- ISI PEMBUKA -->
    <p>
        Yang bertanda tangan di bawah ini Wali Kelas:
    </p>

    <p>
        Dengan ini mengajukan <strong>PERMOHONAN REKOMENDASI IZIN BERMALAM (IB) / PESIAR</strong>
        untuk meninggalkan sekolah. Berdasarkan permohonan ini dan keterangan yang disampaikan
        dari Orang Tua/Wali, menerangkan bahwa:
    </p>

    <!-- DATA SISWA -->
    <table class="data data-indent">
        <tr>
            <td class="label">Nama Siswa</td>
            <td class="separator">:</td>
            <td>{{ $student->name }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="separator">:</td>
            <td>{{ $student->class->name ?? '_________________' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Induk Siswa</td>
            <td class="separator">:</td>
            <td>{{ $student->nis ?? '_________________' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="separator">:</td>
            <td>{{ $address ?? '_________________' }}</td>
        </tr>
    </table>

    <hr>

    <p>
        Adalah <strong>BENAR</strong> siswa SMA Plus Astha Hannas, mohon kiranya diberikan izin
        untuk meninggalkan Asrama / Sekolah untuk keperluan
        <strong>{{ strtoupper($reason) }}</strong> pada:
    </p>

    <!-- WAKTU IZIN -->
    <table class="data data-indent">
        <tr>
            <td class="label">Hari</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($start)->translatedFormat('l') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($start)->translatedFormat('d / m / Y') }}</td>
        </tr>
        <tr>
            <td class="label">Pukul</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($start)->format('H:i') }} WIB</td>
        </tr>
    </table>

    <p>
        Dan kembali masuk Kampus pada:
    </p>

    <table class="data data-indent">
        <tr>
            <td class="label">Hari</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($end)->translatedFormat('l') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($end)->translatedFormat('d / m / Y') }}</td>
        </tr>
        <tr>
            <td class="label">Pukul</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($end)->format('H:i') }} WIB</td>
        </tr>
    </table>

    <p>
        Demikian surat rekomendasi ini dibuat untuk digunakan sebagaimana mestinya.
    </p>

    <!-- TTD -->
    <div class="signature">
        <p>Binong - Subang, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Wali Kelas,</p>

        <img src="{{ $qrCode }}" style="width: 80px; height: 80px; display: block; margin: 6px auto 2px;">
        <p style="font-size: 8pt; margin: 0 0 0; color: #555; text-align: center;">Scan untuk verifikasi</p>

        <div class="name" style="margin-top: 10px;">{{ $wali->name }}</div>
    </div>

</body>

</html>