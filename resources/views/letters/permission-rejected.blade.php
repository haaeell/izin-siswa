<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Penolakan Izin</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 40px 50px;
            color: #000;
        }

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

        .reason {
            margin: 10px 0 15px 20px;
            padding-left: 10px;
            border-left: 3px solid #000;
            font-style: italic;
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
        <h2>Surat Penolakan Izin</h2>
        <p>Nomor: {{ $nomor }}</p>
    </div>

    <!-- ISI -->
    <div class="content">
        <p>
            Berdasarkan permohonan izin yang telah diajukan, dengan ini pihak sekolah
            menyampaikan bahwa permohonan izin siswa berikut:
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
                <td class="label">Jenis Izin</td>
                <td class="separator">:</td>
                <td>{{ ucfirst($permission->type) }}</td>
            </tr>
        </table>

        <p>
            Dengan ini permohonan izin tersebut
            <strong>TIDAK DAPAT DISETUJUI</strong>
            dengan alasan sebagai berikut:
        </p>

        <div class="reason">
            "{{ $permission->reject_reason }}"
        </div>

        <p>
            Demikian surat penolakan ini disampaikan untuk dipergunakan sebagaimana mestinya.
        </p>
    </div>

    <!-- TTD + QR -->
    <div class="footer">
        <div class="signature">
            <p>{{ $city }}, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Petugas Perizinan</p>

            <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('permissions.verify', $permission->qr_token)) }}">

            <p><strong>{{ $permission->approver->name }}</strong></p>
        </div>
        <div class="clear"></div>
    </div>

</body>

</html>