@extends('layouts.app')

@section('title', 'Check-out Siswa')

@section('content')
    <div class="p-6 bg-white rounded-2xl space-y-6 shadow-sm">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold flex items-center gap-2 text-blue-600">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Check-out Siswa
                </h1>

                @if (Auth::user()->role !== 'smaplusasthahannas')

                    <p class="text-sm text-slate-500 mt-1">
                        Scan QR atau input NIS untuk mengembalikan siswa ke kelas
                    </p>
                @endif
            </div>
        </div>

        @if (Auth::user()->role !== 'smaplusasthahannas')
            <div class="p-4 bg-slate-50 border rounded-2xl flex flex-col md:flex-row gap-3">
                <input id="barcodeInput" autofocus
                    class="flex-1 px-4 py-3 border rounded-xl text-lg focus:ring focus:ring-blue-200"
                    placeholder="Scan QR / input NIS">
                <button id="btnCheckout"
                    class="px-6 py-3 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Check-out
                </button>
            </div>
        @endif


        <div class="bg-white border rounded-2xl overflow-hidden p-4">
            <table id="checkoutTable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
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
        // ── DataTable ─────────────────────────────────────────────────────────────
        const table = $('#checkoutTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { url: '{{ route('checkout.data') }}' },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nis', name: 'nis' },
                { data: 'name', name: 'name' },
                { data: 'class', name: 'class' },
                { data: 'keperluan', name: 'keperluan', orderable: false },
                { data: 'checkin_fmt', name: 'checkin_at' },
                { data: 'checkout_fmt', name: 'checkout_at' },
                { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
            ],
            order: [[6, 'desc']],
            pageLength: 10,
            responsive: true,
        });

        // ── Check-out ─────────────────────────────────────────────────────────────
        function submitCheckout() {
            const input = $('#barcodeInput');
            const code = input.val().trim();
            if (!code) return;

            $.post('/checkout', { _token: '{{ csrf_token() }}', code })
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
            if (e.which === 13) { e.preventDefault(); submitCheckout(); }
        });
        $('#btnCheckout').on('click', submitCheckout);

        setInterval(() => document.getElementById('barcodeInput')?.focus(), 600);
    </script>
@endpush