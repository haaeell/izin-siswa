@extends('layouts.app')

@section('title', 'Tahun Akademik')

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl">
        <div class="flex justify-between mb-4">

            <div class="">
                <h1 class="text-2xl font-semibold text-slate-800">Tahun Akademik</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="/home" class="hover:text-blue-600">Dashboard</a>
                        </li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Tahun Akademik</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button onclick="openCreateModal()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    + Tambah
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>#</th>
                        <th>Tahun Akademik</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($years as $i => $year)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $year->name }}</td>
                            <td>
                                @if($year->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center space-x-2">
                                <button onclick='openEditModal(@json($year))'
                                    class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500 transition">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button onclick="deleteYear({{ $year->id }})"
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="yearModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl p-6">
            <h2 id="modalTitle" class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-calendar"></i>
                Tambah Tahun Akademik
            </h2>

            <form id="yearForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div class="mb-3">
                    <label class="text-sm font-medium">Nama Tahun</label>

                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa-solid fa-calendar-days"></i>
                        </span>

                        <input type="text" name="name" id="yearName" required
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
                    </div>
                </div>


                <div class="mb-4">
                    <label class="flex items-center gap-3 text-sm cursor-pointer">
                        <span class="text-green-500">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>

                        <input type="checkbox" name="is_active" id="yearActive"
                            class="rounded text-blue-600 focus:ring-blue-500">

                        <span>Jadikan aktif</span>
                    </label>
                </div>

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

                const $modal = $('#yearModal');
                const $form = $('#yearForm');
                const $title = $('#modalTitle');

                const $name = $('#yearName');
                const $active = $('#yearActive');
                const $method = $('#methodField');

                const $btn = $('#submitBtn');
                const $btnText = $('#btnText');
                const $loader = $('#loader');

                window.openCreateModal = function () {
                    $modal.removeClass('hidden');
                    $title.text('Tambah Tahun Akademik');

                    $form.attr('action', '/master/academic-years');
                    $method.val('');
                    $name.val('');
                    $active.prop('checked', false);
                }

                window.openEditModal = function (data) {
                    $modal.removeClass('hidden');
                    $title.text('Edit Tahun Akademik');

                    $form.attr('action', `/master/academic-years/${data.id}`);
                    $method.val('PUT');
                    $name.val(data.name);
                    $active.prop('checked', data.is_active);
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

                window.deleteYear = function (id) {
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Data Tahun Akademik akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement("form");
                            form.method = "POST";
                            form.action = `/master/academic-years/${id}`;

                            form.innerHTML = `
                                                                                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
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