@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto p-6">

        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Manajemen Kelas</h1>
            <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Tambah Kelas
            </button>
        </div>

        <div class="bg-white rounded-xl shadow border">
            <table class="w-full text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="p-3 text-left">#</th>
                        <th class="p-3 text-left">Nama Kelas</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $i => $class)
                        <tr class="border-t">
                            <td class="p-3">{{ $i + 1 }}</td>
                            <td class="p-3">{{ $class->name }}</td>
                            <td class="p-3 text-right space-x-2">
                                <button onclick="openEditModal({{ $class }})"
                                    class="px-3 py-1 bg-yellow-400 rounded">Edit</button>

                                <button onclick="deleteClass({{ $class->id }})" class="px-3 py-1 bg-red-500 text-white rounded">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="classModal" class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl p-6">
            <h2 id="modalTitle" class="text-lg font-semibold mb-4">Tambah Kelas</h2>

            <form id="classForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div>
                    <label class="text-sm">Nama Kelas</label>
                    <input type="text" name="name" id="className" required class="w-full mt-1 px-3 py-2 border rounded-lg">
                </div>

                <div class="flex justify-end gap-2 mt-4">
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
            const modal = document.getElementById("classModal");
            const form = document.getElementById("classForm");
            const title = document.getElementById("modalTitle");
            const nameInput = document.getElementById("className");
            const methodField = document.getElementById("methodField");

            const btn = document.getElementById("submitBtn");
            const btnText = document.getElementById("btnText");
            const loader = document.getElementById("loader");

            function openCreateModal() {
                modal.classList.remove("hidden");
                title.innerText = "Tambah Kelas";
                form.action = "/master/classes";
                methodField.value = "";
                nameInput.value = "";
            }

            function openEditModal(data) {
                modal.classList.remove("hidden");
                title.innerText = "Edit Kelas";
                form.action = `/master/classes/${data.id}`;
                methodField.value = "PUT";
                nameInput.value = data.name;
            }

            function closeModal() {
                modal.classList.add("hidden");
            }

            form.addEventListener("submit", () => {
                btn.disabled = true;
                btn.classList.add("opacity-70", "cursor-not-allowed");
                btnText.innerText = "Menyimpan...";
                loader.classList.remove("hidden");
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