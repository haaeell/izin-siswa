@extends('layouts.app')

@section('title', 'Kelas')

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl">

        {{-- HEADER --}}
        <div class="flex justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Kelas</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-blue-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Kelas</li>
                    </ol>
                </nav>
            </div>

            <div>
                <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    + Tambah
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>Nama Kelas</th>
                        <th>Wali Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($classes as $i => $class)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->waliKelas->name }}</td>
                            <td class="text-center space-x-2">
                                <button onclick='openEditModal(@json($class))'
                                    class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button onclick="deleteClass({{ $class->id }})" class="px-3 py-1 bg-red-500 text-white rounded">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL --}}
    <div id="classModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl p-6">
            <h2 id="modalTitle" class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-school"></i>
                <span>Tambah Kelas</span>
            </h2>

            <form id="classForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                {{-- NAMA KELAS --}}
                <div class="mb-3">
                    <label class="text-sm font-medium">Nama Kelas</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-door-open"></i>
                        </span>
                        <input type="text" name="name" id="className" required
                            class="w-full pl-10 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                    </div>
                </div>

                {{-- WALI KELAS --}}
                <div class="mb-4">
                    <label class="text-sm font-medium">Wali Kelas</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>
                        <select name="wali_kelas_id" id="waliKelas"
                            class="w-full pl-10 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach ($waliKelas as $wk)
                                <option value="{{ $wk->id }}">{{ $wk->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- BUTTON --}}
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center gap-2">
                        <span id="btnText">Simpan</span>
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

                $('#datatable').DataTable();

                const $modal = $('#classModal');
                const $form = $('#classForm');
                const $title = $('#modalTitle span');

                const $name = $('#className');
                const $wali = $('#waliKelas');
                const $method = $('#methodField');

                const $btn = $('#submitBtn');
                const $btnText = $('#btnText');
                const $loader = $('#loader');

                window.openCreateModal = function () {
                    $modal.removeClass('hidden');
                    $title.text('Tambah Kelas');

                    $form.attr('action', '/master/classes');
                    $method.val('');

                    $name.val('');
                    $wali.val('');
                }

                window.openEditModal = function (data) {
                    $modal.removeClass('hidden');
                    $title.text('Edit Kelas');

                    $form.attr('action', `/master/classes/${data.id}`);
                    $method.val('PUT');

                    $name.val(data.name);
                    $wali.val(data.wali_kelas_id);
                }

                window.closeModal = function () {
                    $modal.addClass('hidden');
                }

                $form.on('submit', function () {
                    $btn.prop('disabled', true)
                        .addClass('opacity-70 cursor-not-allowed');

                    $btnText.text('Menyimpan...');
                    $loader.removeClass('hidden');
                });

            });

            function deleteClass(id) {
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Data kelas akan dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement("form");
                        form.method = "POST";
                        form.action = `/master/classes/${id}`;

                        form.innerHTML = `
                                                                                                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                                                                                                        <input type="hidden" name="_method" value="DELETE">
                                                                                                    `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush


@endsection