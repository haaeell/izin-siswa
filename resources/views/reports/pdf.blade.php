<h3>Laporan Aktivitas Siswa</h3>
<p>Periode: {{ $start }} s/d {{ $end }}</p>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Izin</th>
            <th>Terlambat</th>
            <th>R</th>
            <th>S</th>
            <th>B</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row->name }}</td>
                <td>{{ $row->class }}</td>
                <td>{{ $row->izin }}</td>
                <td>{{ $row->terlambat }}</td>
                <td>{{ $row->ringan }}</td>
                <td>{{ $row->sedang }}</td>
                <td>{{ $row->berat }}</td>
            </tr>
        @endforeach
    </tbody>
</table>