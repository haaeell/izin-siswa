@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
    <div class="mx-auto p-4 sm:p-6 bg-white rounded-xl">

    <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-4">
        <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">Manajemen User</h1>

        <button onclick="openCreateModal()"
            class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah User
        </button>
    </div>

    <div class="bg-white rounded-xl shadow border overflow-x-auto px-3 sm:px-4 py-4 sm:py-5">
        <table id="datatable" class="w-full text-sm whitespace-nowrap">
            <thead class="bg-slate-100 text-slate-700">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th class="hidden sm:table-cell">Email</th>
                    <th class="hidden sm:table-cell">Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $i => $user)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="font-medium">{{ $user->name }}</div>
                            <div class="text-xs text-slate-500 sm:hidden">
                                {{ $user->email }} · {{ ucfirst($user->role) }}
                            </div>
                        </td>
                        <td class="hidden sm:table-cell">{{ $user->email }}</td>
                        <td class="hidden sm:table-cell capitalize">{{ $user->role }}</td>
                        <td>
                            <div class="flex justify-center gap-2">
                                <button onclick='openEditModal(@json($user))'
                                    class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button onclick="deleteUser({{ $user->id }})"
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="userModal"
        class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50 p-3 sm:p-6">
        <div class="bg-white w-full max-w-md rounded-xl p-4 sm:p-6 shadow-lg">

            <h2 id="modalTitle" class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user text-blue-600"></i>
                <span>Tambah User</span>
            </h2>

            <form id="userForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div class="mb-3">
                    <label class="text-sm font-medium">Nama</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="name" id="userName" required
                            class="w-full pl-10 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-300">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-sm font-medium">Email</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="userEmail" required
                            class="w-full pl-10 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-300">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-sm font-medium">Role</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-user-shield"></i>
                        </span>
                        <select name="role" id="userRole"
                            class="w-full pl-10 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-300">
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="security">Security</option>
                            <option value="perizinan">Perizinan</option>
                            <option value="wali_kelas">Wali Kelas</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-sm font-medium">Password</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="password" id="userPassword"
                            class="w-full pl-10 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-300">
                    </div>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Kosongkan jika tidak ingin mengubah password
                    </span>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 mt-4">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border rounded-lg hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition">
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

</div>


    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#datatable').DataTable();

                const $modal = $('#userModal');
                const $form = $('#userForm');
                const $title = $('#modalTitle span');
                const $name = $('#userName');
                const $email = $('#userEmail');
                const $role = $('#userRole');
                const $password = $('#userPassword');
                const $method = $('#methodField');

                window.openCreateModal = function () {
                    $modal.removeClass('hidden');
                    $title.text('Tambah User');
                    $form.attr('action', '/master/users');
                    $method.val('');
                    $name.val('');
                    $email.val('');
                    $role.val('');
                    $password.val('');
                }

                window.openEditModal = function (data) {
                    $modal.removeClass('hidden');
                    $title.text('Edit User');
                    $form.attr('action', `/master/users/${data.id}`);
                    $method.val('PUT');
                    $name.val(data.name);
                    $email.val(data.email);
                    $role.val(data.role);
                    $password.val('');
                }

                window.closeModal = function () {
                    $modal.addClass('hidden');
                }
            });

            function deleteUser(id) {
                Swal.fire({
                    title: 'Yakin?',
                    text: 'User akan dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Ya, hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement("form");
                        form.method = "POST";
                        form.action = `/master/users/${id}`;
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
