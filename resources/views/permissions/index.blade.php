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

                <nav class="text-xs sm:text-sm mt-1 ">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a href="/home" class="hover:text-blue-600 transition">Dashboard</a></li>
                        <li class="text-slate-400">/</li>
                        <li class="text-slate-700 font-medium">Permohonan Izin</li>
                    </ol>
                </nav>
            </div>

            @if (auth()->user()->role === 'wali_kelas')
                <div class="flex gap-2 flex-shrink-0">
                    <button onclick="openCreateModal()" @disabled($activePermissionCount >= $maxActivePermissions) class="whitespace-nowrap px-3 py-2 rounded-lg flex items-center gap-1.5 text-sm transition
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ $activePermissionCount >= $maxActivePermissions
                ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                        <i class="fa-solid fa-plus text-xs"></i> Ajukan Izin
                    </button>
                    <button onclick="openMassalModal()"
                        class="whitespace-nowrap px-3 py-2 rounded-lg flex items-center gap-1.5 text-sm transition bg-emerald-600 text-white hover:bg-emerald-700">
                        <i class="fa-solid fa-users text-xs"></i> Izin Perpulangan
                    </button>
                </div>
            @endif

            @if (auth()->user()->role === 'perizinan')
                <div class="flex gap-2 flex-shrink-0">
                    <button onclick="openQrMassalModal()"
                        class="whitespace-nowrap px-3 py-2 rounded-lg flex items-center gap-1.5 text-sm transition bg-indigo-600 text-white hover:bg-indigo-700">
                        <i class="fa-solid fa-qrcode text-xs"></i> Cetak QR Perpulangan
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
                    @php $isFull = $activePermissionCount >= $maxActivePermissions; @endphp
                    <div
                        class="mb-4 rounded-xl border px-4 py-3 flex flex-col sm:flex-row gap-3 items-start
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $isFull ? 'border-red-300 bg-red-50 text-red-800' : 'border-blue-300 bg-blue-50 text-blue-800' }}">
                        <div class="flex-shrink-0">
                            <div
                                class="w-9 h-9 rounded-full flex items-center justify-center {{ $isFull ? 'bg-red-100' : 'bg-blue-100' }}">
                                <i
                                    class="fa-solid {{ $isFull ? 'fa-circle-exclamation text-red-600' : 'fa-circle-info text-blue-600' }}"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-sm mb-1">Status Izin Siswa Kelas</h3>
                            <p class="text-sm leading-relaxed">
                                Saat ini terdapat <span class="font-bold">{{ $activePermissionCount }}</span>
                                dari <span class="font-bold">{{ $maxActivePermissions }}</span> siswa yang sedang izin.
                            </p>
                            @if ($isFull)
                                <p class="text-xs mt-2 font-medium text-red-700">
                                    <i class="fa-solid fa-circle-exclamation"></i> Batas izin tercapai. Tidak dapat mengajukan izin
                                    baru.
                                </p>
                            @else
                                <p class="text-xs mt-2 text-blue-700">
                                    <i class="fa-solid fa-circle-info"></i> Masih tersedia
                                    <span class="font-semibold">{{ $maxActivePermissions - $activePermissionCount }}</span> slot
                                    izin.
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-4 items-end">

                    <div class="w-full">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Filter Tanggal</label>
                        <input type="text" id="dateRange"
                            class="w-full pl-3 pr-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Pilih rentang tanggal">
                        <input type="hidden" id="startDate">
                        <input type="hidden" id="endDate">
                    </div>

                    <div class="w-full">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select id="filterStatus"
                            class="w-full py-2 px-3 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>

                    <div class="w-full">
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Status Keberadaan
                        </label>
                        <select id="filterCheckinStatus"
                            class="w-full py-2 px-3 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua</option>
                            <option value="belum_checkout">Belum Checkout</option>
                            <option value="dirumah">Dirumah</option>
                            <option value="kembali">Sudah Kembali</option>
                        </select>
                    </div>

                    <div class="w-full">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>

                        <select id="filterKelas" name="kelas"
                            class="w-full py-2 px-3 border rounded-lg text-sm 
                                                                                                                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                                                                                                                       {{ $isWalikelas ? 'bg-slate-100 cursor-not-allowed' : '' }}"
                            {{ $isWalikelas ? 'disabled' : '' }}>

                            @if(!$isWalikelas)
                                <option value="">Semua Kelas</option>
                            @endif

                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @if($isWalikelas) selected
                                @elseif(request('kelas') == $class->id) selected @endif>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>

                        @if($isWalikelas)
                            <input type="hidden" name="kelas" value="{{ $classes->first()->id }}">
                        @endif
                    </div>

                    <div class="flex gap-2 w-full sm:col-span-2 lg:col-span-1 xl:w-auto items-end">
                        <button id="btnTerapkan" type="button"
                            class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition">
                            <i class="fa-solid fa-filter"></i> Terapkan
                        </button>

                        <form method="GET" action="{{ route('permissions.pdf') }}" id="pdfForm" class="inline">
                            <input type="hidden" name="filter" value="1">
                            <input type="hidden" name="start_date" id="pdfStartDate">
                            <input type="hidden" name="end_date" id="pdfEndDate">
                            <input type="hidden" name="status" id="pdfStatus">
                            <input type="hidden" name="checkin_status" id="pdfCheckinStatus">
                            <input type="hidden" name="kelas" id="pdfKelas">
                            <div>
                                <button type="submit"
                                    class="flex-1 sm:flex-none px-4 py-2 bg-red-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-red-700 transition">
                                    <i class="fa-solid fa-file-pdf"></i> PDF
                                </button>
                            </div>
                        </form>

                        <button id="btnReset" type="button"
                            class="hidden flex-1 sm:flex-none px-4 py-2 border rounded-lg hover:bg-slate-50 transition">
                            <i class="fa-solid fa-xmark mr-1"></i> Reset
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table id="datatable" class="w-full text-sm min-w-[700px] md:min-w-full table-auto">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">NIS</th>
                                <th class="px-4 py-2 text-left">Siswa</th>
                                <th class="px-4 py-2 text-left">Kelas</th>
                                <th class="px-4 py-2 text-left">Jenis</th>
                                <th class="px-4 py-2 text-left">Alasan</th>
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ALASAN TOLAK --}}
    <div id="viewRejectModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-xl p-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-red-600 flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark"></i> Alasan Penolakan
                </h2>
                <button onclick="closeViewRejectModal()" class="text-slate-400 hover:text-red-500">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
                <p id="rejectReasonText" class="whitespace-pre-line"></p>
            </div>
            <div class="mt-4 text-right">
                <button onclick="closeViewRejectModal()"
                    class="px-4 py-2 border rounded-lg hover:bg-slate-100">Tutup</button>
            </div>
        </div>
    </div>

    {{-- MODAL ALASAN IZIN --}}
    <div id="reasonModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-xl p-4 sm:p-6 relative">
            <button onclick="closeReasonModal()"
                class="absolute top-3 right-3 text-slate-400 hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <h2 class="text-lg font-semibold mb-3 flex items-center gap-2">
                <i class="fa-solid fa-comment-dots text-blue-600"></i> Alasan Izin
            </h2>
            <div class="text-sm text-slate-700 leading-relaxed break-words max-h-[60vh] overflow-y-auto" id="reasonContent">
                -</div>
            <button onclick="closeReasonModal()"
                class="mt-4 w-full bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg py-2 transition">Tutup</button>
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <div id="createModal" class="fixed inset-0 hidden bg-black/40 z-50 flex items-center justify-center p-2 sm:p-4">
        <div
            class="bg-white w-full max-w-4xl h-full sm:h-auto sm:max-h-[90vh] rounded-xl shadow-lg flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b bg-white sticky top-0 z-20">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-file-circle-plus text-blue-600"></i> Ajukan Izin
                </h2>
                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="/permissions" method="POST" enctype="multipart/form-data" id="createForm"
                class="flex-1 overflow-y-auto p-4 sm:p-6">
                @csrf
                <div id="violationAlert"
                    class="hidden mb-6 rounded-xl border border-rose-200 bg-rose-50 shadow-sm relative overflow-hidden">
                    <div class="relative flex flex-col sm:flex-row gap-4 p-5">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 rounded-xl bg-rose-500 shadow-md shadow-rose-200 flex items-center justify-center">
                                <i class="fa-solid fa-triangle-exclamation text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <h3 class="font-bold text-rose-900 tracking-tight text-base">Pengajuan Izin Terkunci</h3>
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-rose-200 text-rose-700">Status:
                                    Pelanggaran</span>
                            </div>
                            <div id="violationDetail" class="space-y-3"></div>
                        </div>
                    </div>
                </div>

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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 hidden" id="timeFields">
                    <div>
                        <label class="text-sm font-medium mb-1 block">Dari</label>
                        <div class="relative">
                            <i class="fa-regular fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="datetime-local" name="start_at"
                                class="border rounded-lg py-2 pl-10 w-full focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-1 block">Sampai</label>
                        <div class="relative">
                            <i class="fa-regular fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="datetime-local" name="end_at"
                                class="border rounded-lg py-2 pl-10 w-full focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="mb-5 hidden" id="reasonField">
                    <label class="text-sm font-medium mb-1">Alasan</label>
                    <textarea name="reason" rows="4" class="w-full border rounded-lg py-2 px-3"></textarea>
                </div>

                <div class="mb-5 hidden" id="addressField">
                    <label class="text-sm font-medium mb-1">Alamat</label>
                    <textarea name="address" rows="3" class="w-full border rounded-lg py-2 px-3"
                        placeholder="Alamat tempat siswa berada selama izin"></textarea>
                </div>

                <div class="mb-6 space-y-4 hidden" id="attachmentFields">
                    <div>
                        <label class="text-sm font-medium mb-2 block">Surat Orang Tua</label>
                        <label
                            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed rounded-xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-people-roof text-blue-600 text-xl"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-700" id="ortuText">Klik untuk upload surat orang
                                    tua</p>
                                <p class="text-xs text-slate-400">PDF / JPG / PNG • Max 2MB</p>
                            </div>
                            <input type="file" name="surat_ortu" class="hidden"
                                onchange="updateFileLabel(this, 'ortuText')">
                        </label>
                    </div>
                    <div id="suratDokterWrap">
                        <label class="text-sm font-medium mb-2 block">Surat Dokter</label>
                        <label
                            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed rounded-xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-user-doctor text-blue-600 text-xl"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-700" id="dokterText">Klik untuk upload surat dokter
                                </p>
                                <p class="text-xs text-slate-400">PDF / JPG / PNG • Max 2MB</p>
                            </div>
                            <input type="file" name="surat_dokter" class="hidden"
                                onchange="updateFileLabel(this, 'dokterText')">
                        </label>
                    </div>
                </div>

                <div class="border-t p-4 bg-white">
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" onclick="closeCreateModal()"
                            class="px-4 py-2 border rounded-lg">Batal</button>
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

    {{-- MODAL IZIN MASSAL --}}
    <div id="massalModal" class="fixed inset-0 hidden bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-xl shadow-lg flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-users text-emerald-600"></i> Izin Perpulangan Massal
                </h2>
                <button onclick="closeMassalModal()" class="text-slate-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="px-5 pt-4">
                <div
                    class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 flex gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0"></i>
                    <span>Izin akan dibuat otomatis untuk <strong>seluruh siswa</strong> di kelas Anda dan langsung
                        berstatus <strong>Disetujui</strong>.</span>
                </div>
            </div>
            <form action="{{ route('permissions.massal') }}" method="POST" id="massalForm" class="px-5 py-4 space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Mulai</label>
                    <div class="relative">
                        <i class="fa-regular fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="datetime-local" name="start_at" required
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal Selesai</label>
                    <div class="relative">
                        <i class="fa-regular fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="datetime-local" name="end_at" required
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700 mb-1 block">Alasan</label>
                    <div class="relative">
                        <i class="fa-solid fa-align-left absolute left-3 top-3 text-slate-400"></i>
                        <textarea name="reason" rows="3" required
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                            placeholder="Contoh: Libur semester, keperluan keluarga, dll"></textarea>
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <button type="button" onclick="closeMassalModal()"
                        class="flex-1 px-4 py-2 border rounded-lg hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" id="btnMassal"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                        <span id="btnMassalText">Buat Izin</span>
                        <svg id="btnMassalLoader" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.3" />
                            <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL QR MASSAL --}}
    <div id="qrMassalModal" class="fixed inset-0 hidden bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-xl shadow-lg overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-qrcode text-indigo-600"></i> Cetak QR Massal
                </h2>
                <button onclick="closeQrMassalModal()" class="text-slate-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="px-5 pt-4">
                <div class="rounded-lg bg-indigo-50 border border-indigo-200 px-4 py-3 text-sm text-indigo-800 flex gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0"></i>
                    <span>Hanya izin <strong>perpulangan</strong> berstatus <strong>disetujui</strong> yang dicetak.</span>
                </div>
            </div>
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="text-sm font-medium text-slate-700 mb-1 block">Tanggal</label>
                    <div class="relative">
                        <i class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" id="qrMassalDate"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                            value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div id="qrMassalPreview"
                    class="hidden rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 flex items-center gap-2">
                    <svg id="qrMassalLoader" class="hidden w-4 h-4 animate-spin text-indigo-600" viewBox="0 0 24 24"
                        fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.3" />
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                    </svg>
                    Ditemukan <span id="qrMassalCount" class="font-bold text-indigo-600 mx-1">0</span> siswa perpulangan
                </div>
                <div class="flex gap-2 pt-1">
                    <button type="button" onclick="closeQrMassalModal()"
                        class="flex-1 px-4 py-2 border rounded-lg hover:bg-slate-100 transition">Batal</button>
                    <button type="button" onclick="cetakQrMassal()" id="btnCetakQr"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print"></i> <span id="btnCetakQrText">Cetak QR</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL SURAT TERLAMBAT --}}
    <div id="terlambatModal" class="fixed inset-0 hidden bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-xl shadow-lg overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h2 class="text-base font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-orange-500"></i> Upload Surat Keterangan Terlambat
                </h2>
                <button onclick="closeTerlambatModal()" class="text-slate-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form id="terlambatForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                <div>
                    <label
                        class="flex items-center gap-3 px-4 py-4 border-2 border-dashed border-orange-300 rounded-xl cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition">
                        <i class="fa-solid fa-cloud-arrow-up text-orange-500 text-2xl"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-700" id="terlambatFileName">Klik untuk pilih file</p>
                            <p class="text-xs text-slate-400 mt-0.5">PDF / JPG / PNG • Maks. 2MB</p>
                        </div>
                        <input type="file" name="surat_terlambat" id="terlambatFile" required accept=".pdf,.jpg,.jpeg,.png"
                            class="hidden" onchange="updateTerlambatLabel(this)">
                    </label>
                </div>
                <div
                    class="rounded-lg bg-orange-50 border border-orange-200 px-3 py-2.5 text-xs text-orange-700 flex gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0"></i>
                    <span>Jika sudah pernah upload, file lama akan digantikan file baru.</span>
                </div>
                <div class="flex gap-2 pt-1">
                    <button type="button" onclick="closeTerlambatModal()"
                        class="flex-1 px-4 py-2 border rounded-lg hover:bg-slate-100 transition text-sm">Batal</button>
                    <button type="submit" id="btnTerlambat"
                        class="flex-1 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition flex items-center justify-center gap-2 text-sm">
                        <span id="btnTerlambatText">Upload</span>
                        <svg id="btnTerlambatLoader" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.3" />
                            <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL QR --}}
    <div id="qrModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-sm rounded-xl p-4 sm:p-6 relative">
            <button onclick="closeQrModal()" class="absolute top-3 right-3 text-slate-400 hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-barcode text-indigo-600"></i> Barcode Kepulangan
            </h2>
            <div class="flex justify-center mb-4 relative">
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
                <i class="fa-solid fa-print"></i> Cetak QR
            </button>
        </div>
    </div>

    {{-- MODAL REJECT --}}
    <div id="rejectModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-xl p-4 sm:p-6">
            <h2 class="text-lg font-semibold mb-4 text-red-600 flex items-center gap-2">
                <i class="fa-solid fa-circle-xmark"></i> Tolak Permohonan
            </h2>
            <form method="POST" id="rejectForm">
                @csrf
                <textarea name="reject_reason" required rows="3"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500"
                    placeholder="Alasan penolakan"></textarea>
                <div class="flex flex-col sm:flex-row justify-end gap-2 mt-4">
                    <button type="button" onclick="closeRejectModal()"
                        class="w-full sm:w-auto px-4 py-2 border rounded-lg hover:bg-slate-100 transition">Batal</button>
                    <button
                        class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Tolak</button>
                </div>
            </form>
        </div>
    </div>

    {{-- GLOBAL TOOLTIP --}}
    <div id="global-tooltip"
        class="fixed z-[99999] hidden bg-slate-900 text-white text-xs rounded-lg px-3 py-2 shadow-xl whitespace-nowrap pointer-events-none">
        <div id="tooltip-title" class="font-medium"></div>
        <div id="tooltip-desc" class="text-slate-300 text-[11px] mt-0.5"></div>
        <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-slate-900 rotate-45"></div>
    </div>

    @push('scripts')
        <script>
            // ── Tooltip ──────────────────────────────────────────────────────────
            document.addEventListener('mouseenter', function (e) {
                const el = e.target.closest('.tooltip-icon');
                if (!el) return;
                const tooltip = document.getElementById('global-tooltip');
                document.getElementById('tooltip-title').textContent = el.dataset.title || '';
                document.getElementById('tooltip-desc').textContent = el.dataset.desc || '';
                tooltip.classList.remove('hidden');
                const rect = el.getBoundingClientRect();
                tooltip.style.top = `${rect.top + window.scrollY - tooltip.offsetHeight - 10}px`;
                tooltip.style.left = `${rect.left + window.scrollX + rect.width / 2}px`;
                tooltip.style.transform = 'translateX(-50%)';
            }, true);
            document.addEventListener('mouseleave', function (e) {
                if (!e.target.closest('.tooltip-icon')) return;
                document.getElementById('global-tooltip')?.classList.add('hidden');
            }, true);

            // ── Filter state ──────────────────────────────────────────────────
            let dtStartDate = '';
            let dtEndDate = '';
            let dtStatus = '';
            let dtKelas = '';
            let dtCheckinStatus = '';

            // ── DataTable ─────────────────────────────────────────────────────
            const isWalikelas = {{ auth()->user()->role === 'wali_kelas' ? 'true' : 'false' }};
            const isPerizinan = {{ auth()->user()->role === 'perizinan' ? 'true' : 'false' }};

            if (isWalikelas) {
                dtKelas = document.getElementById('filterKelas').value;
                document.getElementById('pdfKelas').value = dtKelas;
            }

            const columns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nis', name: 'nis' },
                { data: 'student_name', name: 'name' },
                { data: 'class_name', name: 'class.name' },
                { data: 'type', name: 'type', className: 'capitalize' },
                { data: 'alasan', name: 'alasan', orderable: false, searchable: false },
                { data: 'waktu', name: 'start_at' },
                { data: 'file', name: 'file', orderable: false, searchable: false, className: 'text-center' },
                { data: 'status_badge', name: 'status' },
            ];

            if (isWalikelas) columns.push({ data: 'aksi_walas', name: 'aksi_walas', orderable: false, searchable: false });
            if (isPerizinan) columns.push({ data: 'aksi_perizinan', name: 'aksi_perizinan', orderable: false, searchable: false });

            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('permissions.data') }}',
                    data: function (d) {
                        d.status = dtStatus;
                        d.start_date = dtStartDate;
                        d.end_date = dtEndDate;
                        d.class_id = dtKelas;
                        d.checkin_status = dtCheckinStatus;
                    }
                },
                columns: columns,
                order: [[6, 'desc']],
                pageLength: 10,
                responsive: true,
                scrollX: true,
                autoWidth: false,
            });

            $('#filterKelas').on('change', function () {
                dtKelas = $(this).val();
            });

            // ── Flatpickr ─────────────────────────────────────────────────────
            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [
                    "{{ now()->startOfMonth()->format('Y-m-d') }}",
                    "{{ now()->endOfMonth()->format('Y-m-d') }}"
                ],
                onClose: function (dates) {
                    if (dates.length === 2) {
                        dtStartDate = dates[0].toISOString().slice(0, 10);
                        dtEndDate = dates[1].toISOString().slice(0, 10);
                        document.getElementById('startDate').value = dtStartDate;
                        document.getElementById('endDate').value = dtEndDate;
                    }
                }
            });

            // Inisialisasi default nilai flatpickr ke filter
            dtStartDate = "{{ now()->startOfMonth()->format('Y-m-d') }}";
            dtEndDate = "{{ now()->endOfMonth()->format('Y-m-d') }}";
            document.getElementById('startDate').value = dtStartDate;
            document.getElementById('endDate').value = dtEndDate;

            // ── Tombol Terapkan ───────────────────────────────────────────────
            document.getElementById('btnTerapkan').addEventListener('click', function () {

                dtStatus = document.getElementById('filterStatus').value;
                dtCheckinStatus = document.getElementById('filterCheckinStatus').value;
                dtKelas = document.getElementById('filterKelas').value;

                table.ajax.reload();

                document.getElementById('pdfStartDate').value = dtStartDate;
                document.getElementById('pdfEndDate').value = dtEndDate;
                document.getElementById('pdfStatus').value = dtStatus;
                document.getElementById('pdfKelas').value = dtKelas;
                document.getElementById('pdfCheckinStatus').value = dtCheckinStatus;

                document.getElementById('btnReset').classList.remove('hidden');
            });

            // ── Tombol Reset ──────────────────────────────────────────────────
            document.getElementById('btnReset').addEventListener('click', function () {
                dtStatus = '';
                dtStartDate = '';
                dtEndDate = '';
                dtKelas = '';
                dtCheckinStatus = '';
                document.getElementById('filterStatus').value = '';
                document.getElementById('startDate').value = '';
                document.getElementById('endDate').value = '';
                document.getElementById('filterCheckinStatus').value = '';
                $('#filterKelas').val('');
                table.ajax.reload();
                this.classList.add('hidden');
            });

            // ── Modal helpers ─────────────────────────────────────────────────
            function openReasonModal(reason) { document.getElementById('reasonContent').innerText = reason; $('#reasonModal').removeClass('hidden'); }
            function closeReasonModal() { $('#reasonModal').addClass('hidden'); }
            function showRejectReason(reason) { $('#rejectReasonText').text(reason || '-'); $('#viewRejectModal').removeClass('hidden'); }
            function closeViewRejectModal() { $('#viewRejectModal').addClass('hidden'); }
            function updateFileLabel(input, id) { if (input.files?.[0]) document.getElementById(id).innerText = input.files[0].name; }
            function openCreateModal() { $('#createModal').removeClass('hidden'); }
            function closeCreateModal() { $('#createModal').addClass('hidden'); }
            function openMassalModal() { $('#massalModal').removeClass('hidden'); }
            function closeMassalModal() { $('#massalModal').addClass('hidden'); $('#massalForm')[0].reset(); }
            function openTerlambatModal(id) { $('#terlambatForm').attr('action', `/permissions/upload-terlambat/${id}`); $('#terlambatFileName').text('Klik untuk pilih file'); $('#terlambatFile').val(''); $('#terlambatModal').removeClass('hidden'); }
            function closeTerlambatModal() { $('#terlambatModal').addClass('hidden'); $('#terlambatForm')[0].reset(); $('#terlambatFileName').text('Klik untuk pilih file'); }
            function updateTerlambatLabel(input) { if (input.files?.[0]) $('#terlambatFileName').text(input.files[0].name); }
            function openRejectModal(id) { $('#rejectForm').attr('action', `/permissions/${id}/reject`); $('#rejectModal').removeClass('hidden'); }
            function closeRejectModal() { $('#rejectModal').addClass('hidden'); }
            function closeQrModal() { $('#qrModal').addClass('hidden'); }
            function openQrMassalModal() { $('#qrMassalModal').removeClass('hidden'); fetchQrMassal(); }
            function closeQrMassalModal() { $('#qrMassalModal').addClass('hidden'); }

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
                        form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            // ── Submit loaders ────────────────────────────────────────────────
            $(document).ready(function () {
                $('.select2').select2({ width: '100%' });

                $('#createModal form').on('submit', function () {
                    $('#submitCreateBtn').prop('disabled', true).addClass('opacity-70');
                    $('#submitCreateText').text('Mengirim...');
                    $('#submitCreateLoader').removeClass('hidden');
                });
                $('#massalForm').on('submit', function () {
                    $('#btnMassal').prop('disabled', true).addClass('opacity-70');
                    $('#btnMassalText').text('Memproses...');
                    $('#btnMassalLoader').removeClass('hidden');
                });
                $('#terlambatForm').on('submit', function () {
                    $('#btnTerlambat').prop('disabled', true).addClass('opacity-70');
                    $('#btnTerlambatText').text('Mengupload...');
                    $('#btnTerlambatLoader').removeClass('hidden');
                });

                // Violation check
                $('select[name="student_id"]').on('change', function () {
                    const studentId = $(this).val();
                    $('#violationAlert').addClass('hidden');
                    $('#violationDetail').html('');
                    if (!studentId) {
                        $('#typeField, #timeFields, #reasonField, #attachmentFields, #addressField').addClass('hidden');
                        $('#submitCreateBtn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                        return;
                    }
                    $('#typeField, #timeFields, #reasonField, #attachmentFields, #addressField').removeClass('hidden');
                    $('#submitCreateBtn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                });

                const $typeField = $('select[name="type"]');
                const $suratDokterWrap = $('#suratDokterWrap');
                function toggleDokterField() {
                    $typeField.val() === 'sakit' ? $suratDokterWrap.show() : $suratDokterWrap.hide();
                }
                toggleDokterField();
                $typeField.on('change', toggleDokterField);

                $('select[name="type"]').on('change', function () {
                    const type = $(this).val();
                    const studentId = $('select[name="student_id"]').val();
                    $('#violationAlert').addClass('hidden');
                    $('#submitCreateBtn').prop('disabled', false).removeClass('opacity-50 grayscale cursor-not-allowed');
                    $('#violationDetail').html('');
                    if (!studentId || type === 'sakit') return;
                    $.get(`/permissions/check-violation/${studentId}`, function (res) {
                        if (!res.has_violation) return;
                        let html = '<div class="grid gap-3">';
                        for (const [vType, data] of Object.entries(res.details)) {
                            const isPengasuhan = vType === 'pengasuhan';
                            const icon = isPengasuhan ? 'fa-user-shield' : 'fa-calendar-check';
                            const color = isPengasuhan ? 'text-rose-600' : 'text-amber-600';
                            html += `<div class="bg-white/80 p-4 rounded-lg border border-rose-100 shadow-sm"><div class="flex gap-3"><i class="fa-solid ${icon} ${color} mt-1"></i><div class="flex-1 text-sm text-slate-700">${isPengasuhan ? `<div class="font-bold text-slate-900 mb-1">Pelanggaran Pengasuhan: ${data.type}</div><div class="italic text-slate-600 mb-2">"${data.description}"</div><div class="text-[11px] font-medium text-slate-500 uppercase"><i class="fa-regular fa-clock mr-1"></i>Hingga: ${data.until}</div>` : `<div class="font-bold text-slate-900 mb-1">Pelanggaran ${data.handling_type}</div><div class="mb-2">Kehadiran: <span class="font-bold text-rose-600">${data.attendance_percentage}%</span></div><div class="text-[11px] font-medium text-slate-500 uppercase"><i class="fa-regular fa-calendar-xmark mr-1"></i>Berlaku Sampai: ${data.until}</div>`}</div></div></div>`;
                        }
                        html += '</div>';
                        $('#violationDetail').html(html);
                        $('#violationAlert').removeClass('hidden').hide().slideDown(300);
                        $('#submitCreateBtn').prop('disabled', true).addClass('opacity-50 grayscale cursor-not-allowed');
                    });
                });
            });

            function showBarcode(token, nama, nis, kelas, asrama, izin, startAt, endAt) {
                barcodeReady = false;
                barcodeData = { token, nama, nis, kelas, asrama, izin, startAt, endAt };

                $('#qrModal').removeClass('hidden');
                $('#qrLoader').removeClass('hidden');
                $('#qrImage').addClass('hidden');
                $('#qrText').text('');
                $('#printQrBtn')
                    .prop('disabled', true)
                    .removeClass('bg-blue-600 hover:bg-blue-700 text-white')
                    .addClass('bg-slate-300 text-slate-500 cursor-not-allowed');

                try {
                    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");

                    JsBarcode(svg, token, {
                        format: "CODE128",
                        width: 2.5,
                        height: 70,
                        displayValue: false,
                        margin: 0
                    });

                    // tampilkan SVG di modal
                    document.getElementById('qrImage').outerHTML = svg.outerHTML;
                    $('#qrLoader').addClass('hidden');
                    $('#qrText').text(token);

                    barcodeReady = true;
                    $('#printQrBtn')
                        .prop('disabled', false)
                        .removeClass('bg-slate-300 text-slate-500 cursor-not-allowed')
                        .addClass('bg-blue-600 hover:bg-blue-700 text-white');

                } catch (e) {
                    $('#qrLoader').addClass('hidden');
                    $('#qrText').text('Gagal generate barcode');
                }
            }

            function printQr() {
                if (!barcodeReady) {
                    Swal.fire({ icon: 'warning', title: 'Barcode belum siap', confirmButtonColor: '#2563eb' });
                    return;
                }

                const d = barcodeData;

                // generate SVG khusus untuk print
                const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");

                JsBarcode(svg, d.token, {
                    format: "CODE128",
                    width: 2.5,
                    height: 60,
                    displayValue: false,
                    margin: 0
                });

                const barcodeHtml = svg.outerHTML;

                const win = window.open('', '_blank', 'width=400,height=400');

                win.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Tiket Izin</title>
                            <style>
                                @page { size: 58mm 45mm; margin: 0; }
                                * { box-sizing: border-box; margin: 0; padding: 0; }

                                html, body {
                                    width: 58mm;
                                    font-family: 'Arial Narrow', Arial, sans-serif;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }

                                .ticket {
                                    width: 54mm;
                                    margin: 2mm;
                                    border: 1px solid #000;
                                    padding: 2mm;
                                }

                                .title {
                                    text-align: center;
                                    font-weight: bold;
                                    font-size: 7px;
                                    border-bottom: 0.5px solid #000;
                                    margin-bottom: 1px;
                                    padding-bottom: 1px;
                                }

                                .info-container {
                                    display: grid;
                                    grid-template-columns: 1.5fr 1fr;
                                    font-size: 6px;
                                    line-height: 1.2;
                                    margin-bottom: 1px;
                                }

                                .col { display: flex; flex-direction: column; }
                                .item { display: flex; margin-bottom: 0.5px; }
                                .label { font-weight: bold; width: 8mm; flex-shrink: 0; }

                                .barcode-section {
                                    text-align: center;
                                    border-top: 0.5px dashed #000;
                                    padding-top: 1px;
                                }

                                .barcode-section svg {
                                    width: 48mm;
                                    height: auto;
                                    display: block;
                                    margin: 0 auto;
                                }

                                .time {
                                    display: flex;
                                    justify-content: space-between;
                                    font-size: 5px;
                                    border-top: 0.5px solid #ccc;
                                    padding-top: 1px;
                                    margin-top: 1px;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="ticket">
                                <div class="title">IZIN KEPULANGAN</div>

                                <div class="info-container">
                                    <div class="col">
                                        <div class="item"><span class="label">Nama</span>: ${d.nama}</div>
                                        <div class="item"><span class="label">NIS</span>: ${d.nis}</div>
                                        <div class="item"><span class="label">Kelas</span>: ${d.kelas}</div>
                                    </div>
                                    <div class="col">
                                        <div class="item"><span class="label">Asrama</span>: ${d.asrama}</div>
                                        <div class="item"><span class="label">Izin</span>: ${d.izin}</div>
                                    </div>
                                </div>

                                <div class="barcode-section">
                                    ${barcodeHtml}
                                </div>

                                <div class="time">
                                    <div><b>Mulai:</b> ${d.startAt}</div>
                                    <div style="text-align:right"><b>Sampai:</b> ${d.endAt}</div>
                                </div>
                            </div>

                            <script>
                                window.onload = function () {
                                    setTimeout(function() {
                                        window.print();
                                        setTimeout(() => { window.close(); }, 800);
                                    }, 400);
                                }
                            <\/script>
                        </body>
                        </html>
                    `);

                win.document.close();
                win.focus();
            }

            // ── QR Massal ─────────────────────────────────────────────────────
            let qrMassalRows = [];
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('qrMassalDate')?.addEventListener('change', fetchQrMassal);
            });

            function fetchQrMassal() {
                const tanggal = document.getElementById('qrMassalDate').value;
                if (!tanggal) return;
                $('#qrMassalPreview').removeClass('hidden');
                $('#qrMassalLoader').removeClass('hidden');
                $('#qrMassalCount').text('...');
                $('#btnCetakQr').prop('disabled', true).addClass('opacity-60 cursor-not-allowed');
                fetch(`/permissions/qr-massal?tanggal=${tanggal}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(r => { if (!r.ok) throw new Error('Gagal fetch data'); return r.json(); })
                    .then(json => { qrMassalRows = json.data; $('#qrMassalCount').text(json.count); $('#qrMassalLoader').addClass('hidden'); $('#btnCetakQr').prop('disabled', false).removeClass('opacity-60 cursor-not-allowed'); })
                    .catch(err => { $('#qrMassalLoader').addClass('hidden'); $('#qrMassalCount').text('Error'); Swal.fire({ icon: 'error', title: 'Gagal memuat data', text: err.message, confirmButtonColor: '#4f46e5' }); });
            }

            function cetakQrMassal() {
                const filterDate = document.getElementById('qrMassalDate').value;
                if (!qrMassalRows.length) {
                    Swal.fire({ icon: 'info', title: 'Tidak ada data', confirmButtonColor: '#4f46e5' });
                    return;
                }

                const cards = qrMassalRows.map(d => {
                    let barcodeHtml = '';

                    try {
                        if (d.token) {
                            const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");

                            JsBarcode(svg, d.token, {
                                format: "CODE128",
                                width: 2.5,
                                height: 60,
                                displayValue: false,
                                margin: 0
                            });

                            barcodeHtml = svg.outerHTML;
                        }
                    } catch (e) {
                        console.error(e);
                    }

                    return `
                            <div class="ticket">
                                <div class="title">IZIN KEPULANGAN</div>

                                <div class="info-container">
                                    <div class="col">
                                        <div class="item"><span class="label">Nama</span>: ${d.nama}</div>
                                        <div class="item"><span class="label">NIS</span>: ${d.nis}</div>
                                        <div class="item"><span class="label">Kelas</span>: ${d.kelas}</div>
                                    </div>
                                    <div class="col">
                                        <div class="item"><span class="label">Asrama</span>: ${d.asrama || '-'}</div>
                                        <div class="item"><span class="label">Izin</span>: ${d.izin || 'Perpulangan'}</div>
                                    </div>
                                </div>

                                <div class="barcode-section">
                                    ${barcodeHtml || '<i style="font-size:6px">Token Error</i>'}
                                </div>

                                <div class="time">
                                    <div><b>Mulai:</b> ${d.start_at}</div>
                                    <div style="text-align:right"><b>Sampai:</b> ${d.end_at}</div>
                                </div>
                            </div>
                        `;
                }).join('');

                const win = window.open('', '_blank', 'width=400,height=600');

                win.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Cetak Massal Thermal</title>

                            <style>
                                @page { size: 58mm 45mm; margin: 0; }
                                * { box-sizing: border-box; margin: 0; padding: 0; }

                                html, body {
                                    width: 58mm;
                                    font-family: 'Arial Narrow', Arial, sans-serif;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                }

                                .ticket {
                                    width: 54mm;
                                    margin: 2mm;
                                    border: 1px solid #000;
                                    padding: 2mm;
                                    page-break-after: always;
                                }

                                .ticket:last-child {
                                    page-break-after: avoid;
                                }

                                .title {
                                    text-align: center;
                                    font-weight: bold;
                                    font-size: 7px;
                                    border-bottom: 0.5px solid #000;
                                    margin-bottom: 1px;
                                    padding-bottom: 1px;
                                }

                                .info-container {
                                    display: grid;
                                    grid-template-columns: 1.5fr 1fr;
                                    font-size: 6px;
                                    line-height: 1.2;
                                    margin-bottom: 1px;
                                }

                                .col { display: flex; flex-direction: column; }
                                .item { display: flex; margin-bottom: 0.5px; }
                                .label { font-weight: bold; width: 8mm; flex-shrink: 0; }

                                .barcode-section {
                                    text-align: center;
                                    border-top: 0.5px dashed #000;
                                    padding-top: 1px;
                                }

                                /* SVG jangan di-scale aneh */
                                .barcode-section svg {
                                    width: 48mm;
                                    height: auto;
                                    display: block;
                                    margin: 0 auto;
                                }

                                .time {
                                    display: flex;
                                    justify-content: space-between;
                                    font-size: 5px;
                                    border-top: 0.5px solid #ccc;
                                    padding-top: 1px;
                                    margin-top: 1px;
                                }
                            </style>
                        </head>

                        <body>
                            ${cards}

                            <script>
                                window.onload = function () {
                                    setTimeout(function() {
                                        window.print();
                                        setTimeout(() => window.close(), 800);
                                    }, 500);
                                }
                            <\/script>
                        </body>
                        </html>
                    `);

                win.document.close();
                win.focus();
            }
        </script>
    @endpush
@endsection