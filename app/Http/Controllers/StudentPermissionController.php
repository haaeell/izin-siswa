<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentPermission;
use App\Models\StudentPermissionCheckin;
use App\Models\StudentViolation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class StudentPermissionController extends Controller
{
    public function index(Request $request)
    {
        $user        = Auth::user();
        $isWalikelas = $user->role === 'wali_kelas';

        $students = Student::when(
            $isWalikelas,
            fn($q) => $q->where('class_id', $user->class->id)
        )->get();

        $activePermissionCount = 0;
        $maxActivePermissions  = (int) Setting::get('max_active_permissions', 3);

        if ($isWalikelas) {
            $activePermissions = StudentPermission::with('checkin')
                ->where('wali_kelas_id', $user->id)
                ->where('status', 'approved')
                ->whereNotIn('type', ['perpulangan', 'sakit'])
                ->where('start_at', '<=', now())
                ->where('end_at', '>=', now())
                ->get();

            $activePermissionCount = $activePermissions->filter(
                fn($p) => !$p->checkin || !$p->checkin->checkin_at
            )->count();
        }

        $classes = SchoolClass::when(
            $isWalikelas,
            fn($q) => $q->where('wali_kelas_id', $user->id)
        )->orderBy('name')->get();

        return view('permissions.index', [
            'students'             => $students,
            'activePermissionCount' => $activePermissionCount,
            'maxActivePermissions' => $maxActivePermissions,
            'classes'              => $classes,
            'isWalikelas'           => $isWalikelas,
        ]);
    }

    public function data(Request $request)
    {
        $user        = Auth::user();
        $isWalikelas = $user->role === 'wali_kelas';

        $query = StudentPermission::with(['student.class', 'student.dormitory', 'checkin'])
            ->select('student_permissions.*');

        if ($isWalikelas) {
            $query->where('wali_kelas_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date   . ' 23:59:59',
            ]);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        if ($request->filled('checkin_status')) {

            if ($request->checkin_status === 'belum_checkout') {
                $query->where('status', 'approved')->whereDoesntHave('checkin');
            } elseif ($request->checkin_status === 'dirumah') {
                $query->whereHas('checkin', function ($q) {
                    $q->whereNotNull('checkout_at')
                        ->whereNull('checkin_at');
                });
            } elseif ($request->checkin_status === 'kembali') {
                $query->whereHas('checkin', function ($q) {
                    $q->whereNotNull('checkout_at')
                        ->whereNotNull('checkin_at');
                });
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('nis',         fn($p) => $p->student->nis ?? '-')
            ->addColumn('student_name', fn($p) => $p->student->name ?? '-')
            ->addColumn('class_name',  fn($p) => $p->student->class->name ?? '-')

            ->addColumn('alasan', function ($p) {
                $safe = addslashes($p->reason);
                return '<button onclick="openReasonModal(`' . $safe . '`)"
                            class="text-blue-600 hover:text-blue-800 text-xs inline-flex items-center gap-1">
                            <i class="fa-solid fa-eye"></i> Alasan
                        </button>';
            })

            ->addColumn('waktu', function ($p) {
                return '<span class="text-xs text-slate-700">
                            <i class="fa-regular fa-clock text-slate-400 mr-1"></i>'
                    . Carbon::parse($p->start_at)->format('d M Y H:i')
                    . ' <span class="mx-1 text-slate-400">→</span> '
                    . Carbon::parse($p->end_at)->format('d M Y H:i')
                    . '</span>';
            })

            ->addColumn('file', function ($p) {
                $html = '<div class="flex items-center justify-center gap-2">';
                if ($p->surat_walas) {
                    $html .= '<a href="' . asset('storage/' . $p->surat_walas) . '" target="_blank"
                                class="text-blue-600 hover:text-blue-800 tooltip-icon"
                                data-title="Surat Wali Kelas" data-desc="Klik untuk melihat surat wali kelas">
                                <i class="fa-solid fa-user-tie"></i></a>';
                }
                if ($p->surat_ortu) {
                    $html .= '<a href="' . asset('storage/' . $p->surat_ortu) . '" target="_blank"
                                class="text-green-600 hover:text-green-800 tooltip-icon"
                                data-title="Surat Orang Tua" data-desc="Klik untuk melihat surat orang tua">
                                <i class="fa-solid fa-people-roof"></i></a>';
                }
                if ($p->surat_dokter) {
                    $html .= '<a href="' . asset('storage/' . $p->surat_dokter) . '" target="_blank"
                                class="text-red-600 hover:text-red-800 tooltip-icon"
                                data-title="Surat Dokter" data-desc="Klik untuk melihat surat dokter">
                                <i class="fa-solid fa-user-doctor"></i></a>';
                }
                if ($p->surat_terlambat) {
                    $html .= '<a href="' . asset('storage/' . $p->surat_terlambat) . '" target="_blank"
                                class="text-orange-500 hover:text-orange-700 tooltip-icon"
                                data-title="Surat Terlambat" data-desc="Klik untuk melihat surat keterangan terlambat">
                                <i class="fa-solid fa-clock-rotate-left"></i></a>';
                }
                if (!$p->surat_walas && !$p->surat_ortu && !$p->surat_dokter && !$p->surat_terlambat) {
                    $html .= '<span class="text-slate-400 text-xs">—</span>';
                }
                $html .= '</div>';
                return $html;
            })

            ->addColumn('status_badge', function ($p) {
                return match ($p->status) {
                    'pending'  => '<span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs word-breaks"><i class="fa-regular fa-clock"></i> Pending</span>',
                    'approved' => '<span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded text-xs word-breaks"><i class="fa-solid fa-check"></i> Disetujui</span>',
                    default    => '<span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded text-xs word-breaks"><i class="fa-solid fa-xmark"></i> Ditolak</span>',
                };
            })

            ->addColumn('aksi_walas', function ($p) {
                $html = '<div class="flex items-center justify-center gap-1.5 flex-nowrap">';

                if ($p->type !== 'perpulangan') {
                    if (in_array($p->status, ['approved', 'rejected'])) {
                        $html .= '<a href="' . route('permissions.surat', $p->id) . '" target="_blank"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition whitespace-nowrap">
                                    <i class="fa-solid fa-file-lines"></i> Lihat Surat</a>';
                    } else {
                        $html .= '<span class="text-xs text-slate-400 whitespace-nowrap">Belum tersedia</span>';
                    }
                }

                $label = $p->surat_terlambat ? 'Ganti File Terlambat' : 'Upload File Terlambat';
                $html .= '<button onclick="openTerlambatModal(' . $p->id . ')"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs rounded-lg transition whitespace-nowrap">
                            <i class="fa-solid fa-clock-rotate-left"></i> ' . $label . '</button>';

                $html .= '</div>';
                return $html;
            })

            ->addColumn('aksi_perizinan', function ($p) {
                $html = '<div class="flex items-center justify-start gap-2 whitespace-nowrap">';

                if ($p->status === 'pending') {
                    $html .= '<button onclick="approvePermission(' . $p->id . ')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg transition">
                                <i class="fa-solid fa-check"></i> <span>Setujui</span></button>';
                    $html .= '<button onclick="openRejectModal(' . $p->id . ')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg transition">
                                <i class="fa-solid fa-xmark"></i> <span>Tolak</span></button>';
                } elseif ($p->status === 'rejected') {
                    $safe = addslashes($p->reject_reason ?? '');
                    $html .= '<button onclick="showRejectReason(`' . $safe . '`)"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg transition">
                                <i class="fa-solid fa-eye"></i> <span>Alasan Ditolak</span></button>';
                } elseif ($p->status === 'approved' && $p->qr_token) {
                    $name     = addslashes($p->student->name ?? '');
                    $nis      = $p->student->nis ?? '';
                    $kelas    = addslashes($p->student->class->name ?? '');
                    $asrama   = addslashes($p->student->dormitory->name ?? '-');
                    $type     = ucfirst($p->type);
                    $startFmt = Carbon::parse($p->start_at)->format('d M Y H:i');
                    $endFmt   = Carbon::parse($p->end_at)->format('d M Y H:i');
                    $html .= '<button onclick="showBarcode(\'' . $p->qr_token . '\',\'' . $name . '\',\'' . $nis . '\',\'' . $kelas . '\',\'' . $asrama . '\',\'' . $type . '\',\'' . $startFmt . '\',\'' . $endFmt . '\')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-lg transition">
                                <i class="fa-solid fa-barcode"></i> <span>Barcode</span></button>';
                }

                if ($p->type !== 'perpulangan') {
                    if (in_array($p->status, ['approved', 'rejected'])) {
                        $html .= '<a href="' . route('permissions.surat', $p->id) . '" target="_blank"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition">
                                <i class="fa-solid fa-file-lines"></i> <span>Surat</span></a>';
                    }
                }

                $html .= '</div>';
                return $html;
            })

            ->rawColumns(['alasan', 'waktu', 'file', 'status_badge', 'aksi_walas', 'aksi_perizinan'])
            ->make(true);
    }

    public function pdf(Request $request)
    {
        $user        = Auth::user();
        $isWalikelas = $user->role === 'wali_kelas';

        $query = StudentPermission::with(['student.class', 'checkin']);

        if ($isWalikelas) {
            $query->where('wali_kelas_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kelasName = null;

        if ($request->filled('kelas')) {
            $kelas = SchoolClass::find($request->kelas);
            $kelasName = $kelas?->name;
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date   . ' 23:59:59',
            ]);
        }

        if ($request->filled('checkin_status')) {
            if ($request->checkin_status === 'belum_checkout') {
                $query->whereDoesntHave('checkin');
            } elseif ($request->checkin_status === 'dirumah') {
                $query->whereHas('checkin', function ($q) {
                    $q->whereNotNull('checkout_at')->whereNull('checkin_at');
                });
            } elseif ($request->checkin_status === 'kembali') {
                $query->whereHas('checkin', function ($q) {
                    $q->whereNotNull('checkout_at')->whereNotNull('checkin_at');
                });
            }
        }

        $permissions = $query->latest()->get();

        $tanggal = date('Y-m-d');
        $namaKelasFile = $kelasName ? str_replace(' ', '-', $kelasName) : 'Semua-Kelas';

        $fileName = "laporan-perizinan-{$namaKelasFile}-{$tanggal}.pdf";

        $pdf = Pdf::loadView('permissions.pdf', [
            'permissions' => $permissions,
            'request'     => $request,
            'kelas'       => $kelasName,
        ])->setPaper('A4', 'portrait');

        return $pdf->download($fileName);
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'wali_kelas', 403);

        $data = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'type'         => 'required|string',
            'start_at'     => 'required|date',
            'end_at'       => 'required|date|after_or_equal:start_at',
            'reason'       => 'required|string',
            'surat_ortu'   => 'required|file|mimes:pdf,jpg,png|max:2048',
            'surat_dokter' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'address'      => 'required|string|max:255',
        ], [
            'student_id.required'  => 'Siswa wajib dipilih',
            'address.required'     => 'Alamat wajib diisi',
            'type.required'        => 'Jenis izin wajib dipilih',
            'start_at.required'    => 'Tanggal mulai wajib diisi',
            'end_at.required'      => 'Tanggal selesai wajib diisi',
            'reason.required'      => 'Alasan wajib diisi',
            'end_at.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai',
            'surat_ortu.required'  => 'Surat ortu wajib diunggah',
            'surat_ortu.mimes'     => 'Surat ortu harus berupa file PDF, JPG, atau PNG',
            'surat_dokter.mimes'   => 'Surat dokter harus berupa file PDF, JPG, atau PNG',
            'surat_ortu.max'       => 'Surat ortu maksimal berukuran 2MB',
            'surat_dokter.max'     => 'Surat dokter maksimal berukuran 2MB',
        ]);

        $hasActivePermission = StudentPermission::where('student_id', $data['student_id'])
            ->whereIn('status', ['pending', 'approved'])
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->whereDoesntHave('checkin')
            ->exists();

        if ($hasActivePermission) {
            return redirect()->back()
                ->withErrors(['student_id' => 'Siswa masih dalam masa izin dan tidak dapat mengajukan izin baru.'])
                ->withInput();
        }

        foreach (['surat_ortu', 'surat_dokter'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $data[$field] = $file->storeAs(
                    "permissions/{$field}",
                    $field . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension(),
                    'public'
                );
            }
        }

        $year       = now()->year;
        $urut       = StudentPermission::whereYear('created_at', $year)->count() + 1;
        $nomorSurat = sprintf('421.5/%03d/WK-%s/%d', $urut, strtoupper(config('school.code', 'QR')), $year);
        $student = Student::findOrFail($data['student_id']);

        $permission = StudentPermission::create([
            ...$data,
            'wali_kelas_id' => Auth::id(),
            'student_name'  => $student->name,           // tambah ini
            'student_class' => $student->class->name,
            'status'        => 'pending',
        ]);

        $guruId    = Auth::id();
        $timestamp = now()->format('YmdHi');
        $signature = sha1($permission->id . '|' . $guruId . '|' . $timestamp . '|' . config('app.key'));

        $verifyUrl = route('verify.walas', [
            'p' => $permission->id,
            'g' => $guruId,
            't' => $timestamp,
            's' => $signature,
        ]);

        $qrCode = 'data:image/png;base64,' . base64_encode(
            file_get_contents('https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
                'size' => '200x200',
                'data' => $verifyUrl,
            ]))
        );

        $student = Student::findOrFail($data['student_id']);

        $pdf = Pdf::loadView('pdf.surat-walas', [
            'student' => $student,
            'wali'    => Auth::user(),
            'type'    => $data['type'],
            'start'   => $data['start_at'],
            'end'     => $data['end_at'],
            'address' => $data['address'],
            'reason'  => $data['reason'],
            'nomor'   => $nomorSurat,
            'qrCode'  => $qrCode,
            'city'    => 'Yogyakarta',
            'verify'  => $verifyUrl,
        ]);

        $path = 'permissions/surat-walas/surat-walas-' . now()->format('YmdHis') . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $permission->update(['surat_walas' => $path]);

        return redirect()->back()->with('success', 'Permohonan izin berhasil diajukan');
    }

    public function show($id)
    {
        $permission = StudentPermission::with('student')->findOrFail($id);
        return view('student_permissions.show', compact('permission'));
    }

    public function checkViolation($studentId)
    {
        $now        = now();
        $violations = StudentViolation::where('student_id', $studentId)
            ->where(function ($q) use ($now) {
                $q->where('no_phone_until', '>=', $now)
                    ->orWhere('no_permission_until', '>=', $now)
                    ->orWhere('attendance_until', '>=', $now);
            })->get();

        $response = ['has_violation' => false, 'details' => []];

        foreach ($violations as $v) {
            $type = $v->handling_type;

            if ($type === 'pengasuhan') {
                $untils = [];
                if ($v->no_phone && $v->no_phone_until) {
                    $untils[] = 'HP sampai ' . Carbon::parse($v->no_phone_until)->format('d M Y');
                }
                if ($v->no_permission && $v->no_permission_until) {
                    $untils[] = 'Izin sampai ' . Carbon::parse($v->no_permission_until)->format('d M Y');
                }
                $response['has_violation']     = true;
                $response['details'][$type]    = [
                    'type'        => ucfirst($v->type),
                    'description' => $v->description,
                    'until'       => implode(', ', $untils),
                    'can_apply_at' => $v->no_phone_until
                        ? Carbon::parse($v->no_phone_until)->addDay()->format('d M Y')
                        : null,
                ];
            } elseif ($v->attendance_percentage <= 15) {
                $response['has_violation']  = true;
                $response['details'][$type] = [
                    'attendance_percentage' => $v->attendance_percentage,
                    'handling_type'         => $type,
                    'until'                 => $v->attendance_until
                        ? Carbon::parse($v->attendance_until)->format('d M Y')
                        : '-',
                ];
            }
        }

        return response()->json($response);
    }

    public function storeMassal(Request $request)
    {
        abort_if(auth()->user()->role !== 'wali_kelas', 403);

        $data = $request->validate([
            'start_at' => 'required|date',
            'end_at'   => 'required|date|after_or_equal:start_at',
            'reason'   => 'required|string|max:500',
        ], [
            'start_at.required'     => 'Tanggal mulai wajib diisi',
            'end_at.required'       => 'Tanggal selesai wajib diisi',
            'end_at.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai',
            'reason.required'       => 'Alasan wajib diisi',
        ]);

        $user    = Auth::user();
        $students = Student::where('class_id', $user->class->id)
            ->select('id', 'name', 'nis', 'class_id', 'dormitory_id')
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada siswa di kelas ini.');
        }

        $guruId = $user->id;
        $now    = now();

        DB::transaction(function () use ($students, $data, $guruId, $now) {
            StudentPermission::insert(
                $students->map(fn($s) => [
                    'student_id'    => $s->id,
                    'wali_kelas_id' => $guruId,
                    'type'          => 'perpulangan',
                    'start_at'      => $data['start_at'],
                    'end_at'        => $data['end_at'],
                    'reason'        => $data['reason'],
                    'status'        => 'approved',
                    'address'       => '-',
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ])->toArray()
            );

            $permissions = StudentPermission::whereIn('student_id', $students->pluck('id'))
                ->where('wali_kelas_id', $guruId)
                ->whereDate('created_at', $now->toDateString())
                ->orderBy('id')
                ->get()
                ->keyBy('student_id');

            $checkinRows = [];

            foreach ($students as $student) {
                $permission = $permissions->get($student->id);
                if (!$permission) continue;

                StudentPermission::where('id', $permission->id)->update([
                    'qr_token' => strtoupper(base_convert($permission->id, 10, 36) . Str::random(4)),
                ]);

                $checkinRows[] = [
                    'student_permission_id' => $permission->id,
                    'checkout_at'           => $now,
                    'checkin_at'            => null,
                    'status'                => 'DI LUAR',
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ];
            }

            if (!empty($checkinRows)) {
                StudentPermissionCheckin::insert($checkinRows);
            }
        });

        return redirect()->back()->with('success', "Izin perpulangan massal berhasil dibuat untuk {$students->count()} siswa.");
    }

    public function uploadTerlambat(Request $request, $id)
    {
        $request->validate([
            'surat_terlambat' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ], [
            'surat_terlambat.required' => 'File surat keterangan terlambat wajib dipilih.',
            'surat_terlambat.mimes'    => 'File harus berformat PDF, JPG, atau PNG.',
            'surat_terlambat.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $permission = StudentPermission::findOrFail($id);

        if ($permission->surat_terlambat) {
            Storage::disk('public')->delete($permission->surat_terlambat);
        }

        $file = $request->file('surat_terlambat');
        $path = $file->storeAs(
            'permissions/surat_terlambat',
            'terlambat_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension(),
            'public'
        );

        $permission->update(['surat_terlambat' => $path]);

        return redirect()->back()->with('success', 'Surat keterangan terlambat berhasil diupload.');
    }

    public function getQrMassal(Request $request)
    {
        abort_if(!in_array(auth()->user()->role, ['perizinan', 'wali_kelas']), 403);

        $request->validate(['tanggal' => 'required|date']);

        $data = StudentPermission::with(['student.class', 'student.dormitory'])
            ->where('type', 'perpulangan')
            ->where('status', 'approved')
            ->whereDate('start_at', '<=', $request->tanggal)
            ->whereDate('end_at', '>=', $request->tanggal)
            ->join('students', 'student_permissions.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->orderBy('classes.name')
            ->orderBy('students.name')
            ->select('student_permissions.*')
            ->get()
            ->map(fn($p) => [
                'token'    => $p->qr_token,
                'nama'     => $p->student->name,
                'nis'      => $p->student->nis,
                'kelas'    => $p->student->class->name ?? '-',
                'asrama'   => $p->student->dormitory->name ?? '-',
                'izin'     => $p->type,
                'start_at' => Carbon::parse($p->start_at)->format('d M Y H:i'),
                'end_at'   => Carbon::parse($p->end_at)->format('d M Y H:i'),
            ]);

        return response()->json([
            'count' => $data->count(),
            'data'  => $data,
        ]);
    }
}
