@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
    <div class="mx-auto p-4 sm:p-6 bg-white rounded-xl">
        <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">Data Siswa</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2 flex-wrap">
                        <li><a href="/home" class="hover:text-blue-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Siswa</li>
                    </ol>
                </nav>
            </div>

            @if (Auth::user()->role === 'perizinan')
                <div class="flex flex-wrap gap-3 mb-4 items-end">
                    {{-- Filter Kelas --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Filter Kelas</label>
                        <select id="filterKelas"
                            class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[160px]">
                            <option value="">Semua Kelas</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected($filterClass == $class->id)>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-2">
                <button id="btnFilterPulang" data-active="{{ $filterPulang ? 'true' : 'false' }}"
                    class="w-full sm:w-auto px-4 py-2 rounded-lg transition flex items-center justify-center gap-2
                                    {{ $filterPulang ? 'bg-orange-600 text-white hover:bg-orange-700' : 'bg-orange-100 text-orange-700 hover:bg-orange-200' }}">
                    <i class="fa-solid fa-person-walking-arrow-right"></i>
                    Sedang Pulang
                    <span id="pulangBadge" class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full
                                    {{ $filterPulang ? 'bg-white text-orange-600' : 'bg-orange-600 text-white' }}">
                        {{ $sedangPulangCount }}
                    </span>
                </button>

                @if (Auth::user()->role === 'perizinan')
                    <button onclick="openImportModal()"
                        class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-excel"></i>
                        Import Excel
                    </button>

                    <button onclick="openCreateModal()"
                        class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        + Tambah
                    </button>
                @endif
            </div>
        </div>

        <div id="importModal" class="fixed inset-0 hidden bg-black/50 flex items-center justify-center z-50 p-3 sm:p-4">
            <div class="bg-white w-full max-w-xl md:max-w-2xl rounded-xl p-4 sm:p-6 overflow-y-auto max-h-[90vh]">
                <h2 class="text-lg font-semibold mb-6 flex items-center gap-2 flex-wrap">
                    <i class="fa-solid fa-file-excel text-emerald-600"></i>
                    Import Data Siswa (Excel)
                </h2>

                <div class="mb-4 p-4 border rounded-lg bg-slate-50 flex flex-col md:flex-row gap-4 items-start">
                    <div
                        class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold flex-shrink-0">
                        1</div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-700">Download Template Excel</p>
                        <p class="text-sm text-slate-500 mb-2">Gunakan template agar format sesuai sistem.</p>
                        <a href="/master/students/template"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">
                            <i class="fa-solid fa-download"></i> Download Template
                        </a>
                    </div>
                </div>

                <div class="mb-4 p-4 border rounded-lg bg-slate-50 flex flex-col md:flex-row gap-4 items-start">
                    <div
                        class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold flex-shrink-0">
                        2</div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-700">Upload File Excel</p>
                        <p class="text-sm text-slate-500 mb-3">Upload file <b>.xlsx</b> sesuai template.</p>
                        <form action="/master/students/import" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label id="dropzone"
                                class="group flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-xl cursor-pointer bg-white hover:bg-blue-50 border-slate-300 hover:border-blue-500 transition">
                                <div class="text-center px-2" id="dropzoneDefault">
                                    <i class="fa-solid fa-file-excel text-4xl text-green-600 mb-2"></i>
                                    <p class="text-sm text-slate-600">Klik atau <span
                                            class="text-blue-600 font-semibold">drag & drop</span></p>
                                    <p class="text-xs text-slate-400 mt-1">Format .xlsx • Maks 2MB</p>
                                </div>
                                <div class="text-center px-2 hidden" id="dropzoneSelected">
                                    <i class="fa-solid fa-file-circle-check text-4xl text-emerald-500 mb-2"></i>
                                    <p class="text-sm font-semibold text-emerald-700" id="selectedFileName">-</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik untuk ganti file</p>
                                </div>
                                <input type="file" name="file" id="importFileInput" accept=".xlsx" required class="hidden">
                            </label>
                            <button
                                class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Import Data
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button onclick="closeImportModal()"
                        class="px-4 py-2 border rounded-lg hover:bg-slate-100 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border overflow-x-auto px-3 sm:px-4 py-4 sm:py-5">
            <table id="datatable" class="w-full text-sm whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Barak</th>
                        @if (Auth::user()->role === 'perizinan')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="studentModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl p-6">
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">Tambah Siswa</h2>
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
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="text-sm font-medium">Asrama</label>
                    <select name="dormitory_id" id="dormitory_id" class="w-full px-3 py-2 border rounded-lg select2">
                        <option value="">- Pilih Asrama -</option>
                        @foreach ($dormitories as $dorm)
                            <option value="{{ $dorm->id }}">{{ $dorm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Batal</button>
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

                let filterPulang = {{ $filterPulang ? 'true' : 'false' }};
                let filterKelas = '{{ $filterClass ?? '' }}';

                const isTherePerizinan = {{ Auth::user()->role === 'perizinan' ? 'true' : 'false' }};

                const columns = [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nis', name: 'nis' },
                    { data: 'name', name: 'name' },
                    { data: 'class_name', name: 'class.name' },
                    { data: 'dormitory_name', name: 'dormitory.name' },
                ];

                if (isTherePerizinan) {
                    columns.push({ data: 'aksi', name: 'aksi', orderable: false, searchable: false });
                }

                const table = $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('master.students.data') }}',
                        data: function (d) {
                            d.filter = filterPulang ? 'pulang' : '';
                            d.class_id = filterKelas;
                        }
                    },
                    columns: columns,
                    order: [[2, 'asc']],
                    pageLength: 10,
                });

                $('#filterKelas').on('change', function () {
                    filterKelas = $(this).val();
                    table.ajax.reload();
                }).val(filterKelas);

                $('#btnFilterPulang').on('click', function () {
                    filterPulang = !filterPulang;

                    if (filterPulang) {
                        $(this)
                            .removeClass('bg-orange-100 text-orange-700 hover:bg-orange-200')
                            .addClass('bg-orange-600 text-white hover:bg-orange-700');
                        $('#pulangBadge')
                            .removeClass('bg-orange-600 text-white')
                            .addClass('bg-white text-orange-600');
                    } else {
                        $(this)
                            .removeClass('bg-orange-600 text-white hover:bg-orange-700')
                            .addClass('bg-orange-100 text-orange-700 hover:bg-orange-200');
                        $('#pulangBadge')
                            .removeClass('bg-white text-orange-600')
                            .addClass('bg-orange-600 text-white');
                    }

                    table.ajax.reload();
                });

                $('#importFileInput').on('change', function () {
                    const file = this.files[0];
                    if (file) {
                        $('#selectedFileName').text(file.name);
                        $('#dropzoneDefault').addClass('hidden');
                        $('#dropzoneSelected').removeClass('hidden');
                        $('#dropzone').removeClass('border-slate-300').addClass('border-emerald-400 bg-emerald-50');
                    }
                });

                $('.select2').select2({ width: '100%' });

                const $modal = $('#studentModal');
                const $form = $('#studentForm');
                const $title = $('#modalTitle');
                const $method = $('#methodField');
                const $btn = $('#submitBtn');
                const $btnText = $('#btnText');
                const $loader = $('#loader');

                window.openCreateModal = function () {
                    $modal.removeClass('hidden');
                    $title.text('Tambah Siswa');
                    $form.attr('action', '/master/students');
                    $method.val('');
                    $('#nis').val('');
                    $('#name').val('');
                    $('#class_id').val('');
                    $('#dormitory_id').val('').trigger('change');
                };

                window.openEditModal = function (data) {
                    $modal.removeClass('hidden');
                    $title.text('Edit Siswa');
                    $form.attr('action', `/master/students/${data.id}`);
                    $method.val('PUT');
                    $('#nis').val(data.nis);
                    $('#name').val(data.name);
                    $('#class_id').val(data.class_id);
                    $('#dormitory_id').val(data.dormitory_id).trigger('change');
                };

                window.closeModal = function () {
                    $modal.addClass('hidden');
                };

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
                };

                window.openImportModal = () => $('#importModal').removeClass('hidden');
                window.closeImportModal = () => $('#importModal').addClass('hidden');
            });
        </script>
    @endpush
@endsection