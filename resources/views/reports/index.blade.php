@extends('layouts.app')

@section('title', 'Laporan Bulanan')


@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl shadow">

        {{-- ══ HEADER ══ --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-chart-line text-blue-600"></i>
                    Laporan Bulanan
                </h1>
                <p class="text-xs md:text-sm text-slate-500 mt-0.5">
                    Periode {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                {{-- Active filter chips --}}
                @if(request('start_date') || request('end_date'))
                    <span
                        class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-600 text-[10px] md:text-xs rounded border whitespace-nowrap">
                        <i class="fa-solid fa-calendar-days text-blue-400 text-[10px]"></i>
                        {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M') : '—' }}
                        – {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : '—' }}
                    </span>
                @endif

                @if(request('class_id'))
                    <span
                        class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-600 text-[10px] md:text-xs rounded border whitespace-nowrap">
                        <i class="fa-solid fa-chalkboard text-blue-400 text-[10px]"></i>
                        {{ $classes->firstWhere('id', request('class_id'))?->name ?? 'Kelas' }}
                    </span>
                @endif

                <div class="flex items-center gap-2">
                    <button type="button" onclick="openFilter()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition whitespace-nowrap">
                        <i class="fa-solid fa-sliders"></i> Filter
                        @if(collect(['start_date', 'end_date', 'jam_mulai', 'jam_akhir', 'class_id', 'dormitory_id', 'gender'])->filter(fn($k) => request($k))->count() > 0)
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-500 text-white text-[10px] font-bold">
                                {{ collect(['start_date', 'end_date', 'jam_mulai', 'jam_akhir', 'class_id', 'dormitory_id', 'gender'])->filter(fn($k) => request($k))->count() }}
                            </span>
                        @endif
                    </button>

                    @if(collect(['start_date', 'end_date', 'jam_mulai', 'jam_akhir', 'class_id', 'dormitory_id', 'gender'])->filter(fn($k) => request($k))->count() > 0)
                        <a href="{{ route('reports.index') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-2.5 border border-slate-200 text-slate-500 text-sm rounded-lg hover:bg-slate-50 transition">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══ SUMMARY CARDS ══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 flex items-center gap-4">
                <div
                    class="shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-envelope-open-text text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Total Izin</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $summary['total_izin'] }}</p>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-center gap-4">
                <div
                    class="shrink-0 w-12 h-12 bg-rose-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-rose-600 uppercase tracking-wider">Terlambat</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $summary['total_late'] }}</p>
                </div>
            </div>

            <div
                class="p-4 rounded-xl bg-amber-50 border border-amber-100 flex items-center gap-4 sm:col-span-2 lg:col-span-1">
                <div
                    class="shrink-0 w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Pelanggaran</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $summary['total_violation'] }}</p>
                </div>
            </div>
        </div>

        {{-- ══ CHECK-IN SECTION ══ --}}
        <div class="mb-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

                <div class="flex items-center gap-3">
                    <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                    <h2 class="text-base sm:text-lg md:text-xl font-bold text-slate-800">
                        Data Check-in Siswa
                    </h2>
                </div>

                <div
                    class="text-[10px] sm:text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full text-center sm:text-right">
                    {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
                    <span class="hidden sm:inline">&bull;</span>
                    <br class="sm:hidden">
                    {{ $jamMulai }} – {{ $jamAkhir }}
                </div>

            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                {{-- Sudah Laki --}}
                <div class="rounded-xl p-4 border-l-4 border-green-500 bg-green-50 shadow-sm hover:shadow-md transition">
                    <p class="text-xs font-semibold text-slate-500 mb-2">Sudah Check-in</p>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl sm:text-3xl xl:text-4xl font-bold text-green-700">
                            {{ $checkinSummary['sudah_l'] }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-slate-500 text-right leading-4">
                            <i class="fa-solid fa-mars text-blue-500"></i><br>Laki-laki
                        </span>
                    </div>
                </div>

                {{-- Sudah Perempuan --}}
                <div class="rounded-xl p-4 border-l-4 border-pink-500 bg-pink-50 shadow-sm hover:shadow-md transition">
                    <p class="text-xs font-semibold text-slate-500 mb-2">Sudah Check-in</p>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl sm:text-3xl xl:text-4xl font-bold text-pink-700">
                            {{ $checkinSummary['sudah_p'] }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-slate-500 text-right leading-4">
                            <i class="fa-solid fa-venus text-pink-500"></i><br>Perempuan
                        </span>
                    </div>
                </div>

                {{-- Belum Laki --}}
                <div class="rounded-xl p-4 border-l-4 border-orange-500 bg-orange-50 shadow-sm hover:shadow-md transition">
                    <p class="text-xs font-semibold text-slate-500 mb-2">Belum Check-in</p>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl sm:text-3xl xl:text-4xl font-bold text-orange-700">
                            {{ $checkinSummary['belum_l'] }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-slate-500 text-right leading-4">
                            <i class="fa-solid fa-mars text-blue-500"></i><br>Laki-laki
                        </span>
                    </div>
                </div>

                {{-- Belum Perempuan --}}
                <div class="rounded-xl p-4 border-l-4 border-red-500 bg-red-50 shadow-sm hover:shadow-md transition">
                    <p class="text-xs font-semibold text-slate-500 mb-2">Belum Check-in</p>
                    <div class="flex items-end justify-between">
                        <span class="text-2xl sm:text-3xl xl:text-4xl font-bold text-red-700">
                            {{ $checkinSummary['belum_p'] }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-slate-500 text-right leading-4">
                            <i class="fa-solid fa-venus text-pink-500"></i><br>Perempuan
                        </span>
                    </div>
                </div>

            </div>

        </div>

        {{-- Tabel Check-in --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-5 mb-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

                <h2 class="font-bold flex items-center gap-2 text-blue-600 text-base sm:text-lg">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Daftar Check-in Siswa
                </h2>

                <a href="{{ route('reports.export-pdf', array_filter(request()->all())) }}" target="_blank" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 
                                  bg-red-600 text-white text-sm font-semibold rounded-lg 
                                  shadow-sm hover:bg-red-700 active:scale-95 
                                  transition-all duration-200">
                    <i class="fa-solid fa-file-pdf text-base"></i>
                    Export PDF
                </a>

            </div>

            {{-- Table Wrapper --}}
            <div class="overflow-x-auto rounded-lg border border-slate-100">

                <table id="tableCheckin" class="min-w-[900px] w-full text-xs sm:text-sm">

                    <thead class="bg-slate-50 text-slate-700 whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3 sm:px-4 text-left">No</th>
                            <th class="py-3 px-3 sm:px-4 text-left">NIS</th>
                            <th class="py-3 px-3 sm:px-4 text-left">Nama</th>
                            <th class="py-3 px-3 sm:px-4 text-center">JK</th>
                            <th class="py-3 px-3 sm:px-4 text-left">Kelas</th>
                            <th class="py-3 px-3 sm:px-4 text-left">Alasan</th>
                            <th class="py-3 px-3 sm:px-4 text-left">Keperluan</th>
                            <th class="py-3 px-3 sm:px-4 text-left">Check-in</th>
                            <th class="py-3 px-3 sm:px-4 text-left">Check-out</th>
                            <th class="py-3 px-3 sm:px-4 text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white"></tbody>

                </table>

            </div>

        </div>

        {{-- Tabel Terlambat --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-8">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-rose-600 text-lg">
                <i class="fa-solid fa-clock-rotate-left"></i> Daftar Siswa Terlambat
            </h2>
            <div class="overflow-x-auto">
                <table id="tableLate" class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">NIS</th>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Kelas</th>
                            <th class="py-3 px-4 text-left">Waktu Datang</th>
                            <th class="py-3 px-4 text-left">Terlambat</th>
                            <th class="py-3 px-4 text-left">Lampiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y"></tbody>
                </table>
            </div>
        </div>

        {{-- Tabel Pelanggaran --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-8">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-amber-600 text-lg">
                <i class="fa-solid fa-triangle-exclamation"></i> Daftar Pelanggaran
            </h2>
            <div class="overflow-x-auto">
                <table id="tableViolation" class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">NIS</th>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Kelas</th>
                            <th class="py-3 px-4 text-left">Jenis</th>
                            <th class="py-3 px-4 text-left">Deskripsi</th>
                            <th class="py-3 px-4 text-left">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y"></tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ══ FILTER OFFCANVAS ══ --}}
    <div id="filterBackdrop" onclick="closeFilter()" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[9998]">
    </div>

    <div id="filterPanel"
        class="fixed top-0 right-0 h-screen w-[360px] bg-white z-[9999] shadow-[-8px_0_40px_rgba(0,0,0,0.12)]
                                                       translate-x-full transition-transform duration-300 ease-[cubic-bezier(.4,0,.2,1)] flex flex-col font-[Poppins,sans-serif]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-sliders text-blue-600 text-sm"></i>
                </div>
                <span class="font-bold text-slate-800 text-[15px] tracking-tight">Filter Laporan</span>
            </div>
            <button onclick="closeFilter()"
                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors duration-150">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-6">
            <form method="GET" action="{{ route('reports.index') }}" id="filterForm">

                {{-- PERIODE --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Periode</p>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-600">Tanggal Mulai</label>
                        <div class="relative">
                            <i
                                class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <input type="date" name="start_date"
                                value="{{ request('start_date', $startDate->toDateString()) }}"
                                class="w-full h-10 pl-8 pr-3 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-150">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-600">Tanggal Akhir</label>
                        <div class="relative">
                            <i
                                class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <input type="date" name="end_date" value="{{ request('end_date', $endDate->toDateString()) }}"
                                class="w-full h-10 pl-8 pr-3 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-150">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                {{-- RENTANG JAM --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rentang Jam Check-in</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600">Jam Mulai</label>
                            <div class="relative">
                                <i
                                    class="fa-regular fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                <input type="time" name="jam_mulai" value="{{ request('jam_mulai', '00:00') }}"
                                    class="w-full h-10 pl-8 pr-2 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-150">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600">Jam Akhir</label>
                            <div class="relative">
                                <i
                                    class="fa-regular fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                <input type="time" name="jam_akhir" value="{{ request('jam_akhir', '23:59') }}"
                                    class="w-full h-10 pl-8 pr-2 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-150">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                {{-- DATA SISWA --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Data Siswa</p>

                    {{-- Gender toggle --}}
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold text-slate-600">Jenis Kelamin</label>

                        <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-xl">

                            <button type="button" id="btn-gender-all" onclick="setGender('')"
                                class="gender-btn text-[10px] sm:text-xs py-1.5 px-2">
                                <i class="fa-solid fa-users text-[9px]"></i>
                                Semua
                            </button>

                            <button type="button" id="btn-gender-l" onclick="setGender('L')"
                                class="gender-btn text-[10px] sm:text-xs py-1.5 px-2">
                                <i class="fa-solid fa-mars text-[9px]"></i>
                                Laki-laki
                            </button>

                            <button type="button" id="btn-gender-p" onclick="setGender('P')"
                                class="gender-btn text-[10px] sm:text-xs py-1.5 px-2">
                                <i class="fa-solid fa-venus text-[9px]"></i>
                                Perempuan
                            </button>

                        </div>

                        <input type="hidden" name="gender" id="inputGender" value="{{ request('gender', '') }}">
                    </div>

                    {{-- Kelas --}}
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-600">Kelas</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-chalkboard absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <select name="class_id"
                                class="w-full h-10 pl-8 pr-8 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-150 appearance-none cursor-pointer">
                                <option value="">Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i
                                class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Asrama --}}
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-600">Asrama</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-building absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <select name="dormitory_id"
                                class="w-full h-10 pl-8 pr-8 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-150 appearance-none cursor-pointer">
                                <option value="">Semua Asrama</option>
                                @foreach($dormitories as $dorm)
                                    <option value="{{ $dorm->id }}" {{ request('dormitory_id') == $dorm->id ? 'selected' : '' }}>
                                        {{ $dorm->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i
                                class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-4 border-t border-slate-100 shrink-0">
            <div class="grid grid-cols-2 gap-2.5">
                <a href="{{ route('reports.index') }}"
                    class="h-10 flex items-center justify-center gap-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all duration-150">
                    <i class="fa-solid fa-rotate-left text-xs"></i> Reset
                </a>
                <button type="submit" form="filterForm"
                    class="h-10 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg text-sm font-semibold transition-all duration-150 shadow-sm shadow-blue-200">
                    <i class="fa-solid fa-filter text-xs"></i> Terapkan
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {

            // ── Helpers ───────────────────────────────────────────────────────
            const params = () => ({
                start_date: '{{ request('start_date') }}',
                end_date: '{{ request('end_date') }}',
                jam_mulai: '{{ request('jam_mulai', '00:00') }}',
                jam_akhir: '{{ request('jam_akhir', '23:59') }}',
                class_id: '{{ request('class_id') }}',
                dormitory_id: '{{ request('dormitory_id') }}',
                gender: '{{ request('gender') }}',
            });

            const periodText = 'Periode {{ $startDate->format("d M Y") }} - {{ $endDate->format("d M Y") }}';

            function pdfStyle(doc, widths) {
                doc.styles.title = { alignment: 'center', fontSize: 14, bold: true, margin: [0, 0, 0, 6] };
                doc.styles.message = { alignment: 'center', fontSize: 10, margin: [0, 0, 0, 14] };
                const table = doc.content.find(c => c.table);
                table.table.widths = widths;
                doc.styles.tableHeader = { fontSize: 9, bold: true, alignment: 'center', fillColor: '#1f2937', color: '#ffffff' };
                table.table.body.forEach((row, i) => {
                    if (i === 0) return;
                    row.forEach((cell, j) => {
                        cell.fontSize = 9;
                        if (j === 0) { cell.alignment = 'center'; cell.noWrap = true; }
                    });
                });
                table.layout = {
                    hLineWidth: () => 0.8, vLineWidth: () => 0.8,
                    hLineColor: () => '#e5e7eb', vLineColor: () => '#e5e7eb',
                    paddingLeft: () => 6, paddingRight: () => 6,
                    paddingTop: () => 4, paddingBottom: () => 4,
                };
            }

            // ── Table: Check-in ───────────────────────────────────────────────
            $('#tableCheckin').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('reports.data.checkin') }}',
                    data: d => Object.assign(d, params()),
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-slate-400 text-center' },
                    { data: 'nis', name: 'students.nis', className: 'font-mono text-xs text-slate-600' },
                    { data: 'nama', name: 'students.name', className: 'font-medium text-slate-900' },
                    { data: 'gender_badge', name: 'students.gender', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'kelas', name: 'classes.name', className: 'text-slate-600' },
                    { data: 'alasan', name: 'student_permissions.reason', className: 'text-xs text-slate-500 max-w-[200px]' },
                    { data: 'keperluan', name: 'student_permissions.type', orderable: false, searchable: false },
                    { data: 'checkin_at', name: 'checkin_at', className: 'text-slate-700 text-xs' },
                    { data: 'checkout_at', name: 'checkout_at', className: 'text-slate-700 text-xs' },
                    { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'text-center' },
                ],
                order: [[4, 'asc']],
                pageLength: 10,
                language: dtLang(),
            });

            // ── Table: Terlambat ──────────────────────────────────────────────
            $('#tableLate').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('reports.data.terlambat') }}',
                    data: d => Object.assign(d, params()),
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-slate-400' },
                    { data: 'nis', name: 'students.nis', className: 'font-medium text-slate-900' },
                    { data: 'nama', name: 'students.name', className: 'font-medium text-slate-900' },
                    { data: 'kelas', name: 'classes.name' },
                    { data: 'waktu_datang', orderable: false, searchable: false, className: 'text-rose-600 font-medium' },
                    { data: 'durasi_terlambat', orderable: false, searchable: false },
                    { data: 'lampiran', orderable: false, searchable: false },
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Laporan Siswa Terlambat',
                        messageTop: periodText,
                        className: 'buttons-excel',
                        text: '<i class="fa-solid fa-file-excel mr-1"></i> Excel',
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Laporan Siswa Terlambat',
                        messageTop: periodText,
                        orientation: 'landscape',
                        pageSize: 'A4',
                        className: 'buttons-pdf',
                        text: '<i class="fa-solid fa-file-pdf mr-1"></i> PDF',
                        customize: doc => pdfStyle(doc, ['4%', '13%', '28%', '13%', '18%', '14%', '10%']),
                    },
                ],
                pageLength: 10,
                language: dtLang(),
            });

            // ── Table: Pelanggaran ────────────────────────────────────────────
            $('#tableViolation').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('reports.data.pelanggaran') }}',
                    data: d => Object.assign(d, params()),
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-slate-400' },
                    { data: 'nis', name: 'students.nis', className: 'font-medium text-slate-900' },
                    { data: 'name', name: 'students.name', className: 'font-medium text-slate-900' },
                    { data: 'class_name', name: 'classes.name' },
                    { data: 'jenis', orderable: false, searchable: false },
                    { data: 'deskripsi', orderable: false, searchable: false, className: 'text-slate-600' },
                    { data: 'tanggal', name: 'student_violations.occurred_at', className: 'text-slate-600' },
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Laporan Pelanggaran Siswa',
                        messageTop: periodText,
                        className: 'buttons-excel',
                        text: '<i class="fa-solid fa-file-excel mr-1"></i> Excel',
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Laporan Pelanggaran Siswa',
                        messageTop: periodText,
                        orientation: 'portrait',
                        pageSize: 'A4',
                        className: 'buttons-pdf',
                        text: '<i class="fa-solid fa-file-pdf mr-1"></i> PDF',
                        customize: doc => pdfStyle(doc, ['5%', '12%', '18%', '10%', '15%', '25%', '15%']),
                    },
                ],
                pageLength: 10,
                language: dtLang(),
            });

            // ── Offcanvas ─────────────────────────────────────────────────────
            window.openFilter = () => {
                document.getElementById('filterBackdrop').style.display = 'block';
                document.getElementById('filterPanel').style.transform = 'translateX(0)';
                document.body.style.overflow = 'hidden';
            };
            window.closeFilter = () => {
                document.getElementById('filterPanel').style.transform = 'translateX(100%)';
                document.getElementById('filterBackdrop').style.display = 'none';
                document.body.style.overflow = '';
            };
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeFilter(); });

            // ── Gender toggle ─────────────────────────────────────────────────
            const initGender = '{{ request('gender', '') }}';
            window.setGender = (val) => {
                document.getElementById('inputGender').value = val;
                ['all', 'l', 'p'].forEach(k => {
                    const expected = k === 'all' ? '' : k.toUpperCase();
                    const el = document.getElementById('btn-gender-' + k);
                    el.classList.toggle('active', val === expected);
                });
            };
            setGender(initGender);

            // ── DataTables language ───────────────────────────────────────────
            function dtLang() {
                return {
                    search: '<i class="fa-solid fa-magnifying-glass text-slate-400 mr-1"></i>',
                    searchPlaceholder: 'Cari...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_-_END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: {
                        first: '<i class="fa-solid fa-angles-left"></i>',
                        previous: '<i class="fa-solid fa-angle-left"></i>',
                        next: '<i class="fa-solid fa-angle-right"></i>',
                        last: '<i class="fa-solid fa-angles-right"></i>',
                    },
                    processing: '<div class="text-center text-sm text-slate-500 py-4"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Memuat data...</div>',
                };
            }
        });
    </script>
@endpush