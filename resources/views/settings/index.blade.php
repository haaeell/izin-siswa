@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="mx-auto p-4 sm:p-6 bg-white rounded-xl">

        <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">Pengaturan</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2 flex-wrap">
                        <li><a href="/home" class="hover:text-blue-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Pengaturan</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded-lg flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow border px-4 sm:px-6 py-5">

            <form id="settingForm" method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')

                {{-- SECTION: Pengaturan Izin --}}
                <div class="mb-6">
                    <h2 class="text-base font-semibold text-slate-700 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-file-circle-check text-blue-500"></i>
                        Pengaturan Izin
                    </h2>
                    <p class="text-sm text-slate-400 mb-4">Konfigurasi aturan pengajuan izin siswa.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Batas Maksimal Izin Aktif --}}
                        <div>
                            <label class="text-sm font-medium text-slate-700">
                                Batas Maksimal Izin Aktif
                            </label>
                            <p class="text-xs text-slate-400 mb-1">
                                Jumlah maksimal izin aktif yang bisa diajukan oleh wali kelas.
                            </p>
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-solid fa-hashtag"></i>
                                </span>
                                <input type="number" name="max_active_permissions"
                                    value="{{ old('max_active_permissions', $settings['max_active_permissions']->value ?? 3) }}"
                                    min="1" max="100" required class="w-full pl-10 py-2 border rounded-lg focus:ring focus:ring-blue-200
                                            @error('max_active_permissions') border-red-400 @enderror">
                            </div>
                            @error('max_active_permissions')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tambah setting lain di sini jika perlu --}}

                    </div>
                </div>

                <hr class="my-6 border-slate-100">

                {{-- BUTTON --}}
                <div class="flex justify-end">
                    <button type="submit" id="submitBtn"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span id="btnText">Simpan Pengaturan</span>
                        <svg id="loader" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.3" />
                            <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                        </svg>
                    </button>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#settingForm').on('submit', function () {
                    $('#submitBtn').prop('disabled', true).addClass('opacity-70 cursor-not-allowed');
                    $('#btnText').text('Menyimpan...');
                    $('#loader').removeClass('hidden');
                });
            });
        </script>
    @endpush

@endsection