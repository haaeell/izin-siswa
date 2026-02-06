@extends('layouts.app')

@section('title', 'Permohonan Izin')

@section('content')
    <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8 bg-white rounded-xl shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-lg sm:text-xl md:text-2xl font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-blue-600"></i>
                    Permohonan Izin
                </h1>

                <nav class="text-xs sm:text-sm mt-1 flex flex-wrap gap-1">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a href="/home" class="hover:text-blue-600 transition">Dashboard</a></li>
                        <li class="text-slate-400">/</li>
                        <li class="text-slate-700 font-medium">Permohonan Izin</li>
                    </ol>
                </nav>
            </div>

            @if (auth()->user()->role === 'wali_kelas')
                <div class="w-full md:w-auto">
                    <button onclick="openCreateModal()" @disabled($activePermissionCount >= 3) class="w-full px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ $activePermissionCount >= 3
                ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                        <i class="fa-solid fa-plus"></i>
                        Ajukan Izin
                    </button>
                </div>
            @endif

        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow border overflow-x-auto">
            <div class="p-4 sm:p-6">

                @if (auth()->user()->role === 'wali_kelas')
                    @php
                        $limit = 3;
                        $isFull = $activePermissionCount >= $limit;
                    @endphp

                    <div
                        class="mb-4 rounded-xl border
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                {{ $isFull ? 'border-red-300 bg-red-50 text-red-800' : 'border-blue-300 bg-blue-50 text-blue-800' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                px-4 py-3 flex flex-col sm:flex-row gap-3 items-start">

                        <div class="flex-shrink-0">
                            <div
                                class="w-9 h-9 rounded-full flex items-center justify-center
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ $isFull ? 'bg-red-100' : 'bg-blue-100' }}">
                                <i
                                    class="fa-solid
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $isFull ? 'fa-circle-exclamation text-red-600' : 'fa-circle-info text-blue-600' }}">
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

                            @if ($isFull)
                                <p class="text-xs mt-2 font-medium text-red-700">
                                    <i class="fa-solid fa-circle-exclamation"></i> Batas izin tercapai. Tidak dapat
                                    mengajukan izin
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

                <form method="GET"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-4 items-end">
                    <input type="hidden" name="filter" value="1">

                    @php
                        $start = request('start_date') ?? now()->startOfMonth()->format('Y-m-d');
                        $end = request('end_date') ?? now()->endOfMonth()->format('Y-m-d');
                    @endphp

                    <div class="w-full">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Filter Tanggal</label>
                        <input type="text" id="dateRange"
                            class="w-full pl-3 pr-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            value="{{ $start . ' to ' . $end }}" placeholder="Pilih rentang tanggal">
                    </div>

                    <input type="hidden" name="start_date" id="startDate" value="{{ $start }}">
                    <input type="hidden" name="end_date" id="endDate" value="{{ $end }}">


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
                        {{-- Filter --}}
                        <button type="submit"
                            class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition">
                            <i class="fa-solid fa-filter"></i> Terapkan
                        </button>

                        <button type="submit" formaction="{{ route('permissions.pdf') }}" formmethod="GET"
                            class="flex-1 sm:flex-none px-4 py-2 bg-red-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-red-700 transition">
                            <i class="fa-solid fa-file-pdf"></i> Cetak PDF
                        </button>

                        @if (request()->hasAny(['status', 'start_date']))
                            <a href="/permissions"
                                class="flex-1 sm:flex-none px-4 py-2 border rounded-lg text-center hover:bg-slate-50 transition">
                                <i class="fa-solid fa-xmark mr-1"></i> Reset
                            </a>
                        @endif
                    </div>

                </form>

                <div class="overflow-x-auto w-full">
                    <table id="datatable" class="w-full text-sm min-w-[700px] md:min-w-full table-auto">

                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">NIS</th>
                                <th class="px-4 py-2 text-left">Siswa</th>
                                <th class="px-4 py-2 text-left">Kelas</th>
                                <th class="px-4 py-2 text-left">Jenis</th>
                                <th class="px-4 py-2 text-left">Waktu</th>
                                <th class="px-4 py-2 text-left">File</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                @if (auth()->user()->role === 'wali_kelas')
                                    <th class="px-4 py-2 text-center">Surat</th>
                                @endif
                                @if (auth()->user()->role === 'perizinan')
                                    <th class="px-4 py-2 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $i => $p)
                                <tr class="border-b">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $loop->iteration}}</td>
                                    <td class="px-4 py-2">{{ $p->student->nis}}</td>
                                    <td class="px-4 py-2 font-medium break-words">{{ $p->student->name }}</td>
                                    <td class="px-4 py-2 capitalize whitespace-nowrap">{{ $p->student->class->name }}</td>
                                    <td class="px-4 py-2 capitalize whitespace-nowrap">{{ $p->type }}</td>
                                    <td class="px-4 py-2 text-xs text-slate-700 break-words">
                                        <i class="fa-regular fa-clock text-slate-400 mr-1"></i>
                                        {{ \Carbon\Carbon::parse($p->start_at)->format('d M Y H:i') }}
                                        <span class="mx-1 text-slate-400">→</span>
                                        {{ \Carbon\Carbon::parse($p->end_at)->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($p->surat_walas)
                                                <a href="{{ asset('storage/' . $p->surat_walas) }}" target="_blank"
                                                    class="text-blue-600 hover:text-blue-800 tooltip-icon"
                                                    data-title="Surat Wali Kelas" data-desc="Klik untuk melihat surat wali kelas">
                                                    <i class="fa-solid fa-user-tie"></i>
                                                </a>
                                            @endif

                                            @if ($p->surat_ortu)
                                                <a href="{{ asset('storage/' . $p->surat_ortu) }}" target="_blank"
                                                    class="text-green-600 hover:text-green-800 tooltip-icon"
                                                    data-title="Surat Orang Tua" data-desc="Klik untuk melihat surat orang tua">
                                                    <i class="fa-solid fa-people-roof"></i>
                                                </a>
                                            @endif

                                            @if ($p->surat_dokter)
                                                <a href="{{ asset('storage/' . $p->surat_dokter) }}" target="_blank"
                                                    class="text-red-600 hover:text-red-800 tooltip-icon" data-title="Surat Dokter"
                                                    data-desc="Klik untuk melihat surat dokter">
                                                    <i class="fa-solid fa-user-doctor"></i>
                                                </a>
                                            @endif

                                            @if (!$p->surat_walas && !$p->surat_ortu && !$p->surat_dokter)
                                                <span class="text-slate-400 text-xs">—</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-4 py-2 whitespace-nowrap">
                                        @if ($p->status === 'pending')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">
                                                <i class="fa-regular fa-clock mr-1"></i> Pending
                                            </span>
                                        @elseif($p->status === 'approved')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">
                                                <i class="fa-solid fa-check mr-1"></i> Disetujui
                                            </span>
                                        @else
                                            <div class="flex flex-col">
                                                <span
                                                    class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs inline-flex items-center">
                                                    <i class="fa-solid fa-xmark mr-1"></i> Ditolak
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    @if (auth()->user()->role === 'wali_kelas')
                                        <td class="px-4 py-2 text-center">
                                            @if (in_array($p->status, ['approved', 'rejected']))
                                                <a href="{{ route('permissions.surat', $p->id) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 px-2 py-1
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      text-xs rounded bg-indigo-100 text-indigo-700
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      hover:bg-indigo-200 transition">
                                                    <i class="fa-solid fa-file-lines"></i>
                                                    Lihat Surat
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400">Belum tersedia</span>
                                            @endif
                                        </td>
                                    @endif

                                    @if (auth()->user()->role === 'perizinan')
                                        <td class="px-4 py-2 text-center flex flex-wrap justify-center gap-2">

                                            @if ($p->status === 'pending')
                                                <button onclick="approvePermission({{ $p->id }})"
                                                    class="px-2 py-1 bg-green-500 text-white rounded text-xs">
                                                    <i class="fa-solid fa-check"></i> Setujui
                                                </button>

                                                <button onclick="openRejectModal({{ $p->id }})"
                                                    class="px-2 py-1 bg-red-500 text-white rounded text-xs">
                                                    <i class="fa-solid fa-xmark"></i> Tolak
                                                </button>
                                            @elseif ($p->status === 'rejected')
                                                <button onclick="showRejectReason(`{{ addslashes($p->reject_reason) }}`)"
                                                    class="px-2 py-1 bg-slate-600 text-white rounded text-xs hover:bg-slate-700">
                                                    <i class="fa-solid fa-eye"></i> Lihat Alasan
                                                </button>
                                            @elseif ($p->status === 'approved' && $p->qr_token)
                                                <button
                                                    onclick="showBarcode(
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    '{{ $p->qr_token }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    '{{ $p->student->name }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    '{{ $p->student->nis }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    '{{ $p->student->class->name }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    '{{ $p->student->dormitory->name ?? '-' }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    '{{ ucfirst($p->type) }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    '{{ \Carbon\Carbon::parse($p->start_at)->format('d M Y H:i') }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    '{{ \Carbon\Carbon::parse($p->end_at)->format('d M Y H:i') }}'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                )"
                                                    class="px-2 py-1 bg-indigo-600 text-white rounded text-xs">
                                                    <i class="fa-solid fa-barcode"></i> Lihat Barcode
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

    {{-- ================= MODAL LIHAT ALASAN ================= --}}
    <div id="viewRejectModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50 p-4">

        <div class="bg-white w-full max-w-md rounded-xl p-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-red-600 flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Alasan Penolakan
                </h2>

                <button onclick="closeViewRejectModal()" class="text-slate-400 hover:text-red-500">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
                <p id="rejectReasonText" class="whitespace-pre-line"></p>
            </div>

            <div class="mt-4 text-right">
                <button onclick="closeViewRejectModal()" class="px-4 py-2 border rounded-lg hover:bg-slate-100">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ================= MODAL CREATE ================= --}}
    <div id="createModal" class="fixed inset-0 hidden bg-black/40 z-50 flex items-center justify-center p-2 sm:p-4">

        <!-- MODAL CARD -->
        <div
            class="bg-white w-full max-w-4xl h-full sm:h-auto sm:max-h-[90vh] rounded-xl shadow-lg flex flex-col overflow-hidden">
            <!-- HEADER (STICKY) -->
            <div
                class="flex items-center justify-between px-4 sm:px-6 py-4
                                                                                                                                                                                                                                                                                                                                                                                                                                                   border-b bg-white sticky top-0 z-20">

                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-file-circle-plus text-blue-600"></i>
                    Ajukan Izin
                </h2>

                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>


            <form action="/permissions" method="POST" enctype="multipart/form-data" id="createForm"
                class="flex-1 overflow-y-auto p-4 sm:p-6">
                @csrf

                <div id="violationAlert"
                    class="hidden mb-6 rounded-xl border border-rose-200 bg-rose-50 shadow-sm relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-rose-100 rounded-full opacity-50 blur-2xl">
                    </div>

                    <div class="relative flex flex-col sm:flex-row gap-4 p-5">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 rounded-xl bg-rose-500 shadow-md shadow-rose-200 flex items-center justify-center">
                                <i class="fa-solid fa-triangle-exclamation text-white text-xl"></i>
                            </div>
                        </div>

                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <h3 class="font-bold text-rose-900 tracking-tight text-base">
                                    Pengajuan Izin Terkunci
                                </h3>
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-rose-200 text-rose-700">
                                    Status: Pelanggaran
                                </span>
                            </div>

                            <div id="violationDetail" class="space-y-3"></div>
                        </div>
                    </div>
                </div>

                <!-- SISWA -->
                <div class="mb-4">
                    <label class="text-sm font-medium mb-1 block">Siswa</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="student_id" required class="select2 w-full border rounded-lg py-2 pl-10">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach ($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- JENIS -->
                <div class="mb-4 hidden" id="typeField">
                    <label class="text-sm font-medium mb-1">Jenis Izin</label>
                    <div class="relative">
                        <i class="fa-solid fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="type" class="select2 w-full border rounded-lg py-2 pl-10">
                            <option value="sakit">Sakit</option>
                            <option value="pulang">Pulang</option>
                            <option value="pesiar">Pesiar</option>
                        </select>
                    </div>
                </div>

                <!-- WAKTU -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 hidden" id="timeFields">
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

                <!-- ALASAN -->
                <div class="mb-5 hidden" id="reasonField">
                    <label class="text-sm font-medium mb-1">Alasan</label>
                    <textarea name="reason" rows="4" class="w-full border rounded-lg py-2 px-3"></textarea>
                </div>

                <!-- SURAT LAMPIRAN -->
                <div class="mb-6 space-y-4 hidden" id="attachmentFields">
                    <!-- Surat Orang Tua -->
                    <div>
                        <label class="text-sm font-medium mb-2 block">
                            Surat Orang Tua
                        </label>

                        <label
                            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed rounded-xl
                                                                                                                                                                                                                                                                                   cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">

                            <i class="fa-solid fa-people-roof text-blue-600 text-xl"></i>

                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-700" id="ortuText">
                                    Klik untuk upload surat orang tua
                                </p>
                                <p class="text-xs text-slate-400">PDF / JPG / PNG • Max 2MB</p>
                            </div>

                            <input type="file" name="surat_ortu" class="hidden"
                                onchange="updateFileLabel(this, 'ortuText')">
                        </label>
                    </div>

                    <!-- Surat Dokter -->
                    <div>
                        <label class="text-sm font-medium mb-2 block">
                            Surat Dokter
                        </label>

                        <label
                            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed rounded-xl
                                                                                                                                                                                                                                                                                   cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">

                            <i class="fa-solid fa-user-doctor text-blue-600 text-xl"></i>

                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-700" id="dokterText">
                                    Klik untuk upload surat dokter
                                </p>
                                <p class="text-xs text-slate-400">PDF / JPG / PNG • Max 2MB</p>
                            </div>

                            <input type="file" name="surat_dokter" class="hidden"
                                onchange="updateFileLabel(this, 'dokterText')">
                        </label>
                    </div>

                </div>

                <!-- FOOTER (STICKY) -->
                <div class="border-t p-4 bg-white">
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <button onclick="closeCreateModal()" class="px-4 py-2 border rounded-lg">
                            Batal
                        </button>
                        <button type="submit" id="submitCreateBtn"
                            class="w-full sm:w-auto px-5 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition">
                            <span id="submitCreateText">Kirim</span>

                            <svg id="submitCreateLoader" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24"
                                fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.3" />
                                <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                            </svg>
                        </button>
                    </div>
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
                <i class="fa-solid fa-barcode text-indigo-600"></i>
                Barcode Kepulangan
            </h2>

            <div class="flex justify-center mb-4 relative">
                <!-- Loader -->
                <div id="qrLoader" class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-lg">
                    <svg class="w-8 h-8 animate-spin text-indigo-600" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.3" />
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                    </svg>
                </div>

                <img id="qrImage" class="w-full max-w-sm mx-auto hidden">
            </div>

            <p class="text-xs text-center text-slate-500 break-all mb-4" id="qrText"></p>

            <button onclick="printQr()" id="printQrBtn" disabled
                class="w-full bg-slate-300 text-slate-500 rounded-lg py-2 flex items-center justify-center gap-2 cursor-not-allowed transition mb-2">
                <i class="fa-solid fa-print"></i>
                Cetak QR
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

    <div id="global-tooltip"
        class="fixed z-[99999] hidden
                                                                                                                                                                                                                                                                                                                                                                                            bg-slate-900 text-white text-xs rounded-lg
                                                                                                                                                                                                                                                                                                                                                                                            px-3 py-2 shadow-xl whitespace-nowrap
                                                                                                                                                                                                                                                                                                                                                                                            pointer-events-none transition-all duration-150">
        <div id="tooltip-title" class="font-medium"></div>
        <div id="tooltip-desc" class="text-slate-300 text-[11px] mt-0.5"></div>

        <div
            class="absolute -bottom-1 left-1/2 -translate-x-1/2
                                                                                                                                                                                                                                                                                                                                                                                                w-2 h-2 bg-slate-900 rotate-45">
        </div>
    </div>


    @push('scripts')
        <script>
            function updateFileLabel(input, targetId) {
                if (!input.files || !input.files[0]) return;
                document.getElementById(targetId).innerText = input.files[0].name;
            }

            document.addEventListener('mouseenter', function (e) {
                const el = e.target.closest('.tooltip-icon');
                if (!el) return;

                const tooltip = document.getElementById('global-tooltip');
                const title = document.getElementById('tooltip-title');
                const desc = document.getElementById('tooltip-desc');

                title.textContent = el.dataset.title || '';
                desc.textContent = el.dataset.desc || '';

                tooltip.classList.remove('hidden');

                positionTooltip(el, tooltip);
            }, true);

            document.addEventListener('mouseleave', function (e) {
                if (!e.target.closest('.tooltip-icon')) return;
                document.getElementById('global-tooltip')?.classList.add('hidden');
            }, true);

            function positionTooltip(el, tooltip) {
                const rect = el.getBoundingClientRect();

                const top = rect.top + window.scrollY - tooltip.offsetHeight - 10;
                const left = rect.left + window.scrollX + rect.width / 2;

                tooltip.style.top = `${top}px`;
                tooltip.style.left = `${left}px`;
                tooltip.style.transform = 'translateX(-50%)';
            }

            function previewPermissionFile(input) {
                if (!input.files || !input.files[0]) return;

                const file = input.files[0];
                document.getElementById('fileName').innerText = file.name;
                document.getElementById('filePreview').classList.remove('hidden');
            }

            function removePermissionFile() {
                const input = document.getElementById('permissionFile');
                input.value = '';
                document.getElementById('filePreview').classList.add('hidden');
            }

            function showRejectReason(reason) {
                $('#rejectReasonText').text(reason || '-');
                $('#viewRejectModal').removeClass('hidden');
            }

            function closeViewRejectModal() {
                $('#viewRejectModal').addClass('hidden');
            }


            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [
                    "{{ $start }}",
                    "{{ $end }}"
                ],
                onClose: function (dates) {
                    if (dates.length === 2) {
                        document.getElementById('startDate').value =
                            dates[0].toISOString().slice(0, 10);
                        document.getElementById('endDate').value =
                            dates[1].toISOString().slice(0, 10);
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
                $('#violationDetail').html('');

                if (!studentId) {
                    $('#typeField, #timeFields, #reasonField, #attachmentFields').addClass('hidden');
                    $('#submitCreateBtn').prop('disabled', true)
                        .addClass('opacity-50 cursor-not-allowed');
                    return;
                }

                $('#typeField, #timeFields, #reasonField, #attachmentFields').removeClass('hidden');
                $('#submitCreateBtn').prop('disabled', false)
                    .removeClass('opacity-50 cursor-not-allowed');
            });

            const $typeField = $('select[name="type"]');
            const $attachmentFields = $('#attachmentFields');
            const $suratDokter = $attachmentFields.find('[name="surat_dokter"]').closest('div');

            function toggleDokterField() {
                const type = $typeField.val();

                if (type === 'sakit') {
                    $suratDokter.show();
                } else {
                    $suratDokter.hide();
                    $suratDokter.find('input').val('');
                    $('#dokterText').text('Klik untuk upload surat dokter');
                }
            }

            toggleDokterField();

            $typeField.on('change', function () {
                toggleDokterField();
            });

            $('select[name="type"]').on('change', function () {
                const type = $(this).val();
                const studentId = $('select[name="student_id"]').val();
                const $alert = $('#violationAlert');
                const $submitBtn = $('#submitCreateBtn');

                // Reset
                $alert.addClass('hidden');
                $submitBtn.prop('disabled', false).removeClass('opacity-50 grayscale cursor-not-allowed');
                $('#violationDetail').html('');

                if (!studentId || type === 'sakit') return;

                $.get(`/permissions/check-violation/${studentId}`, function (res) {
                    if (!res.has_violation) return;

                    let html = '<div class="grid gap-3">';
                    for (const [vType, data] of Object.entries(res.details)) {
                        const isPengasuhan = vType === 'pengasuhan';
                        const icon = isPengasuhan ? 'fa-user-shield' : 'fa-calendar-check';
                        const accentColor = isPengasuhan ? 'text-rose-600' : 'text-amber-600';

                        html += `
                                                                                                                                                                                                                                                                <div class="bg-white/80 backdrop-blur-sm p-4 rounded-lg border border-rose-100 shadow-sm transition-hover hover:shadow-md">
                                                                                                                                                                                                                                                                    <div class="flex gap-3">
                                                                                                                                                                                                                                                                        <i class="fa-solid ${icon} ${accentColor} mt-1"></i>
                                                                                                                                                                                                                                                                        <div class="flex-1 text-sm text-slate-700">
                                                                                                                                                                                                                                                                            ${isPengasuhan ? `
                                                                                                                                                                                                                                                                                <div class="font-bold text-slate-900 mb-1">Pelanggaran Pengasuhan: ${data.type}</div>
                                                                                                                                                                                                                                                                                <div class="italic text-slate-600 mb-2">"${data.description}"</div>
                                                                                                                                                                                                                                                                                <div class="flex flex-wrap gap-x-4 gap-y-1 text-[11px] font-medium text-slate-500 uppercase">
                                                                                                                                                                                                                                                                                    <span><i class="fa-regular fa-clock mr-1"></i>Hingga: ${data.until}</span>
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                            ` : `
                                                                                                                                                                                                                                                                                <div class="font-bold text-slate-900 mb-1">Pelanggaran ${data.handling_type}</div>
                                                                                                                                                                                                                                                                                <div class="mb-2">Kehadiran: <span class="font-bold text-rose-600">${data.attendance_percentage}%</span></div>
                                                                                                                                                                                                                                                                                <div class="text-[11px] font-medium text-slate-500 uppercase">
                                                                                                                                                                                                                                                                                    <i class="fa-regular fa-calendar-xmark mr-1"></i>Berlaku Sampai: ${data.until}
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                            `}
                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                </div>`;
                    }
                    html += '</div>';

                    $('#violationDetail').html(html);
                    $alert.removeClass('hidden').hide().slideDown(300);
                    $submitBtn.prop('disabled', true).addClass('opacity-50 grayscale cursor-not-allowed');
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

            let barcodeData = {};

            let barcodeReady = false;

            function showBarcode(
                token,
                nama,
                nis,
                kelas,
                asrama,
                izin,
                startAt,
                endAt
            ) {
                barcodeReady = false;

                barcodeData = { nama, nis, kelas, asrama, izin, startAt, endAt };

                $('#qrModal').removeClass('hidden');

                // Reset UI
                $('#qrImage').addClass('hidden');
                $('#qrLoader').removeClass('hidden');
                $('#qrText').text('Memuat barcode...');
                $('#printQrBtn')
                    .prop('disabled', true)
                    .removeClass('bg-blue-600 hover:bg-blue-700 text-white')
                    .addClass('bg-slate-300 text-slate-500 cursor-not-allowed');

                const url = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(token)}&scale=2&height=12&includetext=false`;

                const img = new Image();
                img.onload = function () {
                    $('#qrImage').attr('src', url).removeClass('hidden');
                    $('#qrLoader').addClass('hidden');
                    $('#qrText').text(token);

                    barcodeReady = true;

                    $('#printQrBtn')
                        .prop('disabled', false)
                        .removeClass('bg-slate-300 text-slate-500 cursor-not-allowed')
                        .addClass('bg-blue-600 hover:bg-blue-700 text-white');
                };

                img.onerror = function () {
                    $('#qrLoader').addClass('hidden');
                    $('#qrText').text('Gagal memuat barcode');
                };

                img.src = url;
            }




            function closeQrModal() {
                $('#qrModal').addClass('hidden');
                $('#waNumber').val('');
            }

            function printQr() {
                if (!barcodeReady) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Barcode belum siap',
                        text: 'Tunggu sampai barcode selesai dimuat sebelum mencetak.',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }
                const barcodeSrc = document.getElementById('qrImage').src;
                const d = barcodeData;

                const win = window.open('', '_blank', 'width=360,height=520');

                win.document.write(`
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <!DOCTYPE html>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <html>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <head>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <title>Tiket Izin</title>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <style>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    @page {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        size: 80mm auto;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        margin: 4mm;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                    body {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        margin: 0;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        font-family: Arial, sans-serif;
                                                                                                                                                                                                                                                                                                                                                                                                                                                         padding: 6mm 0; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                    .ticket {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        border: 2px solid #000;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        padding: 6px 8px;

                                                                                                                                                                                                                                                                                                                                                                                                                                                        max-width: 70mm;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        margin: 0 auto; 

                                                                                                                                                                                                                                                                                                                                                                                                                                                        font-size: 9px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                    .title {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        text-align: center;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        font-weight: bold;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        font-size: 11px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        letter-spacing: 1px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        border-bottom: 1px solid #000;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        padding-bottom: 3px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        margin-bottom: 4px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                    /* BIODATA */
                                                                                                                                                                                                                                                                                                                                                                                                                                                    .info {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        display: grid;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        grid-template-columns: 55px auto;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        row-gap: 2px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        margin-bottom: 6px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                    .label {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        font-weight: bold;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                    /* BARCODE */
                                                                                                                                                                                                                                                                                                                                                                                                                                                    .barcode {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        text-align: center;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        border-top: 1px dashed #000;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        padding-top: 6px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                    .barcode img {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        width: 180px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                    .barcode small {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        display: block;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        font-size: 7px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        margin-top: 2px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                    /* WAKTU */
                                                                                                                                                                                                                                                                                                                                                                                                                                                    .time {
                                                                                                                                                                                                                                                                                                                                                                                                                                                        display: flex;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        justify-content: space-between;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        font-size: 8px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        border-top: 1px dashed #000;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        margin-top: 4px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                        padding-top: 4px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                    </style>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    </head>

                                                                                                                                                                                                                                                                                                                                                                                                                                                    <body>
                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="ticket">
                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="title">IZIN KEPULANGAN</div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="info">
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="label">Nama</div><div>: ${d.nama}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="label">NIS</div><div>: ${d.nis}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="label">Kelas</div><div>: ${d.kelas}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="label">Asrama</div><div>: ${d.asrama}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="label">Jenis</div><div>: ${d.izin}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="barcode">
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <img src="${barcodeSrc}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <small>Scan saat keluar & masuk</small>
                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="time">
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                <b>Mulai</b><br>${d.startAt}
                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="text-align:right">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                <b>Sampai</b><br>${d.endAt}
                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
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