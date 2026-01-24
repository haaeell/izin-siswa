@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl shadow-md">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-file-chart-line text-blue-600"></i> Laporan
            </h1>
            <nav class="text-sm text-slate-500 mt-1">
                <ol class="flex items-center gap-2">
                    <li><a href="/home" class="hover:text-blue-600"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                    <li>/</li>
                    <li class="text-slate-700 font-medium">Laporan</li>
                </ol>
            </nav>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="bg-slate-50 border rounded-xl p-4 mb-6
                   grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

            {{-- TANGGAL MULAI --}}
            <div class="flex flex-col">
                <label class="text-sm font-medium mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="h-11 px-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
            </div>

            {{-- TANGGAL AKHIR --}}
            <div class="flex flex-col">
                <label class="text-sm font-medium mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="h-11 px-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
            </div>

            {{-- KELAS --}}
            <div class="flex flex-col">
                <label class="text-sm font-medium mb-1">Kelas</label>
                <select name="class_id" class="h-11 px-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                    <option value="">Semua Kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- BUTTON --}}
            <div class="flex">
                <button class="h-11 w-full bg-blue-600 text-white rounded-lg
                           hover:bg-blue-700 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i> Tampilkan
                </button>
            </div>

        </form>


        {{-- SUMMARY --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

            {{-- TOTAL IZIN --}}
            <div class="p-4 rounded-xl flex items-center gap-3 
                                                                bg-blue-50 border border-blue-200">
                <i class="fa-solid fa-envelope-open-text text-blue-600 text-2xl"></i>
                <div>
                    <p class="text-sm text-blue-600/80">Total Izin</p>
                    <p class="text-2xl font-semibold text-blue-700">
                        {{ $summary['total_permission'] }}
                    </p>
                </div>
            </div>

            {{-- TERLAMBAT --}}
            <div class="p-4 rounded-xl flex items-center gap-3 
                                                                bg-red-50 border border-red-200">
                <i class="fa-solid fa-clock text-red-600 text-2xl"></i>
                <div>
                    <p class="text-sm text-red-600/80">Datang Terlambat</p>
                    <p class="text-2xl font-semibold text-red-700">
                        {{ $summary['late_checkin'] }}
                    </p>
                </div>
            </div>

            {{-- TOTAL PELANGGARAN --}}
            <div class="p-4 rounded-xl flex items-center gap-3 
                                                                bg-yellow-50 border border-yellow-200">
                <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-2xl"></i>
                <div>
                    <p class="text-sm text-yellow-700/80">Pelanggaran</p>
                    <p class="text-2xl font-semibold text-yellow-800">
                        {{ $summary['total_violation'] }}
                    </p>
                </div>
            </div>

            {{-- PELANGGARAN BERAT --}}
            <div class="p-4 rounded-xl flex items-center gap-3 
                                                                bg-rose-50 border border-rose-200">
                <i class="fa-solid fa-skull-crossbones text-rose-600 text-2xl"></i>
                <div>
                    <p class="text-sm text-rose-600/80">Pelanggaran Berat</p>
                    <p class="text-2xl font-semibold text-rose-700">
                        {{ $summary['heavy_violation'] }}
                    </p>
                </div>
            </div>

        </div>

        {{-- CHART --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            {{-- BAR CHART --}}
            <div class="bg-white border rounded-xl p-4 shadow">
                <h2 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-blue-600"></i>
                    Ringkasan Aktivitas
                </h2>
                <canvas id="summaryChart" height="80"></canvas>
            </div>

            {{-- PIE CHART --}}
            <div class="bg-white border rounded-xl p-4 shadow">
                <h2 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-rose-600"></i>
                    Proporsi Pelanggaran
                </h2>
                <canvas id="violationChart" height="80" style="max-height: 200px"></canvas>
            </div>

        </div>



        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th> Izin</th>
                        <th>Terlambat</th>
                        <th>Pelanggaran (R/S/B)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $i => $row)
                        <tr class="hover:bg-slate-50">
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->class_name }}</td>
                            <td class="text-center">{{ $row->permission_count }}</td>
                            <td class="text-center">{{ $row->late_count }}</td>
                            <td class="text-center">
                                {{ $row->light }}/{{ $row->medium }}/{{ $row->heavy }}
                            </td>
                            <td>
                                @if ($row->heavy > 0)
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Kritis
                                    </span>
                                @elseif ($row->medium > 0 || $row->late_count > 0)
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i> Perlu Perhatian
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs flex items-center gap-1">
                                        <i class="fa-solid fa-check"></i> Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // BAR CHART - SUMMARY
            const summaryCtx = document.getElementById('summaryChart');
            new Chart(summaryCtx, {
                type: 'bar',
                data: {
                    labels: ['Izin', 'Terlambat', 'Pelanggaran', 'Pelanggaran Berat'],
                    datasets: [{
                        label: 'Jumlah',
                        data: [
                                                                            {{ $summary['total_permission'] }},
                                                                            {{ $summary['late_checkin'] }},
                                                                            {{ $summary['total_violation'] }},
                                                                            {{ $summary['heavy_violation'] }},
                        ],
                        backgroundColor: [
                            '#3b82f6',
                            '#ef4444',
                            '#f59e0b',
                            '#be123c'
                        ],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // PIE CHART - VIOLATION
            const violationCtx = document.getElementById('violationChart');
            new Chart(violationCtx, {
                type: 'pie',
                data: {
                    labels: ['Ringan', 'Sedang', 'Berat'],
                    datasets: [{
                        data: [
                                                                            {{ $rows->sum('light') }},
                                                                            {{ $rows->sum('medium') }},
                                                                            {{ $rows->sum('heavy') }},
                        ],
                        backgroundColor: [
                            '#fde68a',
                            '#fb923c',
                            '#ef4444'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // DATATABLE
            $(document).ready(function () {
                $('#datatable').DataTable({
                    pageLength: 10,
                    order: [[1, 'asc']],
                    responsive: true
                });
            });
        </script>
    @endpush
@endsection