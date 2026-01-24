@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl">

        {{-- HEADER --}}
        <div class="flex justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Data Siswa</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-blue-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Siswa</li>
                    </ol>
                </nav>
            </div>

            <div>
                @if (Auth::user()->role === 'perizinan')
                    <div class="flex gap-2">
                        <button onclick="openImportModal()"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
                            <i class="fa-solid fa-file-excel"></i>
                            Import Excel
                        </button>

                        <button onclick="openCreateModal()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            + Tambah
                        </button>
                    </div>
                @endif
            </div>

            {{-- MODAL IMPORT EXCEL --}}
            <div id="importModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-lg rounded-xl p-6">

                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i>
                        Import Data Siswa (Excel)
                    </h2>

                    {{-- STEP 1 --}}
                    <div class="mb-4 p-4 border rounded-lg bg-slate-50">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold">
                                1
                            </div>

                            <div>
                                <p class="font-semibold text-slate-700">Download Template Excel</p>
                                <p class="text-sm text-slate-500 mb-2">
                                    Gunakan template agar format sesuai sistem (NIS, Nama, Kelas).
                                </p>

                                <a href="/master/students/template"
                                    class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">
                                    <i class="fa-solid fa-download"></i>
                                    Download Template
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2 --}}
                    <div class="mb-4 p-4 border rounded-lg bg-slate-50">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                2
                            </div>

                            <div class="w-full">
                                <p class="font-semibold text-slate-700">Upload File Excel</p>
                                <p class="text-sm text-slate-500 mb-3">
                                    Upload file <b>.xlsx</b> sesuai template yang sudah diunduh.
                                </p>

                                <form id="importForm" action="/master/students/import" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    {{-- CUSTOM FILE INPUT --}}
                                    <label for="fileInput"
                                        class="group flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-xl cursor-pointer
                                                                               bg-white hover:bg-blue-50 border-slate-300 hover:border-blue-500 transition">

                                        <div class="flex flex-col items-center justify-center text-center">
                                            <i class="fa-solid fa-file-excel text-4xl text-green-600 mb-2"></i>

                                            <p class="text-sm text-slate-600">
                                                Klik untuk memilih file atau
                                                <span class="text-blue-600 font-semibold">drag & drop</span>
                                            </p>

                                            <p class="text-xs text-slate-400 mt-1">
                                                Format .xlsx • Maks 2MB
                                            </p>

                                            <p id="fileName" class="mt-2 text-sm font-medium text-slate-700 hidden">
                                            </p>
                                        </div>

                                        <input id="fileInput" type="file" name="file" accept=".xlsx" required
                                            class="hidden">
                                    </label>

                                    {{-- SUBMIT --}}
                                    <button type="submit" id="importBtn"
                                        class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition">
                                        <span id="importText">Import Data</span>
                                        <svg id="importLoader" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                                opacity="0.3" />
                                            <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="flex justify-end">
                        <button onclick="closeImportModal()" class="px-4 py-2 border rounded-lg">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>

        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            @if (session('import_error'))
                <div class="mb-4 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                    <i class="fa-solid fa-circle-xmark mt-1"></i>
                    <div>
                        <p class="font-semibold">Gagal Import</p>
                        <p class="text-sm">{{ session('import_error') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                    <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                    <div>
                        <p class="font-semibold">Validasi Gagal</p>
                        <ul class="text-sm list-disc ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        @if (Auth::user()->role === 'perizinan')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $student)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $student->nis }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->class->name ?? '-' }}</td>
                            @if (Auth::user()->role === 'perizinan')
                                <td class="text-center space-x-2">
                                    <button onclick='openEditModal(@json($student))'
                                        class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <button onclick="deleteStudent({{ $student->id }})"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL --}}
    <div id="studentModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl p-6">
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">
                Tambah Siswa
            </h2>

            <form id="studentForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div class="mb-3">
                    <label class="text-sm font-medium">NIS</label>
                    <input type="text" name="nis" id="nis" required class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="mb-3">
                    <label class="text-sm font-medium">Nama</label>
                    <input type="text" name="name" id="name" required class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium">Kelas</label>
                    <select name="class_id" id="class_id" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">- Pilih Kelas -</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">
                        Batal
                    </button>

                    <button type="submit" id="submitBtn"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center gap-2">
                        <span id="btnText">Simpan</span>
                        <svg id="loader" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24">
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

                $('#datatable').DataTable();

                const $modal = $('#studentModal');
                const $form = $('#studentForm');
                const $title = $('#modalTitle');

                const $nis = $('#nis');
                const $name = $('#name');
                const $class = $('#class_id');
                const $method = $('#methodField');

                const $btn = $('#submitBtn');
                const $btnText = $('#btnText');
                const $loader = $('#loader');

                window.openCreateModal = function () {
                    $modal.removeClass('hidden');
                    $title.text('Tambah Siswa');

                    $form.attr('action', '/master/students');
                    $method.val('');
                    $nis.val('');
                    $name.val('');
                    $class.val('');
                }

                window.openEditModal = function (data) {
                    $modal.removeClass('hidden');
                    $title.text('Edit Siswa');

                    $form.attr('action', `/master/students/${data.id}`);
                    $method.val('PUT');
                    $nis.val(data.nis);
                    $name.val(data.name);
                    $class.val(data.class_id);
                }

                window.closeModal = function () {
                    $modal.addClass('hidden');
                }

                $form.on('submit', function () {
                    $btn.prop('disabled', true).addClass('opacity-70');
                    $btnText.text('Menyimpan...');
                    $loader.removeClass('hidden');
                });

                window.deleteStudent = function (id) {
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Data siswa akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/master/students/${id}`;
                            form.innerHTML = `
                                                                                                                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                                                                                                    <input type="hidden" name="_method" value="DELETE">
                                                                                                                                                `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }

                window.openImportModal = function () {
                    $('#importModal').removeClass('hidden');
                }

                window.closeImportModal = function () {
                    $('#importModal').addClass('hidden');
                }

                $('#importForm').on('submit', function () {
                    $('#importBtn').prop('disabled', true).addClass('opacity-70');
                    $('#importText').text('Mengimpor...');
                    $('#importLoader').removeClass('hidden');
                });
                $('#fileInput').on('change', function () {
                    const file = this.files[0];

                    if (file) {
                        $('#fileName')
                            .text(file.name)
                            .removeClass('hidden');
                    }
                });

                $('#importForm').on('submit', function () {
                    $('#importBtn')
                        .prop('disabled', true)
                        .addClass('opacity-70 cursor-not-allowed');

                    $('#importText').text('Mengimpor...');
                    $('#importLoader').removeClass('hidden');
                });

            });
        </script>
    @endpush
@endsection