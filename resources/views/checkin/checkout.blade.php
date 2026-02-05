@extends('layouts.app')

@section('title', 'Check-out Siswa')

@section('content')
    <div class="p-6 bg-white rounded-2xl space-y-6 shadow-sm">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold flex items-center gap-2 text-blue-600">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Check-out Siswa
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Scan QR atau input NIS untuk mengembalikan siswa ke kelas
                </p>
            </div>
        </div>

        {{-- INPUT --}}
        <div class="p-4 bg-slate-50 border rounded-2xl flex flex-col md:flex-row gap-3">
            <input id="barcodeInput" autofocus
                class="flex-1 px-4 py-3 border rounded-xl text-lg focus:ring focus:ring-blue-200"
                placeholder="Scan QR / input NIS">

            <button id="btnCheckout"
                class="px-6 py-3 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                Check-out
            </button>
        </div>

        {{-- TABLE --}}
        <div class="bg-white border rounded-2xl overflow-hidden">
            <table id="checkoutTable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Keperluan</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($checkins as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-medium">
                                {{ $item->permission->student->name }}
                            </td>
                            <td>
                                {{ $item->permission->student->class->name }}
                            </td>
                            <td>
                                {{ $item->permission->reason ?? '-' }}
                            </td>
                            <td class="text-green-600 font-medium">
                                {{ optional($item->checkin_at)->format('H:i') ?? '-' }}
                            </td>
                            <td class="text-blue-600 font-medium">
                                {{ optional($item->checkout_at)->format('H:i') ?? '-' }}
                            </td>
                            <td>
                                @if($item->checkin_at && !$item->checkout_at)
                                    <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                        Di luar
                                    </span>
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
        function submitCheckout() {
            const input = $('#barcodeInput');
            const code = input.val();
            if (!code) return;

            $.post('/checkout', {
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

        $('#barcodeInput').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                submitCheckout();
            }
        });

        $('#btnCheckout').on('click', submitCheckout);

        setInterval(() => {
            document.getElementById('barcodeInput')?.focus();
        }, 600);

        // DATATABLES
        $('#checkoutTable').DataTable({
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
                zeroRecords: "Tidak ada siswa di luar"
            }
        });
    </script>
@endpush