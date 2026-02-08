<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Asesmen Kantor Manggala</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 40px 50px;
            color: #000;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .title div {
            margin: 2px 0;
        }

        .divider {
            border-top: 3px solid #000;
            margin: 10px 0 25px;
        }

        p {
            margin: 0 0 10px;
            text-align: justify;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 15px 40px;
        }

        table.data td {
            padding: 3px 0;
            vertical-align: top;
        }

        table.data td.label {
            width: 140px;
        }

        table.data td.separator {
            width: 10px;
        }

        .highlight {
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature {
            margin-top: 40px;
            width: 40%;
            margin-left: auto;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- JUDUL -->
    <div class="title">
        <div>ASESMEN KANTOR MANGGALA</div>
        <div>PENGAJUAN PERMOHONAN IZIN BERMALAM / PESIAR SISWA</div>
        <div>SMA PLUS ASTHA HANNAS</div>
    </div>

    <div class="divider"></div>

    <!-- ISI -->
    <p>
        Berdasarkan permohonan izin bermalam/pesiar yang telah diajukan, dengan ini pihak sekolah
        <span class="highlight">memberikan persetujuan</span> izin bermalam/pesiar kepada:
    </p>

    <table class="data">
        <tr>
            <td class="label">Nama Siswa</td>
            <td class="separator">:</td>
            <td>{{ $permission->student->name }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="separator">:</td>
            <td>{{ $permission->student->class->name }}</td>
        </tr>
        <tr>
            <td class="label">Barak</td>
            <td class="separator">:</td>
            <td>{{ $permission->student->dormitory->name ?? '—' }}</td>
        </tr>
    </table>

    <p>
        Izin bermalam/pesiar tersebut diberikan untuk keperluan:
        <strong>{{ strtoupper($permission->reason) }}</strong>
        dengan ketentuan sebagai berikut:
    </p>

    <table class="data">
        <tr>
            <td class="label">Hari</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($permission->start_at)->translatedFormat('l') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($permission->start_at)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Pukul</td>
            <td class="separator">:</td>
            <td>{{ \Carbon\Carbon::parse($permission->start_at)->format('H:i') }} WIB</td>
        </tr>
    </table>

    <p>
        Siswa yang bersangkutan diwajibkan kembali ke kampus sesuai waktu yang telah ditentukan
        serta mematuhi seluruh ketentuan dan tata tertib yang berlaku.
    </p>

    <p>
        Demikian persetujuan izin bermalam/pesiar ini dibuat untuk digunakan sebagaimana mestinya.
    </p>

    <!-- TTD -->
    <div class="signature">
        <p>Binong - Subang, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Wakasek Bid. Pengasuhan,</p>

        <br><br><br>

        <p>
            <strong>Drs. Anwari Hilmy</strong><br>
            Komisaris Besar Polisi (P)
        </p>
    </div>

</body>

</html>