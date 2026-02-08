@extends('layouts.app')

@section('title', 'Check-in Siswa')

@section('content')
    <div class="p-6 bg-white rounded-2xl space-y-6 shadow-sm">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold flex items-center gap-2 text-green-600">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    Check-in Siswa
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Scan QR atau input NIS untuk mencatat keluar masuk siswa
                </p>
            </div>
        </div>

        {{-- INPUT CHECKIN --}}
        <div class="p-4 bg-slate-50 border rounded-2xl flex flex-col md:flex-row gap-3">
            <input id="barcodeInput" autofocus
                class="flex-1 px-4 py-3 border rounded-xl text-lg focus:ring focus:ring-green-200"
                placeholder="Scan QR / input NIS">

            <button id="btnCheckin"
                class="px-6 py-3 rounded-xl bg-green-600 text-white font-medium hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i>
                Check-in
            </button>
        </div>

        {{-- FILTER --}}
        <form method="GET"
            class="p-4 bg-slate-50 border rounded-2xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

            <div>
                <label class="text-sm font-medium">Tanggal Mulai</label>
                <input type="date" name="start_date"
                    class="w-full border rounded-xl px-3 py-2 focus:ring focus:ring-green-200">
            </div>

            <div>
                <label class="text-sm font-medium">Tanggal Akhir</label>
                <input type="date" name="end_date"
                    class="w-full border rounded-xl px-3 py-2 focus:ring focus:ring-green-200">
            </div>

            <div>
                <label class="text-sm font-medium">Kelas</label>
                <select name="class_id" class="w-full border rounded-xl px-3 py-2 focus:ring focus:ring-green-200">
                    <option value="">Semua Kelas</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-medium">Asrama</label>
                <select name="dormitory_id" class="w-full border rounded-xl px-3 py-2 focus:ring focus:ring-green-200">
                    <option value="">Semua Asrama</option>
                </select>
            </div>

            <div class="flex items-end">
                <button class="w-full px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>


        {{-- TABLE --}}
        <div class="bg-white border rounded-2xl overflow-hidden">
            <table id="checkinTable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Asrama</th>
                        <th>Keperluan</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($checkins as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->permission->student->nis }}</td>
                            <td class="font-medium">{{ $item->permission->student->name }}</td>
                            <td>{{ $item->permission->student->class->name ?? '-' }}</td>
                            <td>{{ $item->permission->student->dormitory->name ?? '-' }}</td>
                            <td>{{ $item->permission->reason ?? '-' }}</td>
                            <td class="text-green-600 font-medium">
                                {{ $item->checkin_at?->translatedFormat('l, d F Y H:i') ?? '-' }}</td>
                            <td class="text-blue-600 font-medium">
                                {{ $item->checkout_at?->translatedFormat('l, d F Y H:i') ?? '-' }}</td>
                            <td class="text-nowrap">
                                @if ($item->status === 'TERLAMBAT')
                                    @php
                                        $checkin = \Carbon\Carbon::parse($item->checkin_at);
                                        $endAt = \Carbon\Carbon::parse($item->permission->end_at);
                                        $diff = $checkin->diff($endAt);
                                    @endphp
                                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                        TERLAMBAT
                                        @if ($diff->d > 0)
                                            {{ $diff->d }} hari
                                        @endif
                                        @if ($diff->h > 0)
                                            {{ $diff->h }} jam
                                        @endif
                                        @if ($diff->i > 0)
                                            {{ $diff->i }} menit
                                        @endif
                                    </span>
                                @elseif($item->status === 'TEPAT WAKTU')
                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">TEPAT
                                        WAKTU</span>
                                @elseif($item->status === 'DI LUAR')
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">PULANG</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function submitCheckin() {
            const input = $('#barcodeInput');
            const code = input.val();
            if (!code) return;

            $.post('/checkin', {
                _token: '{{ csrf_token() }}',
                code: code
            }).done(res => {
                if (!res.success) {
                    Swal.fire('Info', res.message, 'warning');
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            });

            input.val('');
        }

        $('#barcodeInput').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                submitCheckin();
            }
        });

        $('#btnCheckin').on('click', submitCheckin);

        setInterval(() => {
            document.getElementById('barcodeInput')?.focus();
        }, 600);

        $('#checkinTable').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "‹",
                    next: "›"
                },
                zeroRecords: "Data tidak ditemukan"
            }
        });
    </script>
@endpush
