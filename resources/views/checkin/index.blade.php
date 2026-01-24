@extends('layouts.app')

@section('title', 'Check-in Masuk Siswa')

@section('content')
    <div class="p-6 bg-white rounded-xl">

        <h1 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-door-open text-green-600"></i>
            Check-in Masuk Siswa
        </h1>

        <div class="flex gap-2 mb-4">
            <button id="btnScan" class="px-4 py-2 bg-green-600 text-white rounded-lg flex items-center gap-2">
                <i class="fa-solid fa-qrcode"></i>
                Scan QR
            </button>

            <button id="btnUpload" class="px-4 py-2 bg-indigo-600 text-white rounded-lg flex items-center gap-2">
                <i class="fa-solid fa-upload"></i>
                Upload QR
            </button>

            <input type="file" id="qrFileInput" accept="image/*" class="hidden">

            <button id="btnManual" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg flex items-center gap-2">
                <i class="fa-solid fa-keyboard"></i>
                Input Manual
            </button>
        </div>

        <div id="scanSection" class="hidden mb-6">
            <div id="scanner" class="w-72"></div>
        </div>

        <div id="manualSection" class="hidden mb-6 p-4 border rounded-xl bg-slate-50">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="text-sm font-medium">Nama Siswa</label>
                    <select id="studentSelect" class="w-full">
                        <option value="">-- Cari Nama / NIS --</option>
                        @foreach($students as $p)
                            <option value="{{ $p->student->id }}" data-nis="{{ $p->student->nis }}"
                                data-kelas="{{ $p->student->class->name }}" data-type="{{ $p->type }}"
                                data-start="{{ $p->start_at?->format('d M Y H:i') }}"
                                data-end="{{ $p->end_at?->format('d M Y H:i') }}" data-reason="{{ $p->reason }}">
                                {{ $p->student->name }} - {{ $p->student->nis }}
                            </option>
                        @endforeach
                    </select>

                    <div id="infoSiswa"
                        class="hidden mt-4 rounded-xl border bg-gradient-to-br from-white to-slate-50 p-5 shadow-sm">

                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                                <i class="fa-solid fa-user"></i>
                            </div>

                            <div>
                                <p class="font-semibold text-slate-800" id="infoNama">-</p>
                                <p class="text-xs text-slate-500">
                                    NIS: <span id="infoNis"></span> •
                                    Kelas: <span id="infoKelas"></span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div class="bg-white rounded-lg p-3 border">
                                <p class="text-xs text-slate-500 mb-1">Jenis Izin</p>
                                <p class="font-medium text-slate-800" id="infoType"></p>
                            </div>

                            <div class="bg-white rounded-lg p-3 border">
                                <p class="text-xs text-slate-500 mb-1">Alasan</p>
                                <p class="font-medium text-slate-800" id="infoReason"></p>
                            </div>

                            <div class="bg-white rounded-lg p-3 border">
                                <p class="text-xs text-slate-500 mb-1">Mulai</p>
                                <p class="font-medium text-slate-800" id="infoStart"></p>
                            </div>

                            <div class="bg-white rounded-lg p-3 border">
                                <p class="text-xs text-slate-500 mb-1">Sampai</p>
                                <p class="font-medium text-slate-800" id="infoEnd"></p>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <button id="submitManual" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center gap-2">
                <span id="manualText">Simpan Check-in</span>
                <svg id="manualLoader" class="hidden w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </button>

        </div>

        <table id="datatable" class="w-full text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Waktu Masuk</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checkins as $i => $c)
                    <tr>
                        <td>{{ $loop->iteration}}</td>
                        <td>{{ $c->permission->student->name }}</td>
                        <td>{{ $c->permission->student->class->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->checkin_at)->format('d M Y H:i') }}</td>
                        <td>
                            @if($c->status === 'tepat_waktu')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">
                                    Masuk Tepat Waktu
                                </span>
                            @else
                                @php
                                    $diff = $c->checkin_at->diff($c->permission->end_at);

                                    if ($diff->d > 0) {
                                        $delay = $diff->d . ' hari ' . $diff->h . ' jam ' . $diff->i . ' menit';
                                    } elseif ($diff->h > 0) {
                                        $delay = $diff->h . ' jam ' . $diff->i . ' menit';
                                    } else {
                                        $delay = $diff->i . ' menit';
                                    }
                                @endphp
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">
                                    Masuk Terlambat
                                </span>
                                <div class="text-xs text-red-700 mt-1">Telat: {{ $delay }}</div>
                            @endif
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        $(document).ready(function () {
            $('#studentSelect').select2({
                placeholder: 'Cari nama atau NIS siswa',
                width: '100%'
            });

            $('#studentSelect').on('change', function () {
                const selected = $(this).find(':selected');

                if (!selected.val()) {
                    $('#infoSiswa').addClass('hidden');
                    return;
                }

                $('#infoNama').text(selected.text());
                $('#infoNis').text(selected.data('nis'));
                $('#infoKelas').text(selected.data('kelas'));
                $('#infoType').text(selected.data('type'));
                $('#infoStart').text(selected.data('start'));
                $('#infoEnd').text(selected.data('end'));
                $('#infoReason').text(selected.data('reason'));

                $('#infoSiswa').removeClass('hidden');
            });

            $('#datatable').DataTable();
        });

        let scanner;

        const fileScanner = new Html5Qrcode("scanner");

        $('#btnUpload').click(function () {
            stopScanner();
            $('#scanSection').addClass('hidden');
            $('#manualSection').addClass('hidden');

            $('#qrFileInput').click();
        });

        $('#qrFileInput').on('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            fileScanner.scanFile(file, true)
                .then(qrCodeMessage => {
                    $.post('/checkin', {
                        _token: '{{ csrf_token() }}',
                        qr_token: qrCodeMessage
                    }, handleResponse);
                })
                .catch(err => {
                    Swal.fire(
                        'QR Tidak Terbaca 😵',
                        'Pastikan gambar QR jelas dan tidak blur',
                        'error'
                    );
                    console.error(err);
                })
                .finally(() => {
                    $('#qrFileInput').val('');
                });
        });

        function setManualLoading(isLoading) {
            $('#submitManual').prop('disabled', isLoading)
                .toggleClass('opacity-70 cursor-not-allowed', isLoading);

            $('#manualText').text(isLoading ? 'Menyimpan...' : 'Simpan Check-in');
            $('#manualLoader').toggleClass('hidden', !isLoading);
        }

        function stopScanner() {
            if (scanner) {
                scanner.stop().catch(() => { });
                scanner = null;
            }
        }

        $('#btnScan').click(function () {
            $('#scanSection').removeClass('hidden');
            $('#manualSection').addClass('hidden');
            stopScanner();

            scanner = new Html5Qrcode("scanner");

            scanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                onScanSuccess
            ).catch(err => {
                Swal.fire(
                    'Kamera Tidak Bisa Dibuka',
                    'Pastikan kamera tersedia dan izin sudah diberikan',
                    'error'
                );

                $('#scanSection').addClass('hidden');
                console.error(err);
            });
        });


        $('#btnManual').click(function () {
            stopScanner();
            $('#scanSection').addClass('hidden');
            $('#manualSection').removeClass('hidden');
        });

        function onScanSuccess(qrCodeMessage) {
            stopScanner();

            $.post('/checkin', {
                _token: '{{ csrf_token() }}',
                qr_token: qrCodeMessage
            }, handleResponse);
        }
        $('#studentSelect').on('change', function () {
            $('#nis').val($(this).find(':selected').data('nis') || '');
        });

        $('#submitManual').click(function () {
            const studentId = $('#studentSelect').val();

            if (!studentId) {
                Swal.fire('Oops', 'Pilih siswa terlebih dahulu', 'warning');
                return;
            }

            setManualLoading(true);

            $.post('/checkin-manual', {
                _token: '{{ csrf_token() }}',
                student_id: studentId
            })
                .done(handleResponse)
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    console.log('Response Text:', jqXHR.responseText);
                    Swal.fire('Error', 'Gagal menyimpan data: ' + errorThrown, 'error');
                })
                .always(function () {
                    setManualLoading(false);
                });
        });



        function handleResponse(res) {
            if (!res.success) {
                Swal.fire('Gagal', res.message, 'error');
                return;
            }

            $('#datatable').DataTable().row.add([
                $('#datatable tbody tr').length + 1,
                res.data.nama,
                res.data.kelas,
                res.data.waktu,
                `<span class="px-2 py-1 rounded text-xs
                                                                                                            ${res.data.status === 'TEPAT WAKTU'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700'}">
                                                                                                            ${res.data.status}
                                                                                                        </span>`
            ]).draw(false);

            $('#studentSelect').val(null).trigger('change');

            Swal.fire('Berhasil', 'Check-in berhasil dicatat', 'success');
        }


    </script>
@endpush