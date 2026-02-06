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
                @if(Auth::user()->role !== 'wali_kelas')
                    <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Tambah Pelanggaran
                    </button>
                @endif

            </div>
        </div>

        {{-- FILTER KELAS --}}
        @if (Auth::user()->role === 'perizinan')
            <form method="GET" class="mb-4">
                <div class="flex items-center gap-3">
                    <select name="class_id" id="filter_class" class="w-64">
                        <option value="">Semua Kelas</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>

                    @if (request('class_id'))
                        <a href="{{ url()->current() }}" class="px-4 py-2 border rounded-lg hover:bg-slate-100">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        @endif

        {{-- NAV TABS FILTER HANDLING TYPE --}}
        <div class="mb-4 border-b">
            <ul class="flex space-x-4" id="handlingTabs">
                <li><button class="tab-btn px-4 py-2 rounded-t-lg" data-type="pengasuhan">Pengasuhan</button></li>
                <li><button class="tab-btn px-4 py-2 rounded-t-lg" data-type="pengajaran">Pengajaran</button></li>
                <li><button class="tab-btn px-4 py-2 rounded-t-lg" data-type="pelatihan">Pelatihan</button></li>
            </ul>
        </div>

        {{-- TABLE --}}
        <div class="bg-white shadow rounded-xl p-4">
            <table id="datatable" class="w-full text-sm">
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

                        @if(Auth::user()->role !== 'wali_kelas')
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
                                <td>{{ $v->description }}</td>
                                <td class="space-y-1">
                                    @if ($v->no_phone)
                                        <div class="text-xs">🚫 HP</div>
                                    @endif
                                    @if ($v->no_permission)
                                        <div class="text-xs">🚫 Izin</div>
                                    @endif
                                </td>
                                <td class="text-xs">
                                    @if ($v->no_phone && $v->no_phone_until)
                                        <div>HP: {{ \Carbon\Carbon::parse($v->no_phone_until)->format('d-m-Y') }}</div>
                                    @endif

                                    @if ($v->no_permission && $v->no_permission_until)
                                        <div>Izin: {{ \Carbon\Carbon::parse($v->no_permission_until)->format('d-m-Y') }}</div>
                                    @endif
                                </td>
                            @else
                                <td>{{ $v->attendance_percentage ?? '-' }}%</td>
                                <td>{{ \Carbon\Carbon::parse($v->attendance_until)->format('d-m-Y') }}</td>
                            @endif

                            @if(Auth::user()->role !== 'wali_kelas')
                                <td>
                                    <button onclick='openEditModal(@json($v))' class="px-2 py-1 bg-yellow-400 rounded">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <button onclick="deleteData({{ $v->id }})" class="px-2 py-1 bg-red-500 text-white rounded">
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
    <div id="modal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-xl max-h-[90vh] overflow-hidden animate-scaleIn">
            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b bg-white">
                <div>
                    <h2 id="modalTitle" class="text-lg font-semibold text-slate-800">Tambah Pelanggaran</h2>
                    <p class="text-sm text-slate-500">Catat pelanggaran siswa dengan benar</p>
                </div>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition text-xl">✕</button>
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
                        <select name="student_id" id="student_id"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg select2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
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
                            class="w-full pl-10 pr-3 py-2 border rounded-lg select2 focus:ring-2 focus:ring-blue-500">
                            <option value="pengasuhan">Pengasuhan</option>
                            <option value="pengajaran">Pengajaran</option>
                            <option value="pelatihan">Pelatihan</option>
                        </select>
                    </div>
                </div>

                <div id="pengasuhan">
                    {{-- JENIS PELANGGARAN --}}
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
                            <i
                                class="fa-solid fa-calendar-days absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="date" name="occurred_at" id="occurred_at"
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
                                class="flex items-start gap-3 border rounded-xl p-4 cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">
                                <input type="checkbox" name="no_phone"
                                    class="mt-1 w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <div>
                                    <p class="text-sm font-medium flex items-center gap-1">
                                        <i class="fa-solid fa-mobile-screen-button"></i> Larangan HP
                                    </p>
                                    <p class="text-xs text-slate-500">Tidak boleh mengambil HP</p>
                                </div>
                            </label>

                            <label
                                class="flex items-start gap-3 border rounded-xl p-4 cursor-pointer hover:border-red-500 hover:bg-red-50 transition">
                                <input type="checkbox" name="no_permission"
                                    class="mt-1 w-4 h-4 text-red-600 rounded focus:ring-red-500">
                                <div>
                                    <p class="text-sm font-medium flex items-center gap-1">
                                        <i class="fa-solid fa-ban"></i> Larangan Izin
                                    </p>
                                    <p class="text-xs text-slate-500">Tidak boleh izin pulang</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- BERLAKU SAMPAI --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-600">Larangan HP sampai</label>
                            <input type="date" name="no_phone_until" id="no_phone_until"
                                class="w-full py-2 px-3 border rounded-lg disabled:bg-slate-100" disabled>
                        </div>
                        <div>
                            <label class="text-xs text-slate-600">Larangan Izin sampai</label>
                            <input type="date" name="no_permission_until" id="no_permission_until"
                                class="w-full py-2 px-3 border rounded-lg disabled:bg-slate-100" disabled>
                        </div>
                    </div>
                </div>

                <div id="attendanceSection" class="border rounded-xl p-4 space-y-3 hidden">
                    {{-- KEHADIRAN --}}
                    <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <i class="fa-solid fa-chart-line"></i> Kehadiran
                    </label>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-600">Kehadiran (%)</label>
                            <input type="number" name="attendance_percentage" id="attendance_percentage" min="1" max="100"
                                class="w-full py-2 px-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="text-xs text-slate-600">Berlaku Sampai</label>
                            <input type="date" name="attendance_until" id="attendance_until"
                                value="{{ now()->addDays(7)->format('Y-m-d') }}" class="w-full py-2 px-3 border rounded-lg">
                        </div>
                    </div>
                </div>


                <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 rounded-lg border hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" id="btnSubmit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <span id="btnText">Simpan</span>
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
                const phoneUntil = document.getElementById('no_phone_until');
                const izinUntil = document.getElementById('no_permission_until');

                $('input[name="no_phone"]').on('change', function () {
                    phoneUntil.disabled = !this.checked;
                });
                $('input[name="no_permission"]').on('change', function () {
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

                $('#form').on('submit', function () {
                    const btn = $('#btnSubmit');
                    btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed');
                    $('#btnText').text('Menyimpan...');
                    $('#btnLoading').removeClass('hidden');
                });

                $('#handlingTabs .tab-btn').on('click', function () {
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