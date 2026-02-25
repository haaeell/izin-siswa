@extends('layouts.app')

@section('title', 'Check-in Siswa')

@section('content')
    <div class="p-6 bg-white rounded-2xl space-y-6 shadow-sm">

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

        <div class="p-4 bg-slate-50 border rounded-2xl flex flex-col md:flex-row gap-3">
            <input id="barcodeInput" autofocus
                class="flex-1 px-4 py-3 border rounded-xl text-lg focus:ring focus:ring-green-200"
                placeholder="Scan QR / input NIS">
            <button id="btnCheckin"
                class="px-6 py-3 rounded-xl bg-green-600 text-white font-medium hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> Check-in
            </button>
        </div>

        <div class="p-4 bg-slate-50 border rounded-2xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="text-sm font-medium">Tanggal Mulai</label>
                <input type="date" id="filterStart"
                    class="w-full border rounded-xl px-3 py-2 focus:ring focus:ring-green-200">
            </div>
            <div>
                <label class="text-sm font-medium">Tanggal Akhir</label>
                <input type="date" id="filterEnd"
                    class="w-full border rounded-xl px-3 py-2 focus:ring focus:ring-green-200">
            </div>
            <div>
                <label class="text-sm font-medium">Kelas</label>
                <select id="filterKelas" class="w-full border rounded-xl px-3 py-2 focus:ring focus:ring-green-200">
                    <option value="">Semua Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">Asrama</label>
                <select id="filterAsrama" class="w-full border rounded-xl px-3 py-2 focus:ring focus:ring-green-200">
                    <option value="">Semua Asrama</option>
                    @foreach ($dormitories as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button id="btnFilter" class="w-full px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
            </div>
        </div>

        <div class="bg-white border rounded-2xl overflow-hidden p-4">
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
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ── Filter state ─────────────────────────────────────────────────────────
        let fStart = '';
        let fEnd = '';
        let fKelas = '';
        let fAsrama = '';

        // ── DataTable ─────────────────────────────────────────────────────────────
        const table = $('#checkinTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('checkin.data') }}',
                data: d => {
                    d.start_date = fStart;
                    d.end_date = fEnd;
                    d.class_id = fKelas;
                    d.dormitory_id = fAsrama;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nis', name: 'nis' },
                { data: 'name', name: 'name' },
                { data: 'class', name: 'class' },
                { data: 'asrama', name: 'asrama' },
                { data: 'keperluan', name: 'keperluan', orderable: false },
                { data: 'checkin_fmt', name: 'checkin_at' },
                { data: 'checkout_fmt', name: 'checkout_at' },
                { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
            ],
            order: [[6, 'desc']],
            pageLength: 10,
            responsive: true,
        });

        // ── Tombol Filter ─────────────────────────────────────────────────────────
        $('#btnFilter').on('click', function () {
            fStart = $('#filterStart').val();
            fEnd = $('#filterEnd').val();
            fKelas = $('#filterKelas').val();
            fAsrama = $('#filterAsrama').val();
            table.ajax.reload();
        });

        // ── Check-in ──────────────────────────────────────────────────────────────
        function submitCheckin() {
            const input = $('#barcodeInput');
            const code = input.val().trim();
            if (!code) return;

            $.post('/checkin', { _token: '{{ csrf_token() }}', code })
                .done(res => {
                    if (!res.success) {
                        Swal.fire('Info', res.message, 'warning');
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => table.ajax.reload());
                    }
                });

            input.val('');
        }

        $('#barcodeInput').on('keypress', function (e) {
            if (e.which === 13) { e.preventDefault(); submitCheckin(); }
        });
        $('#btnCheckin').on('click', submitCheckin);

        setInterval(() => document.getElementById('barcodeInput')?.focus(), 600);
    </script>
@endpush