@extends('layouts.app')

@section('title', 'Permohonan Izin')

@section('content')
    <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8 bg-white rounded-xl shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-blue-600"></i>
                    Permohonan Izin
                </h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a href="/home" class="hover:text-blue-600 transition">Dashboard</a></li>
                        <li class="text-slate-400">/</li>
                        <li class="text-slate-700 font-medium">Permohonan Izin</li>
                    </ol>
                </nav>
            </div>

            @if(auth()->user()->role === 'wali_kelas')
                <div class="w-full md:w-auto">
                    <button onclick="openCreateModal()" @disabled($activePermissionCount >= 3) class="w-full px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition
                    {{ $activePermissionCount >= 3
                ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                : 'bg-blue-600 text-white hover:bg-blue-700'
                    }}">
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
        <div class="bg-white rounded-xl shadow border overflow-x-auto">
            <div class="p-4 sm:p-6">

                @if(auth()->user()->role === 'wali_kelas')
                        @php
                            $limit = 3;
                            $isFull = $activePermissionCount >= $limit;
                        @endphp

                        <div class="mb-4 rounded-xl border
                                                                    {{ $isFull ? 'border-red-300 bg-red-50 text-red-800'
                    : 'border-blue-300 bg-blue-50 text-blue-800' }}
                                                                    px-4 py-3 flex gap-3 items-start">

                            <div class="flex-shrink-0">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center
                                                                            {{ $isFull ? 'bg-red-100' : 'bg-blue-100' }}">
                                    <i class="fa-solid
                                                                                {{ $isFull ? 'fa-circle-exclamation text-red-600'
                    : 'fa-circle-info text-blue-600' }}">
                                    </i>
                                </div>
                            </div>

                            <div class="flex-1">
                                <h3 class="font-semibold text-sm mb-1">
                                    Status Izin Siswa Kelas
                                </h3>

                                <p class="text-sm leading-relaxed">
                                    Saat ini terdapat
                                    <span class="font-bold">{{ $activePermissionCount }}</span>
                                    dari
                                    <span class="font-bold">{{ $limit }}</span>
                                    siswa yang sedang izin.
                                </p>

                                @if($isFull)
                                    <p class="text-xs mt-2 font-medium text-red-700">
                                        <i class="fa-solid fa-circle-exclamation"></i> Batas izin tercapai. Tidak dapat mengajukan izin
                                        baru.
                                    </p>
                                @else
                                    <p class="text-xs mt-2 text-blue-700">
                                        <i class="fa-solid fa-circle-info"></i> Masih tersedia
                                        <span class="font-semibold">
                                            {{ $limit - $activePermissionCount }}
                                        </span>
                                        slot izin.
                                    </p>
                                @endif
                            </div>
                        </div>
                @endif

                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:flex gap-3 mb-4 items-end">
                    <input type="hidden" name="filter" value="1">

                    {{-- Date Range --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Filter Tanggal</label>
                        <input type="text" id="dateRange"
                            class="w-full pl-3 pr-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Pilih rentang tanggal" value="{{ request('start_date') && request('end_date')
        ? request('start_date') . ' to ' . request('end_date')
        : '' }}">
                    </div>

                    <input type="hidden" name="start_date" id="startDate">
                    <input type="hidden" name="end_date" id="endDate">

                    {{-- Status --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status"
                            class="w-full py-2 px-3 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
                        </select>
                    </div>

                    <div class="flex gap-2 w-full sm:col-span-2 lg:col-span-1 xl:w-auto">
                        <button type="submit" id="filterBtn"
                            class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition">
                            <span id="filterText">
                                <i class="fa-solid fa-filter"></i> Terapkan
                            </span>

                            <svg id="filterLoader" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.3" />
                                <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                            </svg>
                        </button>

                        @if(request()->hasAny(['status', 'start_date']))
                            <a href="/permissions"
                                class="flex-1 sm:flex-none px-4 py-2 border rounded-lg text-center hover:bg-slate-50 transition">
                                <i class="fa-solid fa-xmark mr-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table id="datatable" class="w-full text-sm min-w-full">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">Siswa</th>
                                <th class="px-4 py-2 text-left">Kelas</th>
                                <th class="px-4 py-2 text-left">Jenis</th>
                                <th class="px-4 py-2 text-left">Waktu</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                @if(auth()->user()->role === 'perizinan')
                                    <th class="px-4 py-2 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $i => $p)
                                <tr class="border-b">
                                    <td class="px-4 py-2">{{ $i + 1 }}</td>
                                    <td class="px-4 py-2 font-medium">{{ $p->student->name }}</td>
                                    <td class="px-4 py-2 capitalize">{{ $p->student->class->name }}</td>
                                    <td class="px-4 py-2 capitalize">{{ $p->type }}</td>
                                    <td class="px-4 py-2 text-xs text-slate-700 whitespace-nowrap">
                                        <i class="fa-regular fa-clock text-slate-400 mr-1"></i>
                                        {{ \Carbon\Carbon::parse($p->start_at)->format('d M Y H:i') }}
                                        <span class="mx-1 text-slate-400">→</span>
                                        {{ \Carbon\Carbon::parse($p->end_at)->format('d M Y H:i') }}
                                    </td>

                                    <td class="px-4 py-2">
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
                                        <td class="px-4 py-2 text-center space-x-2">
                                            @if($p->status === 'pending')
                                                <button onclick="approvePermission({{ $p->id }})"
                                                    class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition text-xs">
                                                    <i class="fa-solid fa-check"></i> Setujui
                                                </button>

                                                <button onclick="openRejectModal({{ $p->id }})"
                                                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs">
                                                    <i class="fa-solid fa-xmark"></i> Tolak
                                                </button>
                                            @elseif($p->status === 'approved' && $p->qr_token)
                                                <button onclick="showQr('{{ $p->qr_token }}')"
                                                    class="px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition text-xs">
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
        </div>
    </div>

    {{-- ================= MODAL CREATE ================= --}}
    <div id="createModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50 p-4">
        <div
            class="bg-white w-full max-w-4xl h-full md:h-auto md:max-h-[90vh] rounded-xl shadow-lg relative overflow-y-auto">
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b sticky top-0 bg-white">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-file-circle-plus text-blue-600"></i>
                    Ajukan Izin
                </h2>

                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="/permissions" method="POST" class="p-4 sm:p-6" id="createForm">
                @csrf

                <div id="violationAlert"
                    class="hidden mb-4 rounded-2xl border border-red-400
                                                                                                       bg-gradient-to-br from-red-500 via-red-400 to-rose-500
                                                                                                       text-white shadow-lg relative overflow-hidden">

                    <!-- SOFT PATTERN -->
                    <div
                        class="absolute inset-0 opacity-10
                                                                                                           bg-[radial-gradient(circle_at_1px_1px,white_1px,transparent_0)]
                                                                                                           [background-size:16px_16px]">
                    </div>

                    <div class="relative flex flex-col sm:flex-row gap-4 p-4 sm:p-5">
                        <!-- ICON -->
                        <div class="flex-shrink-0">
                            <div class="w-11 h-11 rounded-full bg-white/20 flex items-center justify-center backdrop-blur">
                                <i class="fa-solid fa-ban text-white text-lg"></i>
                            </div>
                        </div>

                        <!-- CONTENT -->
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <h3 class="font-semibold text-sm tracking-wide">
                                    Tidak Bisa Mengajukan Izin
                                </h3>

                                <span
                                    class="px-2 py-0.5 text-xs rounded-full
                                                                                                                       bg-white/20 backdrop-blur font-medium">
                                    Pelanggaran Aktif
                                </span>
                            </div>

                            <div id="violationDetail" class="text-sm text-white/90 space-y-1 leading-relaxed">
                                <!-- injected by JS -->
                            </div>

                            <div class="mt-3 flex items-center gap-2 text-xs text-white/80 border-t border-white/20 pt-2">
                                <i class="fa-regular fa-clock"></i>
                                <span id="violationTime"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium block mb-1">Siswa</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="student_id" required
                            class="select2 w-full border rounded-lg py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4" id="typeField">
                    <label class="text-sm font-medium block mb-1">Jenis Izin</label>
                    <div class="relative">
                        <i class="fa-solid fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="type"
                            class="select2 w-full border rounded-lg py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="pulang">Pulang</option>
                            <option value="sakit">Sakit</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4" id="timeFields">
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

                <div class="mb-5" id="reasonField">
                    <label class="text-sm font-medium block mb-1">Alasan</label>
                    <div class="relative">
                        <i class="fa-solid fa-pen absolute left-3 top-3 text-slate-400"></i>
                        <textarea name="reason" rows="4"
                            class="w-full border rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 border-t pt-4">
                    <button type="button" onclick="closeCreateModal()"
                        class="w-full sm:w-auto px-4 py-2 border rounded-lg hover:bg-slate-100 transition">
                        Batal
                    </button>

                    <button type="submit" id="submitCreateBtn"
                        class="w-full sm:w-auto px-5 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition">
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
    <div id="qrModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-sm rounded-xl p-4 sm:p-6 relative">

            <button onclick="closeQrModal()" class="absolute top-3 right-3 text-slate-400 hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-qrcode text-indigo-600"></i>
                QR Kepulangan
            </h2>

            <div class="flex justify-center mb-4">
                <img id="qrImage" class="w-48 h-48 sm:w-56 sm:h-56">
            </div>

            <p class="text-xs text-center text-slate-500 break-all mb-4" id="qrText"></p>

            <button onclick="printQr()"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 flex items-center justify-center gap-2 transition mb-2">
                <i class="fa-solid fa-print"></i>
                Cetak QR
            </button>
            <button onclick="downloadQrJpg()"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg py-2 flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-download"></i>
                Download JPG
            </button>

        </div>
    </div>

    {{-- ================= MODAL REJECT ================= --}}
    <div id="rejectModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-xl p-4 sm:p-6">
            <h2 class="text-lg font-semibold mb-4 text-red-600 flex items-center gap-2">
                <i class="fa-solid fa-circle-xmark"></i>
                Tolak Permohonan
            </h2>

            <form method="POST" id="rejectForm">
                @csrf

                <textarea name="reject_reason" required rows="3"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    placeholder="Alasan penolakan"></textarea>

                <div class="flex flex-col sm:flex-row justify-end gap-2 mt-4">
                    <button type="button" onclick="closeRejectModal()"
                        class="w-full sm:w-auto px-4 py-2 border rounded-lg hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
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

            $('select[name="student_id"]').on('change', function () {
                const studentId = $(this).val();

                $('#violationAlert').addClass('hidden');
                $('#submitCreateBtn').prop('disabled', false)
                    .removeClass('opacity-60 cursor-not-allowed');

                // Show all fields by default
                $('#typeField, #timeFields, #reasonField').removeClass('hidden');

                if (!studentId) return;

                $.get(`/permissions/check-violation/${studentId}`, function (res) {

                    if (!res.has_violation) {
                        $('#violationAlert').addClass('hidden');

                        $('#submitCreateBtn')
                            .prop('disabled', false)
                            .removeClass('opacity-60 cursor-not-allowed');

                        return;
                    }

                    // Hide fields except student name when violation exists
                    $('#typeField, #timeFields, #reasonField').addClass('hidden');

                    $('#violationDetail').html(`
                                                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
                                                                                            <div class="text-white">Jenis</div>
                                                                                            <div class="font-medium capitalize">: ${res.type}</div>

                                                                                            <div class="text-white">Deskripsi</div>
                                                                                            <div>: ${res.description}</div>

                                                                                            <div class="text-white">Berlaku sampai</div>
                                                                                            <div class="font-bold text-white">: ${res.until}</div>
                                                                                        </div>
                                                                                    `);

                    $('#violationTime').text(
                        `Izin dapat diajukan kembali mulai ${res.can_apply_at}`
                    );

                    $('#violationAlert')
                        .removeClass('hidden')
                        .hide()
                        .fadeIn(200);

                    $('#submitCreateBtn')
                        .prop('disabled', true)
                        .addClass('opacity-60 cursor-not-allowed');
                });

            });


            $('#createModal form').on('submit', function () {
                $('#submitCreateBtn')
                    .prop('disabled', true)
                    .addClass('opacity-70 cursor-not-allowed');

                $('#submitCreateText').text('Mengirim...');
                $('#submitCreateLoader').removeClass('hidden');
            });

            $(document).ready(function () {
                $('#datatable').DataTable({
                    responsive: true,
                    scrollX: true,
                    autoWidth: false
                });
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

            let qrToken = '';

            function showQr(token) {
                qrToken = token;

                const url = `https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=${encodeURIComponent(token)}`;

                $('#qrImage').attr('src', url);
                $('#qrText').text(token);
                $('#qrModal').removeClass('hidden');
            }

            function downloadQrJpg() {
                const qrUrl = document.getElementById('qrImage').src;

                const link = document.createElement('a');
                link.href = qrUrl;
                link.download = `qr-izin-${qrToken}.jpg`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
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