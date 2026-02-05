<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Izin Wali Kelas</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 40px 50px;
            color: #000;
        }

        /* KOP */
        .kop {
            width: 100%;
            margin-bottom: 10px;
        }

        .kop td {
            vertical-align: middle;
        }

        .kop img {
            width: 80px;
        }

        .kop .school {
            text-align: center;
        }

        .kop .school h1 {
            font-size: 16pt;
            margin: 0;
            text-transform: uppercase;
        }

        .kop .school p {
            margin: 2px 0;
            font-size: 11pt;
        }

        .divider {
            border-top: 3px solid #000;
            margin: 10px 0 25px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .header p {
            margin-top: 5px;
            font-size: 11pt;
        }

        .content p {
            margin: 0 0 12px;
            text-align: justify;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 20px;
        }

        table.data td {
            padding: 4px 0;
            vertical-align: top;
        }

        table.data td.label {
            width: 150px;
        }

        table.data td.separator {
            width: 10px;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
        }

        .signature {
            width: 45%;
            float: right;
            text-align: center;
        }

        .signature img {
            width: 90px;
            margin: 5px 0;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>

    <!-- KOP SEKOLAH -->
    <table class="kop">
        <tr>
            <td width="15%">
                <img src="{{ public_path('images/logosekolah.jpg') }}">
            </td>
            <td class="school">
                <h1>{{ $school['name'] }}</h1>
                <p>{{ $school['address'] }}</p>
                <p>Telp. {{ $school['phone'] }} • Email: {{ $school['email'] }}</p>
            </td>
            <td width="15%"></td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- JUDUL -->
    <div class="header">
        <h2>Surat Izin Wali Kelas</h2>
        <p>Nomor: {{ $nomor }}</p>
    </div>

    <!-- ISI -->
    <div class="content">
        <p>
            Yang bertanda tangan di bawah ini, Wali Kelas {{ $school['name'] }},
            dengan ini menerangkan bahwa:
        </p>

        <table class="data">
            <tr>
                <td class="label">Nama Siswa</td>
                <td class="separator">:</td>
                <td>{{ $student->name }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Izin</td>
                <td class="separator">:</td>
                <td>{{ ucfirst($type) }}</td>
            </tr>
            <tr>
                <td class="label">Waktu Izin</td>
                <td class="separator">:</td>
                <td>
                    {{ \Carbon\Carbon::parse($start)->translatedFormat('d F Y H:i') }}
                    s/d
                    {{ \Carbon\Carbon::parse($end)->translatedFormat('d F Y H:i') }}
                </td>
            </tr>
        </table>

        <p>
            Berdasarkan pertimbangan yang ada, siswa tersebut diberikan izin
            sesuai keterangan di atas. Surat ini dibuat untuk dipergunakan
            sebagaimana mestinya.
        </p>
    </div>

    <!-- TANDA TANGAN + QR -->
    <div class="footer">
        <div class="signature">
            <p>{{ $city }}, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Wali Kelas</p>

            <img src="{{ $qrCode }}">

            <p><strong>{{ $wali->name }}</strong></p>
        </div>
        <div class="clear"></div>
    </div>

</body>

</html>