@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6 space-y-6">

        {{-- HEADER --}}
        <div>
            <h1 class="text-2xl font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-gauge-high text-blue-600"></i>
                Dashboard
            </h1>
            <p class="text-sm text-slate-500">Ringkasan aktivitas sistem perizinan siswa</p>
        </div>

        {{-- STAT CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-700">Total Siswa</p>
                    <p class="text-2xl font-bold text-blue-800">{{ $totalStudents }}</p>
                </div>
            </div>

            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xl">
                    <i class="fa-solid fa-school"></i>
                </div>
                <div>
                    <p class="text-sm text-indigo-700">Total Kelas</p>
                    <p class="text-2xl font-bold text-indigo-800">{{ $totalClasses }}</p>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-yellow-500 text-white flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-yellow-700">Izin Pending</p>
                    <p class="text-2xl font-bold text-yellow-800">{{ $pendingCount }}</p>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-green-600 text-white flex items-center justify-center text-xl">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div>
                    <p class="text-sm text-green-700">Izin Hari Ini</p>
                    <p class="text-2xl font-bold text-green-800">{{ $todayCount }}</p>
                </div>
            </div>

        </div>

        {{-- WALI KELAS --}}
        @if(auth()->user()->role === 'wali_kelas')
            <div class="bg-white rounded-xl border border-slate-200">
                <div class="px-6 py-4 border-b flex items-center gap-2">
                    <i class="fa-solid fa-file-signature text-blue-600"></i>
                    <h2 class="font-semibold text-slate-800">Pengajuan Izin Terakhir</h2>
                </div>

                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="text-left p-2">Siswa</th>
                                <th>Jenis</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myPermissions as $row)
                                <tr class="border-b">
                                    <td class="p-2 font-medium">{{ $row->student->name ?? '-' }}</td>
                                    <td class="text-center">{{ ucfirst($row->type) }}</td>
                                    <td class="text-center">
                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                                    {{ $row->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                    {{ $row->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                                    {{ $row->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                            {{ ucfirst($row->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-slate-500 py-4">
                                        <i class="fa-regular fa-folder-open mr-1"></i>
                                        Belum ada pengajuan izin
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- PERIZINAN --}}
        @if(auth()->user()->role === 'perizinan')
            <div class="bg-white rounded-xl border border-slate-200">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                        <h2 class="font-semibold text-slate-800">Menunggu Persetujuan</h2>
                    </div>
                    <span class="text-sm text-slate-500 flex items-center gap-1">
                        <i class="fa-solid fa-qrcode"></i>
                        Check-in hari ini: <b>{{ $todayCheckins }}</b>
                    </span>
                </div>

                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="text-left p-2">Siswa</th>
                                <th>Wali Kelas</th>
                                <th>Jenis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPermissions as $row)
                                <tr class="border-b">
                                    <td class="p-2 font-medium">{{ $row->student->name ?? '-' }}</td>
                                    <td class="text-center">{{ $row->waliKelas->name ?? '-' }}</td>
                                    <td class="text-center">{{ ucfirst($row->type) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-slate-500 py-4">
                                        <i class="fa-solid fa-check-circle text-green-500 mr-1"></i>
                                        Tidak ada izin pending
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
@endsection