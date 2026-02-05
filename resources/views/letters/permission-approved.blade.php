<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Izin Kepulangan</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.6;
        }

        .kop {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }

        .kop p {
            margin: 2px 0;
            font-size: 11pt;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 25px 0;
        }

        .content {
            text-align: justify;
        }

        table {
            width: 100%;
            margin: 15px 0;
        }

        table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .signature {
            margin-top: 50px;
            width: 40%;
            float: right;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="kop">
        <h1>SEKOLAH MENENGAH XYZ</h1>
        <p>Jl. Pendidikan No. 1 – Kota</p>
        <p>Telp. (021) 123456</p>
    </div>

    <div class="title">
        SURAT IZIN KEPULANGAN SISWA
    </div>

    <div class="content">
        <p>
            Yang bertanda tangan di bawah ini, pihak sekolah, dengan ini memberikan izin
            kepada:
        </p>

        <table>
            <tr>
                <td width="150">Nama</td>
                <td>: {{ $permission->student->name }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>: {{ $permission->student->nis }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: {{ $permission->student->class->name }}</td>
            </tr>
            <tr>
                <td>Jenis Izin</td>
                <td>: {{ ucfirst($permission->type) }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>
                    : {{ $permission->start_at->format('d M Y H:i') }}
                    s/d
                    {{ $permission->end_at->format('d M Y H:i') }}
                </td>
            </tr>
        </table>

        <p>
            Demikian surat izin ini dibuat untuk dipergunakan sebagaimana mestinya.
        </p>
    </div>

    <div class="signature">
        <p>{{ now()->format('d M Y') }}</p>
        <p><b>Petugas Perizinan</b></p>
        <br><br><br>
        <p><u>{{ $permission->approver->name }}</u></p>
    </div>

</body>

</html>