@extends('layouts.app')

@section('title', 'Laporan Bulanan')

{{-- Tambahkan CSS DataTables di Head --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2563eb !important;
            color: white !important;
            border: 1px solid #2563eb !important;
        }

        .dt-buttons {
            margin-bottom: 15px;
        }

        button.dt-button {
            background: #ef4444 !important;
            color: white !important;
            border: none !important;
            border-radius: 6px !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl shadow">

        {{-- HEADER --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-chart-line text-blue-600"></i>
                    Laporan Bulanan
                </h1>
                <p class="text-sm text-slate-500">
                    Periode {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                </p>
            </div>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-slate-50 p-4 rounded-xl border">
            <div>
                <label class="text-sm font-medium block mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->toDateString()) }}"
                    class="w-full h-11 px-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="text-sm font-medium block mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->toDateString()) }}"
                    class="w-full h-11 px-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full h-11 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 font-medium">
                    <i class="fa-solid fa-filter"></i>
                    Tampilkan Laporan
                </button>
            </div>
        </form>

        {{-- SUMMARY CARD --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div
                class="p-4 rounded-xl bg-blue-50 border border-blue-100 flex items-center gap-4 transition hover:shadow-md">
                <div
                    class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-envelope-open-text text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">TOTAL IZIN</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $summary['total_izin'] }}</p>
                </div>
            </div>

            <div
                class="p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-center gap-4 transition hover:shadow-md">
                <div
                    class="w-12 h-12 bg-rose-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-rose-200">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-rose-600 uppercase tracking-wider">Terlambat</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $summary['total_late'] }}</p>
                </div>
            </div>

            <div
                class="p-4 rounded-xl bg-amber-50 border border-amber-100 flex items-center gap-4 transition hover:shadow-md">
                <div
                    class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center text-white shadow-lg shadow-amber-200">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Pelanggaran</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $summary['total_violation'] }}</p>
                </div>
            </div>
        </div>

        {{-- TABEL TERLAMBAT --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-8">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-rose-600 text-lg">
                <i class="fa-solid fa-clock-rotate-left"></i> Daftar Siswa Terlambat
            </h2>

            <div class="overflow-x-auto">
                <table id="tableLate" class="w-full text-sm display cell-border">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Kelas</th>
                            <th class="py-3 px-4 text-left">Waktu Datang</th>
                            <th class="py-3 px-4 text-left">Terlambat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($latePermissions as $i => $item)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4">{{ $loop->iteration }}</td>
                                <td class="py-3 px-4 font-medium text-slate-900">{{ $item->student->name }}</td>
                                <td class="py-3 px-4">{{ $item->student->class->name }}</td>
                                <td class="py-3 px-4 text-rose-600 font-medium">
                                    {{ $item->checkin->checkin_at->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    @php
                                        $checkin = $item->checkin->checkin_at;
                                        $endAt = $item->end_at;
                                        $diff = $endAt->diff($checkin);
                                    @endphp

                                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700">

                                        @if($diff->d > 0) {{ $diff->d }} hari @endif
                                        @if($diff->h > 0) {{ $diff->h }} jam @endif
                                        @if($diff->i > 0) {{ $diff->i }} menit @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL PELANGGARAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-amber-600 text-lg">
                <i class="fa-solid fa-triangle-exclamation"></i> Daftar Pelanggaran
            </h2>

            <div class="overflow-x-auto">
                <table id="tableViolation" class="w-full text-sm display cell-border">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Kelas</th>
                            <th class="py-3 px-4 text-left">Jenis</th>
                            <th class="py-3 px-4 text-left">Deskripsi</th>
                            <th class="py-3 px-4 text-left">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($violations as $i => $v)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4">{{ $i + 1 }}</td>
                                <td class="py-3 px-4 font-medium text-slate-900">{{ $v->name }}</td>
                                <td class="py-3 px-4">{{ $v->class_name }}</td>
                                <td class="py-3 px-4">
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-700 uppercase border border-amber-200">
                                        {{ $v->handling_type }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $v->description ?? '-' }}</td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($v->occurred_at)->format('d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {

            const periodText = 'Periode {{ $startDate->format("d M Y") }} - {{ $endDate->format("d M Y") }}';

            function pdfCustomize(doc, widths) {

                doc.styles.title = {
                    alignment: 'center',
                    fontSize: 14,
                    bold: true,
                    margin: [0, 0, 0, 6]
                };

                doc.styles.message = {
                    alignment: 'center',
                    fontSize: 10,
                    margin: [0, 0, 0, 14]
                };

                const table = doc.content.find(c => c.table);
                table.table.widths = widths;

                doc.styles.tableHeader = {
                    fontSize: 9,
                    bold: true,
                    alignment: 'center',
                    fillColor: '#1f2937',
                    color: '#ffffff'
                };

                table.table.body.forEach((row, rowIndex) => {
                    if (rowIndex === 0) return;

                    row.forEach((cell, colIndex) => {
                        cell.fontSize = 9;

                        if (colIndex === 0) {
                            cell.alignment = 'center';
                            cell.noWrap = true;
                        }

                        if (colIndex === 1) {
                            cell.alignment = 'left';
                            cell.noWrap = true;
                        }

                        if (colIndex === 2) {
                            cell.alignment = 'center';
                            cell.noWrap = true;
                        }

                        if (colIndex === 3) {
                            cell.alignment = 'center';
                            cell.noWrap = false;
                        }

                        if (colIndex === 4) {
                            cell.alignment = 'left';
                            cell.noWrap = false;
                        }
                    });
                });

                table.layout = {
                    hLineWidth: () => 0.8,
                    vLineWidth: () => 0.8,
                    hLineColor: () => '#e5e7eb',
                    vLineColor: () => '#e5e7eb',
                    paddingLeft: () => 6,
                    paddingRight: () => 6,
                    paddingTop: () => 4,
                    paddingBottom: () => 4
                };
            }

            // ================= TABLE TERLAMBAT =================
            $('#tableLate').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Laporan Siswa Terlambat',
                        messageTop: periodText
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Laporan Siswa Terlambat',
                        messageTop: periodText,
                        orientation: 'portrait',
                        pageSize: 'A4',
                        customize: function (doc) {
                            pdfCustomize(doc, ['6%', '26%', '14%', '24%', '30%']);
                        }
                    }
                ]
            });

            // ================= TABLE PELANGGARAN =================
            $('#tableViolation').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Laporan Pelanggaran Siswa',
                        messageTop: periodText
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Laporan Pelanggaran Siswa',
                        messageTop: periodText,
                        orientation: 'portrait',
                        pageSize: 'A4',
                        customize: function (doc) {
                            pdfCustomize(doc, ['5%', '20%', '15%', '15%', '30%', '15%']);
                        }
                    }
                ],
                exportOptions: {
                    columns: ':visible'
                }
            });

        });
    </script>
@endpush