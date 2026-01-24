@extends('layouts.app')

@section('title', 'Pelanggaran Siswa')

@section('content')
    <div class="mx-auto p-6 bg-white rounded-xl">

        {{-- HEADER --}}
        <div class="flex justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold">Pelanggaran Siswa</h1>
                <p class="text-sm text-slate-500">Manajemen pelanggaran dan hukuman</p>
            </div>

            <div>
                <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    + Tambah Pelanggaran
                </button>
            </div>
        </div>

        {{-- FILTER KELAS --}}
        @if (Auth::user()->role === 'perizinan')

            <form method="GET" class="mb-4">
                <div class="flex items-center gap-3">
                    <select name="class_id" id="filter_class" class="w-64">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>

                    @if(request('class_id'))
                        <a href="{{ url()->current() }}" class="px-4 py-2 border rounded-lg hover:bg-slate-100">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        @endif

        {{-- TABLE --}}
        <div class="bg-white shadow rounded-xl p-4">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th>#</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Tipe</th>
                        <th>Kejadian</th>
                        <th>Keterangan</th>
                        <th>Hukuman</th>
                        <th>Sampai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($violations as $i => $v)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $v->student->name }}</td>
                            <td>{{ $v->student->class->name }}</td>
                            <td>
                                <span
                                    class="px-2 py-1 rounded text-white
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $v->type == 'ringan' ? 'bg-green-500' : ($v->type == 'sedang' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                    {{ ucfirst($v->type) }}
                                </span>
                            </td>
                            <td>{{ $v->occurred_at }}</td>
                            <td>{{ $v->description }}</td>
                            <td>
                                {{ $v->no_phone ? '🚫 HP ' : '' }}
                                {{ $v->no_permission ? '🚫 Izin' : '' }}
                            </td>

                            <td>{{ $v->until }}</td>
                            <td>
                                <button onclick='openEditModal(@json($v))' class="px-2 py-1 bg-yellow-400 rounded">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button onclick="deleteData({{ $v->id }})" class="px-2 py-1 bg-red-500 text-white rounded">
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
    <div id="modal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-xl max-h-[90vh] overflow-hidden animate-scaleIn">
            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b bg-white">
                <div>
                    <h2 id="modalTitle" class="text-lg font-semibold text-slate-800">
                        Tambah Pelanggaran
                    </h2>
                    <p class="text-sm text-slate-500">
                        Catat pelanggaran siswa dengan benar
                    </p>
                </div>

                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition text-xl">
                    ✕
                </button>
            </div>

            {{-- BODY --}}
            <form id="form" method="POST" class="px-6 py-5 space-y-4 overflow-y-auto max-h-[65vh]">
                @csrf
                <input type="hidden" name="_method" id="method">

                {{-- SISWA --}}
                <div>
                    <label class="text-sm font-medium text-slate-700">Siswa</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="student_id"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg select2
                                                                                                                                                                           focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- JENIS --}}
                <div>
                    <label class="text-sm font-medium text-slate-700">Jenis Pelanggaran</label>
                    <div class="relative mt-1">
                        <i
                            class="fa-solid fa-triangle-exclamation absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="type"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none select2">
                            <option value="ringan">Ringan</option>
                            <option value="sedang">Sedang</option>
                            <option value="berat">Berat</option>
                        </select>
                    </div>
                </div>

                {{-- TANGGAL KEJADIAN --}}
                <div>
                    <label class="text-sm font-medium text-slate-700">Tanggal Kejadian</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-calendar-days absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="occurred_at" id="occurred_at" required
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                {{-- DESKRIPSI --}}
                <div>
                    <label class="text-sm font-medium text-slate-700">Deskripsi</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-align-left absolute left-3 top-3 text-slate-400"></i>
                        <textarea name="description" rows="3" id="description"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Contoh: Membawa HP saat jam pelajaran"></textarea>
                    </div>
                </div>

                {{-- SANKSI --}}
                <div>
                    <label class="text-sm font-medium text-slate-700 mb-2 block">Sanksi</label>

                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex items-start gap-3 border rounded-xl p-4 cursor-pointer
                                                                                                                                                                           hover:border-blue-500 hover:bg-blue-50 transition">
                            <input type="checkbox" name="no_phone"
                                class="mt-1 w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <div>
                                <p class="text-sm font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-mobile-screen-button"></i>
                                    Larangan HP
                                </p>
                                <p class="text-xs text-slate-500">
                                    Tidak boleh mengambil HP
                                </p>
                            </div>
                        </label>

                        <label
                            class="flex items-start gap-3 border rounded-xl p-4 cursor-pointer
                                                                                                                                                                           hover:border-red-500 hover:bg-red-50 transition">
                            <input type="checkbox" name="no_permission"
                                class="mt-1 w-4 h-4 text-red-600 rounded focus:ring-red-500">
                            <div>
                                <p class="text-sm font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-ban"></i>
                                    Larangan Izin
                                </p>
                                <p class="text-xs text-slate-500">
                                    Tidak boleh izin pulang
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- BERLAKU SAMPAI --}}
                <div>
                    <label class="text-sm font-medium text-slate-700">Berlaku Sampai</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="until" id="until"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:bg-slate-100"
                            disabled>
                    </div>
                </div>



                <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 rounded-lg border hover:bg-slate-100 transition">
                        Batal
                    </button>

                    <button type="submit" id="btnSubmit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <span id="btnText">Simpan</span>

                        {{-- spinner --}}
                        <svg id="btnLoading" class="hidden w-4 h-4 animate-spin text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
        <script>
            $(function () {

                $('.select2').select2({
                    width: '100%',
                    allowClear: true
                });
                const untilInput = document.getElementById('until');
                const checks = document.querySelectorAll(
                    'input[name="no_phone"], input[name="no_permission"]'
                );

                $('#filter_class').select2({
                    placeholder: 'Pilih Kelas',
                    allowClear: true,
                });


                checks.forEach(c => {
                    c.addEventListener('change', () => {
                        untilInput.disabled = ![...checks].some(x => x.checked);
                    });
                });

                $('#datatable').DataTable();

                window.openCreateModal = () => {
                    $('#modal').removeClass('hidden');
                    $('#form').attr('action', '/violations');
                    $('#method').val('');
                    $('#occurred_at').val('');
                    $('#form')[0].reset();
                }

                window.openEditModal = (d) => {
                    $('#modal').removeClass('hidden');
                    $('#form').attr('action', `/violations/${d.id}`);
                    $('#method').val('PUT');
                    $('#student_id').val(d.student_id);
                    $('#type').val(d.type);
                    $('#description').val(d.description);
                    $('#occurred_at').val(d.occurred_at);
                    $('#until').val(d.until);
                    $('input[name=no_phone]').prop('checked', d.no_phone);
                    $('input[name=no_permission]').prop('checked', d.no_permission);
                }

                window.closeModal = () => $('#modal').addClass('hidden');

                window.deleteData = (id) => {
                    Swal.fire({
                        title: 'Yakin?',
                        icon: 'warning',
                        showCancelButton: true
                    }).then(r => {
                        if (r.isConfirmed) {
                            $('<form>', {
                                method: 'POST',
                                action: `/violations/${id}`,
                                html: `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        @csrf
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <input type="hidden" name="_method" value="DELETE">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    `
                            }).appendTo('body').submit();
                        }
                    })
                }

                $('#form').on('submit', function () {
                    const btn = $('#btnSubmit');

                    btn.prop('disabled', true)
                        .addClass('opacity-70 cursor-not-allowed');

                    $('#btnText').text('Menyimpan...');
                    $('#btnLoading').removeClass('hidden');
                });

            })
        </script>
    @endpush
@endsection