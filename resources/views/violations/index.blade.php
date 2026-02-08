@extends('layouts.app')

@section('title', 'Pelanggaran Siswa')

@section('content')
    <div class="mx-auto p-4 sm:p-6 bg-white rounded-xl">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold">Pelanggaran Siswa</h1>
                <p class="text-sm text-slate-500">Manajemen pelanggaran dan hukuman</p>
            </div>

            @if (Auth::user()->role !== 'wali_kelas')
                <button onclick="openCreateModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    + Tambah Pelanggaran
                </button>
            @endif
        </div>

        {{-- FILTER KELAS --}}
        @if (Auth::user()->role === 'perizinan')
            <form method="GET" class="mb-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <select name="class_id" id="filter_class" class="w-full sm:w-64">
                        <option value="">Semua Kelas</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Filter
                        </button>

                        @if (request('class_id'))
                            <a href="{{ url()->current() }}" class="px-4 py-2 border rounded-lg hover:bg-slate-100">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        @endif

        {{-- NAV TABS --}}
        <div class="mb-4 border-b overflow-x-auto">
            <ul class="flex gap-2 min-w-max" id="handlingTabs">
                <li><button class="tab-btn px-4 py-2 rounded-t-lg whitespace-nowrap"
                        data-type="pengasuhan">Pengasuhan</button></li>
                <li><button class="tab-btn px-4 py-2 rounded-t-lg whitespace-nowrap"
                        data-type="pengajaran">Pengajaran</button></li>
                <li><button class="tab-btn px-4 py-2 rounded-t-lg whitespace-nowrap"
                        data-type="pelatihan">Pelatihan</button></li>
            </ul>
        </div>

        {{-- TABLE --}}
        <div class="bg-white shadow rounded-xl p-3 sm:p-4 overflow-x-auto">
            <div class="text-xs text-slate-400 mb-2 sm:hidden">
                Geser ke samping →
            </div>

            <table id="datatable" class="w-full text-sm min-w-[900px]">
                <thead class="bg-slate-100">
                    <tr>
                        <th>#</th>
                        <th>Siswa</th>
                        <th>Kelas</th>

                        @if (request('handling_type') === 'pengasuhan')
                            <th>Tipe</th>
                            <th>Kejadian</th>
                            <th>Keterangan</th>
                            <th>Hukuman</th>
                            <th>Sampai</th>
                        @else
                            <th>Persentase Kehadiran</th>
                            <th>Berlaku Sampai</th>
                        @endif

                        @if (Auth::user()->role !== 'wali_kelas')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @foreach ($violations as $i => $v)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $v->student->name }}</td>
                            <td>{{ $v->student->class->name }}</td>

                            @if ($v->handling_type === 'pengasuhan')
                                <td>
                                    <span
                                        class="px-2 py-1 rounded text-white
                                    {{ $v->type == 'ringan' ? 'bg-green-500' : ($v->type == 'sedang' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                        {{ ucfirst($v->type) }}
                                    </span>
                                </td>
                                <td>{{ $v->occurred_at }}</td>
                                <td class="max-w-xs truncate">{{ $v->description }}</td>
                                <td class="text-xs space-y-1">
                                    @if ($v->no_phone)
                                        <div>🚫 HP</div>
                                    @endif
                                    @if ($v->no_permission)
                                        <div>🚫 Izin</div>
                                    @endif
                                </td>
                                <td class="text-xs">
                                    @if ($v->no_phone_until)
                                        <div>HP: {{ \Carbon\Carbon::parse($v->no_phone_until)->format('d-m-Y') }}</div>
                                    @endif
                                    @if ($v->no_permission_until)
                                        <div>Izin: {{ \Carbon\Carbon::parse($v->no_permission_until)->format('d-m-Y') }}
                                        </div>
                                    @endif
                                </td>
                            @else
                                <td>{{ $v->attendance_percentage ?? '-' }}%</td>
                                <td>{{ \Carbon\Carbon::parse($v->attendance_until)->format('d-m-Y') }}</td>
                            @endif

                            @if (Auth::user()->role !== 'wali_kelas')
                                <td class="whitespace-nowrap">
                                    <button onclick='openEditModal(@json($v))'
                                        class="px-2 py-1 bg-yellow-400 rounded">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button onclick="deleteData({{ $v->id }})"
                                        class="px-2 py-1 bg-red-500 text-white rounded">
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
    <div id="modal"
        class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center">

        <div
            class="bg-white w-full sm:max-w-xl sm:rounded-2xl rounded-t-2xl shadow-xl max-h-[95vh] sm:max-h-[90vh] flex flex-col animate-scaleIn">

            {{-- HEADER --}}
            <div class="flex items-start justify-between px-4 sm:px-6 py-4 border-b">
                <div>
                    <h2 id="modalTitle" class="text-base sm:text-lg font-semibold text-slate-800">
                        Tambah Pelanggaran
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Catat pelanggaran siswa dengan benar
                    </p>
                </div>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">
                    &times;
                </button>
            </div>

            {{-- BODY --}}
            <form id="form" method="POST" class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="method">

                {{-- SISWA --}}
                <div>
                    <label class="text-sm font-medium text-slate-700">Siswa</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="student_id" id="student_id"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 select2">
                            @foreach ($students as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- CATEGORY --}}
                <div>
                    <label class="text-sm font-medium text-slate-700">Tipe</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select name="category" id="category"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 select2">
                            <option value="pengasuhan">Pengasuhan</option>
                            <option value="pengajaran">Pengajaran</option>
                            <option value="pelatihan">Pelatihan</option>
                        </select>
                    </div>
                </div>

                {{-- PENGASUHAN --}}
                <div id="pengasuhan" class="space-y-4">

                    <div>
                        <label class="text-sm font-medium text-slate-700">Jenis Pelanggaran</label>
                        <div class="relative mt-1">
                            <i
                                class="fa-solid fa-triangle-exclamation absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select name="type"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 select2">
                                <option value="ringan">Ringan</option>
                                <option value="sedang">Sedang</option>
                                <option value="berat">Berat</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Tanggal Kejadian</label>
                        <div class="relative mt-1">
                            <i
                                class="fa-solid fa-calendar-days absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="date" name="occurred_at" id="occurred_at"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Deskripsi</label>
                        <div class="relative mt-1">
                            <i class="fa-solid fa-align-left absolute left-3 top-3 text-slate-400"></i>
                            <textarea name="description" rows="3"
                                class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Contoh: Membawa HP saat jam pelajaran"></textarea>
                        </div>
                    </div>

                    {{-- SANKSI --}}
                    <div>
                        <label class="text-sm font-medium text-slate-700 mb-2 block">Sanksi</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label
                                class="flex gap-3 border rounded-xl p-3 cursor-pointer hover:border-blue-500 hover:bg-blue-50">
                                <input type="checkbox" name="no_phone" class="mt-1">
                                <div>
                                    <p class="text-sm font-medium">🚫 Larangan HP</p>
                                    <p class="text-xs text-slate-500">Tidak boleh mengambil HP</p>
                                </div>
                            </label>

                            <label
                                class="flex gap-3 border rounded-xl p-3 cursor-pointer hover:border-red-500 hover:bg-red-50">
                                <input type="checkbox" name="no_permission" class="mt-1">
                                <div>
                                    <p class="text-sm font-medium">🚫 Larangan Izin</p>
                                    <p class="text-xs text-slate-500">Tidak boleh izin pulang</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-600">Larangan HP sampai</label>
                            <input type="date" name="no_phone_until"
                                class="w-full py-2 px-3 border rounded-lg disabled:bg-slate-100" disabled>
                        </div>
                        <div>
                            <label class="text-xs text-slate-600">Larangan Izin sampai</label>
                            <input type="date" name="no_permission_until"
                                class="w-full py-2 px-3 border rounded-lg disabled:bg-slate-100" disabled>
                        </div>
                    </div>
                </div>

                {{-- ATTENDANCE --}}
                <div id="attendanceSection" class="border rounded-xl p-4 space-y-3 hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-600">Kehadiran (%)</label>
                            <input type="number" name="attendance_percentage" min="1" max="100"
                                class="w-full py-2 px-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="text-xs text-slate-600">Berlaku Sampai</label>
                            <input type="date" name="attendance_until" class="w-full py-2 px-3 border rounded-lg">
                        </div>
                    </div>
                </div>
            </form>

            {{-- FOOTER --}}
            <div class="px-4 sm:px-6 py-3 border-t flex gap-2">
                <button type="button" onclick="closeModal()"
                    class="flex-1 sm:flex-none px-4 py-2 border rounded-lg hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" form="form"
                    class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(function() {
                $('.select2').select2({
                    width: '100%',
                    allowClear: true
                });
                const phoneUntil = document.getElementById('no_phone_until');
                const izinUntil = document.getElementById('no_permission_until');

                $('input[name="no_phone"]').on('change', function() {
                    phoneUntil.disabled = !this.checked;
                });
                $('input[name="no_permission"]').on('change', function() {
                    izinUntil.disabled = !this.checked;
                });

                $('#filter_class').select2({
                    placeholder: 'Pilih Kelas',
                    allowClear: true
                });
                $('#datatable').DataTable();

                function handleCategory() {
                    const val = $('#category').val();
                    if (val === 'pengasuhan') {
                        $('#attendanceSection').addClass('hidden');
                        $('#pengasuhan').removeClass('hidden');
                    } else {
                        $('#attendanceSection').removeClass('hidden');
                        $('#pengasuhan').addClass('hidden');
                    }
                }
                $('#category').on('change', handleCategory);

                // MODAL FUNCTIONS
                window.openCreateModal = () => {
                    $('#modal').removeClass('hidden');
                    $('#form').attr('action', '/violations');
                    $('#method').val('');
                    $('#form')[0].reset();
                    $('#category').val('pengasuhan').trigger('change');
                    handleCategory();
                    $('#no_phone_until').prop('disabled', true);
                    $('#no_permission_until').prop('disabled', true);
                }

                window.openEditModal = (d) => {
                    $('#modal').removeClass('hidden');
                    $('#form').attr('action', `/violations/${d.id}`);
                    $('#method').val('PUT');
                    $('#student_id').val(d.student_id);
                    $('#type').val(d.type);
                    $('#description').val(d.description);
                    $('#occurred_at').val(d.occurred_at);
                    $('#category').val(d.handling_type).trigger('change');
                    handleCategory();
                    $('#attendance_percentage').val(d.attendance_percentage);
                    $('#attendance_until').val(d.attendance_until);
                    $('#no_phone_until').val(d.no_phone_until).prop('disabled', !d.no_phone);
                    $('#no_permission_until').val(d.no_permission_until).prop('disabled', !d.no_permission);
                    $('input[name=no_phone]').prop('checked', d.no_phone);
                    $('input[name=no_permission]').prop('checked', d.no_permission);
                }

                window.closeModal = () => $('#modal').addClass('hidden');

                window.deleteData = (id) => {
                    Swal.fire({
                            title: 'Yakin?',
                            icon: 'warning',
                            showCancelButton: true
                        })
                        .then(r => {
                            if (r.isConfirmed) {
                                $('<form>', {
                                    method: 'POST',
                                    action: `/violations/${id}`,
                                    html: `@csrf<input type="hidden" name="_method" value="DELETE">`
                                }).appendTo('body').submit();
                            }
                        });
                }

                $('#form').on('submit', function() {
                    const btn = $('#btnSubmit');
                    btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed');
                    $('#btnText').text('Menyimpan...');
                    $('#btnLoading').removeClass('hidden');
                });

                $('#handlingTabs .tab-btn').on('click', function() {
                    const type = $(this).data('type');

                    $('#handlingTabs .tab-btn').removeClass('bg-blue-600 text-white');
                    $(this).addClass('bg-blue-600 text-white');

                    const classId = $('#filter_class').val() || '';
                    let url = new URL(window.location.href.split('?')[0], window.location.origin);
                    if (classId) url.searchParams.set('class_id', classId);
                    if (type) url.searchParams.set('handling_type', type);
                    window.location.href = url.toString();
                });


                const urlParams = new URLSearchParams(window.location.search);
                const currentType = urlParams.get('handling_type') || 'pengasuhan';
                $('#handlingTabs .tab-btn').removeClass('bg-blue-600 text-white');
                $(`#handlingTabs .tab-btn[data-type="${currentType}"]`).addClass('bg-blue-600 text-white');

            });
        </script>
    @endpush
@endsection
