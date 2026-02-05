<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Izin Kepulangan</title>
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

        /* JUDUL */
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

        /* ISI */
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

        /* FOOTER */
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
            margin: 6px 0;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>

    <!-- KOP -->
    <table class="kop">
        <tr>
            <td width="15%">
                @if($school['logo'])
                    <img src="{{ $school['logo'] }}" style="width:80px;">
                @endif

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
        <h2>SURAT IZIN KEPULANGAN SISWA</h2>
    </div>

    <!-- ISI -->
    <div class="content">
        <p>
            Yang bertanda tangan di bawah ini, pihak sekolah, dengan ini memberikan izin
            kepada:
        </p>

        <table class="data">
            <tr>
                <td class="label">Nama</td>
                <td class="separator">:</td>
                <td>{{ $permission->student->name }}</td>
            </tr>
            <tr>
                <td class="label">NIS</td>
                <td class="separator">:</td>
                <td>{{ $permission->student->nis }}</td>
            </tr>
            <tr>
                <td class="label">Kelas</td>
                <td class="separator">:</td>
                <td>{{ $permission->student->class->name }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Izin</td>
                <td class="separator">:</td>
                <td>{{ ucfirst($permission->type) }}</td>
            </tr>
            <tr>
                <td class="label">Waktu</td>
                <td class="separator">:</td>
                <td>
                    {{ $permission->start_at->translatedFormat('d F Y H:i') }}
                    s/d
                    {{ $permission->end_at->translatedFormat('d F Y H:i') }}
                </td>
            </tr>
        </table>

        <p>
            Demikian surat izin ini dibuat untuk dipergunakan sebagaimana mestinya.
        </p>
    </div>

    <!-- TTD + QR -->
    <div class="footer">
        <div class="signature">
            <p>{{ now()->translatedFormat('d F Y') }}</p>
            <p>Petugas Perizinan</p>

            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('permissions.verify', $permission->qr_token)) }}"
                alt="QR Verifikasi">

            <p><strong>{{ $permission->approver->name }}</strong></p>
        </div>
        <div class="clear"></div>
    </div>

</body>

</html>