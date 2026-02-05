<p>
    Permohonan izin atas nama siswa berikut:
</p>

<table>
    <tr>
        <td width="150">Nama</td>
        <td>: {{ $permission->student->name }}</td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>: {{ $permission->student->class->name }}</td>
    </tr>
</table>

<p>
    <b>DITOLAK</b> dengan alasan:
</p>

<p style="margin-left:20px">
    "{{ $permission->reject_reason }}"
</p>