<table>
    <tr>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Izin</th>
        <th>Terlambat</th>
        <th>Ringan</th>
        <th>Sedang</th>
        <th>Berat</th>
    </tr>
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
</table>