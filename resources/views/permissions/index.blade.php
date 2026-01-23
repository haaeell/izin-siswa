@extends('layouts.app')

@section('title', 'Permohonan Izin')

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl">

        {{-- HEADER --}}
        <div class="flex justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-blue-600"></i>
                    Permohonan Izin
                </h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-blue-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Permohonan Izin</li>
                    </ol>
                </nav>
            </div>

            @if(auth()->user()->role === 'wali_kelas')
                <div>
                    <button onclick="openCreateModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        Ajukan Izin
                    </button>
                </div>
            @endif
        </div>


        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <form method="GET" class="flex flex-wrap gap-3 mb-4 items-center">
                <input type="hidden" name="filter" value="1">

                {{-- Date Range --}}
                <input type="text" id="dateRange" class="pl-3 pr-3 py-2 border rounded-lg text-sm w-56"
                    placeholder="Filter tanggal" value="{{ request('start_date') && request('end_date')
        ? request('start_date') . ' to ' . request('end_date')
        : '' }}">

                <input type="hidden" name="start_date" id="startDate">
                <input type="hidden" name="end_date" id="endDate">

                {{-- Status --}}
                <select name="status" class=" py-2 px-3 border rounded-lg text-sm ">
                    <option value="">Semua Status</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
                </select>

                <button type="submit" id="filterBtn"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center gap-2 hover:bg-blue-700">
                    <span id="filterText">
                        <i class="fa-solid fa-filter"></i> Terapkan
                    </span>

                    <svg id="filterLoader" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.3" />
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                    </svg>
                </button>

                @if(request()->hasAny(['status', 'start_date']))
                    <a href="/permissions" class="px-4 py-2 border rounded-lg">
                        <i class="fa-solid fa-xmark mr-1"></i> Reset
                    </a>
                @endif
            </form>

            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Jenis</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        @if(auth()->user()->role === 'perizinan')
                            <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-medium">{{ $p->student->name }}
                            </td>
                            <td class="capitalize">{{ $p->student->class->name }}</td>
                            <td class="capitalize">{{ $p->type }}</td>
                            <td class="text-xs text-slate-700 whitespace-nowrap">
                                <i class="fa-regular fa-clock text-slate-400 mr-1"></i>
                                {{ \Carbon\Carbon::parse($p->start_at)->format('d M Y H:i') }}
                                <span class="mx-1 text-slate-400">→</span>
                                {{ \Carbon\Carbon::parse($p->end_at)->format('d M Y H:i') }}
                            </td>

                            <td>
                                @if($p->status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">
                                        <i class="fa-regular fa-clock mr-1"></i> Pending
                                    </span>
                                @elseif($p->status === 'approved')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">
                                        <i class="fa-solid fa-check mr-1"></i> Disetujui
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">
                                        <i class="fa-solid fa-xmark mr-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            @if(auth()->user()->role === 'perizinan')
                                <td class="text-center space-x-2">
                                    @if($p->status === 'pending')
                                        <button onclick="approvePermission({{ $p->id }})"
                                            class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                            <i class="fa-solid fa-check"></i> Setujui
                                        </button>

                                        <button onclick="openRejectModal({{ $p->id }})"
                                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    @elseif($p->status === 'approved' && $p->qr_token)
                                        <button onclick="showQr('{{ $p->qr_token }}')"
                                            class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                            <i class="fa-solid fa-qrcode"></i> Lihat QR
                                        </button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= MODAL CREATE ================= --}}
    <div id="createModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg relative">

            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-file-circle-plus text-blue-600"></i>
                    Ajukan Izin
                </h2>

                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="/permissions" method="POST" class="p-6" id="createForm">
                @csrf

                <div class="mb-4">
                    <label class="text-sm font-medium">Siswa</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="student_id" required class="select2 w-full border rounded-lg py-2 pl-10">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium">Jenis Izin</label>
                    <div class="relative">
                        <i class="fa-solid fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="type" class="select2 w-full border rounded-lg py-2 pl-10">
                            <option value="pulang">Pulang</option>
                            <option value="sakit">Sakit</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-sm font-medium mb-1 block">
                            Dari
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="datetime-local" name="start_at"
                                class="border rounded-lg py-2 pl-10 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium mb-1 block">
                            Sampai
                        </label>
                        <div class="relative">
                            <i class="fa-regular fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="datetime-local" name="end_at"
                                class="border rounded-lg py-2 pl-10 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="text-sm font-medium">Alasan</label>
                    <div class="relative">
                        <i class="fa-solid fa-pen absolute left-3 top-3 text-slate-400"></i>
                        <textarea name="reason" rows="4" class="w-full border rounded-lg pl-10 pr-3 py-2"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t pt-4">
                    <button type="button" onclick="closeCreateModal()"
                        class="px-4 py-2 border rounded-lg hover:bg-slate-100">
                        Batal
                    </button>

                    <button type="submit" id="submitCreateBtn"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg flex items-center gap-2">
                        <span id="submitCreateText">Kirim</span>

                        <svg id="submitCreateLoader" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.3" />
                            <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL QR ================= --}}
    <div id="qrModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-sm rounded-xl p-6 relative">

            <button onclick="closeQrModal()" class="absolute top-3 right-3 text-slate-400 hover:text-red-500">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-qrcode text-indigo-600"></i>
                QR Kepulangan
            </h2>

            <div class="flex justify-center mb-4">
                <img id="qrImage" class="w-56 h-56">
            </div>

            <p class="text-xs text-center text-slate-500 break-all mb-4" id="qrText"></p>

            <button onclick="printQr()"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 flex items-center justify-center gap-2">
                <i class="fa-solid fa-print"></i>
                Cetak QR
            </button>

        </div>
    </div>

    {{-- ================= MODAL REJECT ================= --}}
    <div id="rejectModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl p-6">
            <h2 class="text-lg font-semibold mb-4 text-red-600 flex items-center gap-2">
                <i class="fa-solid fa-circle-xmark"></i>
                Tolak Permohonan
            </h2>

            <form method="POST" id="rejectForm">
                @csrf

                <textarea name="reject_reason" required rows="3" class="w-full border rounded-lg px-3 py-2"
                    placeholder="Alasan penolakan"></textarea>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border rounded-lg">
                        Batal
                    </button>
                    <button class="px-4 py-2 bg-red-600 text-white rounded-lg">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                onClose: function (dates) {
                    if (dates.length === 2) {
                        $('#startDate').val(dates[0].toISOString().slice(0, 10));
                        $('#endDate').val(dates[1].toISOString().slice(0, 10));
                    }
                }
            });

            $('form[method="GET"]').on('submit', function () {
                const $btn = $('#filterBtn');
                const $text = $('#filterText');
                const $loader = $('#filterLoader');

                $btn.prop('disabled', true)
                    .addClass('opacity-70 cursor-not-allowed');

                $text.text('Memuat...');
                $loader.removeClass('hidden');
            });

            $('#createModal form').on('submit', function () {
                $('#submitCreateBtn')
                    .prop('disabled', true)
                    .addClass('opacity-70 cursor-not-allowed');

                $('#submitCreateText').text('Mengirim...');
                $('#submitCreateLoader').removeClass('hidden');
            });

            $(document).ready(function () {
                $('#datatable').DataTable();
                $('.select2').select2({
                    width: '100%'
                });
            });

            function approvePermission(id) {
                Swal.fire({
                    title: 'Setujui izin?',
                    text: 'Permohonan izin siswa akan disetujui',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, setujui',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/permissions/${id}/approve`;

                        form.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                `;

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function openCreateModal() {
                $('#createModal').removeClass('hidden');
            }

            function closeCreateModal() {
                $('#createModal').addClass('hidden');
            }

            function openRejectModal(id) {
                $('#rejectForm').attr('action', `/permissions/${id}/reject`);
                $('#rejectModal').removeClass('hidden');
            }

            function closeRejectModal() {
                $('#rejectModal').addClass('hidden');
            }

            let qrUrl = '';

            function showQr(token) {
                qrUrl = `${window.location.origin}/permissions/checkin/${token}`;

                $('#qrImage').attr(
                    'src',
                    `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrUrl)}`
                );

                $('#qrModal').removeClass('hidden');
            }


            function closeQrModal() {
                $('#qrModal').addClass('hidden');
                $('#waNumber').val('');
            }

            function printQr() {
                const qrSrc = document.getElementById('qrImage').src;
                const logoSrc = 'https://yt3.googleusercontent.com/aqwnd_6PPBpG0PqWP1QMcBjJZX0GwVYQCmJ0_r0pdJPrAgiqjH3TaxhHCF9a-oHRbhk90Bpz=s900-c-k-c0x00ffffff-no-rj';

                const win = window.open('', '_blank', 'width=450,height=600');

                win.document.write(`
                                                        <!DOCTYPE html>
                                                        <html>
                                                        <head>
                                                            <title>Cetak QR Izin</title>
                                                            <style>
                                                                @media print {
                                                                    body {
                                                                        margin: 0;
                                                                        font-family: Arial, Helvetica, sans-serif;
                                                                        display: flex;
                                                                        justify-content: center;
                                                                        align-items: center;
                                                                        height: 100vh;
                                                                        background: #fff;
                                                                    }

                                                                    .card {
                                                                        width: 340px;
                                                                        border: 1px solid #000;
                                                                        padding: 20px;
                                                                        text-align: center;
                                                                    }

                                                                    .logo {
                                                                        width: 80px;
                                                                        margin-bottom: 10px;
                                                                    }

                                                                    h1 {
                                                                        font-size: 14px;
                                                                        margin: 8px 0 12px;
                                                                        letter-spacing: 1px;
                                                                    }

                                                                    img.qr {
                                                                        width: 220px;
                                                                        height: 220px;
                                                                        margin: 10px 0;
                                                                    }

                                                                    p {
                                                                        font-size: 11px;
                                                                        margin: 4px 0;
                                                                        line-height: 1.4;
                                                                    }

                                                                    .footer {
                                                                        margin-top: 10px;
                                                                        font-size: 10px;
                                                                    }
                                                                }
                                                            </style>
                                                        </head>
                                                        <body>
                                                            <div class="card">
                                                                <img src="${logoSrc}" class="logo" alt="Logo Sekolah">

                                                                <h1>QR IZIN KEPULANGAN SISWA</h1>

                                                                <img src="${qrSrc}" class="qr" alt="QR Izin">

                                                                <p>
                                                                    Tunjukkan QR ini kepada petugas<br>
                                                                    saat kembali ke lingkungan sekolah.
                                                                </p>

                                                                <div class="footer">
                                                                    Sistem Perizinan Sekolah
                                                                </div>
                                                            </div>
                                                        </body>
                                                        </html>
                                                    `);

                win.document.close();
                win.focus();

                setTimeout(() => {
                    win.print();
                    win.close();
                }, 300);
            }


        </script>
    @endpush
@endsection