@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6 space-y-6">

        {{-- HEADER --}}
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Dashboard</h1>
            <p class="text-sm text-slate-500">Ringkasan aktivitas sistem perizinan</p>
        </div>

        {{-- STAT CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border p-5">
                <p class="text-sm text-slate-500">Total Siswa</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalStudents }}</p>
            </div>

            <div class="bg-white rounded-xl border p-5">
                <p class="text-sm text-slate-500">Total Kelas</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalClasses }}</p>
            </div>

            <div class="bg-white rounded-xl border p-5">
                <p class="text-sm text-slate-500">Izin Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
            </div>

            <div class="bg-white rounded-xl border p-5">
                <p class="text-sm text-slate-500">Izin Hari Ini</p>
                <p class="text-2xl font-bold text-blue-600">{{ $todayCount }}</p>
            </div>
        </div>

        {{-- WALI KELAS --}}
        @if(auth()->user()->role === 'wali_kelas')
            <div class="bg-white rounded-xl border">
                <div class="px-6 py-4 border-b">
                    <h2 class="font-semibold text-slate-800">Pengajuan Izin Terakhir</h2>
                </div>

                <div class="p-6">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="text-left p-2">Siswa</th>
                                <th>Jenis</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myPermissions as $row)
                                <tr class="border-b">
                                    <td class="p-2">{{ $row->student->name ?? '-' }}</td>
                                    <td class="text-center">{{ ucfirst($row->type) }}</td>
                                    <td class="text-center">
                                        <span class="px-2 py-1 rounded text-xs
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
            <div class="bg-white rounded-xl border">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h2 class="font-semibold text-slate-800">Menunggu Persetujuan</h2>
                    <span class="text-sm text-slate-500">
                        Check-in hari ini: <b>{{ $todayCheckins }}</b>
                    </span>
                </div>

                <div class="p-6">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="text-left p-2">Siswa</th>
                                <th>Wali Kelas</th>
                                <th>Jenis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPermissions as $row)
                                <tr class="border-b">
                                    <td class="p-2">{{ $row->student->name ?? '-' }}</td>
                                    <td class="text-center">{{ $row->waliKelas->name ?? '-' }}</td>
                                    <td class="text-center">{{ ucfirst($row->type) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-slate-500 py-4">
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