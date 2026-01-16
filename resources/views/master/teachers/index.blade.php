@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl">

        {{-- HEADER --}}
        <div class="flex justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Data Guru</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-blue-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Guru</li>
                    </ol>
                </nav>
            </div>

            <div>
                <button onclick="openCreateModal()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    + Tambah Guru
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>Nama Guru</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $i => $teacher)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td class="text-center space-x-2">
                                <button onclick='openEditModal(@json($teacher))'
                                    class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button onclick="deleteTeacher({{ $teacher->id }})"
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
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
    <div id="teacherModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl p-6">
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">
                Tambah Guru
            </h2>

            <form id="teacherForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div class="mb-3">
                    <label class="text-sm font-medium">Nama Guru</label>
                    <input type="text" name="name" id="name" required class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="mb-3">
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" id="email" required class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium">Password</label>
                    <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg"
                        placeholder="Kosongkan saat edit">
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

                const $modal = $('#teacherModal');
                const $form = $('#teacherForm');
                const $title = $('#modalTitle');

                const $name = $('#name');
                const $email = $('#email');
                const $pass = $('#password');
                const $method = $('#methodField');

                const $btn = $('#submitBtn');
                const $btnText = $('#btnText');
                const $loader = $('#loader');

                window.openCreateModal = function () {
                    $modal.removeClass('hidden');
                    $title.text('Tambah Guru');

                    $form.attr('action', '/master/teachers');
                    $method.val('');
                    $name.val('');
                    $email.val('');
                    $pass.val('');
                }

                window.openEditModal = function (data) {
                    $modal.removeClass('hidden');
                    $title.text('Edit Guru');

                    $form.attr('action', `/master/teachers/${data.id}`);
                    $method.val('PUT');
                    $name.val(data.name);
                    $email.val(data.email);
                    $pass.val('');
                }

                window.closeModal = function () {
                    $modal.addClass('hidden');
                }

                $form.on('submit', function () {
                    $btn.prop('disabled', true).addClass('opacity-70');
                    $btnText.text('Menyimpan...');
                    $loader.removeClass('hidden');
                });

                window.deleteTeacher = function (id) {
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Data guru akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/master/teachers/${id}`;
                            form.innerHTML = `
                                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                    <input type="hidden" name="_method" value="DELETE">
                                                                `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }

            });
        </script>
    @endpush
@endsection