<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPermission;
use App\Models\StudentViolation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentPermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentPermission::with(['student.class', 'checkin']);

        if (Auth::user()->role === 'wali_kelas') {
            $query->whereHas('student.class', function ($q) {
                $q->where('id', Auth::user()->class->id);
            });
        }

        if (Auth::user()->role === 'wali_kelas') {
            $query->where('wali_kelas_id', Auth::user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $students = Student::when(
            Auth::user()->role === 'wali_kelas',
            fn($q) => $q->whereHas(
                'class',
                fn($c) =>
                $c->where('class_id', Auth::user()->class->id)
            )
        )->get();

        $activePermissionCount = 0;

        if (Auth::user()->role === 'wali_kelas') {
            $activePermissions = StudentPermission::with('checkin')
                ->where('wali_kelas_id', Auth::user()->id)
                ->where('status', 'approved')
                ->where('start_at', '<=', now())
                ->where('end_at', '>=', now())
                ->where('type', '!=', 'sakit')
                ->get();

            $activePermissionCount = $activePermissions->filter(function ($permission) {
                return !$permission->checkin || !$permission->checkin->checkin_at;
            })->count();
        }

        return view('permissions.index', [
            'permissions' => $query->latest()->get(),
            'students' => $students,
            'activePermissionCount' => $activePermissionCount,
        ]);
    }

    public function pdf(Request $request)
    {
        $query = StudentPermission::with(['student.class', 'checkin']);

        if (Auth::user()->role === 'wali_kelas') {
            $query->where('wali_kelas_id', Auth::user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $permissions = $query->latest()->get();

        $pdf = Pdf::loadView('permissions.pdf', [
            'permissions' => $permissions,
            'request' => $request
        ])->setPaper('A4', 'portrait');

        return $pdf->download('laporan-perizinan.pdf');
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'wali_kelas', 403);

        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'reason' => 'required|string',
            'surat_ortu'   => 'required|file|mimes:pdf,jpg,png|max:2048',
            'surat_dokter' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'address'       => 'required|string|max:255',
        ], [
            'student_id.required' => 'Siswa wajib dipilih',
            'address.required' => 'Alamat wajib diisi',
            'type.required' => 'Jenis izin wajib dipilih',
            'start_at.required' => 'Tanggal mulai wajib diisi',
            'end_at.required' => 'Tanggal selesai wajib diisi',
            'reason.required' => 'Alasan wajib diisi',
            'end_at.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai',
            'start_at.before_or_equal' => 'Tanggal mulai harus sebelum tanggal selesai',
            'surat_ortu.file' => 'Surat ortu harus berupa file PDF, JPG, atau PNG',
            'surat_dokter.file' => 'Surat dokter harus berupa file PDF, JPG, atau PNG',
            'surat_ortu.mimes' => 'Surat ortu harus berupa file PDF, JPG, atau PNG',
            'surat_dokter.mimes' => 'Surat dokter harus berupa file PDF, JPG, atau PNG',
            'surat_ortu.max' => 'Surat ortu maksimal berukuran 2MB',
            'surat_dokter.max' => 'Surat dokter maksimal berukuran 2MB',
            'surat_ortu.required' => 'Surat ortu wajib diunggah',
        ]);

        $hasActivePermission = StudentPermission::where('student_id', $data['student_id'])
            ->whereIn('status', ['pending', 'approved'])
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->whereDoesntHave('checkin')
            ->exists();


        if ($hasActivePermission) {
            return redirect()->back()
                ->withErrors([
                    'student_id' => 'Siswa masih dalam masa izin dan tidak dapat mengajukan izin baru.'
                ])
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

        /* ================= NOMOR SURAT ================= */
        $year = now()->year;
        $urut = StudentPermission::whereYear('created_at', $year)->count() + 1;

        $nomorSurat = sprintf(
            '421.5/%03d/WK-%s/%d',
            $urut,
            strtoupper(config('school.code', 'SMP')),
            $year
        );

        /* ================= SIMPAN PERMISSION DULU ================= */
        $permission = StudentPermission::create([
            ...$data,
            'wali_kelas_id' => Auth::id(),
            'status' => 'pending',
        ]);

        /* ================= GENERATE QR ================= */
        $guruId = Auth::id();
        $timestamp = now()->format('YmdHi');

        $signature = sha1(
            $permission->id . '|' .
                $guruId . '|' .
                $timestamp . '|' .
                config('app.key')
        );

        $verifyUrl = route('verify.walas', [
            'p' => $permission->id,
            'g' => $guruId,
            't' => $timestamp,
            's' => $signature,
        ]);

        $qrCode = 'data:image/png;base64,' . base64_encode(
            file_get_contents(
                'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
                    'size' => '200x200',
                    'data' => $verifyUrl,
                ])
            )
        );

        /* ================= GENERATE PDF ================= */
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

        $permission->update([
            'surat_walas' => $path,
        ]);

        return redirect()->back()->with('success', 'Permohonan izin berhasil diajukan');
    }

    public function show($id)
    {
        $permission = StudentPermission::with('student')->findOrFail($id);

        return view('student_permissions.show', compact('permission'));
    }

    public function checkViolation($studentId)
    {
        $now = now();

        $violations = StudentViolation::where('student_id', $studentId)
            ->where(function ($q) use ($now) {
                $q->where('no_phone_until', '>=', $now)
                    ->orWhere('no_permission_until', '>=', $now)
                    ->orWhere('attendance_until', '>=', $now);
            })
            ->get();

        $response = [
            'has_violation' => false,
            'details' => [],
        ];

        foreach ($violations as $v) {
            $handlingType = $v->handling_type;

            if ($handlingType === 'pengasuhan') {
                $untils = [];
                if ($v->no_phone && $v->no_phone_until) {
                    $untils[] = 'HP sampai ' . Carbon::parse($v->no_phone_until)->format('d M Y');
                }
                if ($v->no_permission && $v->no_permission_until) {
                    $untils[] = 'Izin sampai ' . Carbon::parse($v->no_permission_until)->format('d M Y');
                }

                $response['has_violation'] = true;
                $response['details'][$handlingType] = [
                    'type' => ucfirst($v->type),
                    'description' => $v->description,
                    'until' => implode(', ', $untils),
                    'can_apply_at' => $v->no_phone_until ? Carbon::parse($v->no_phone_until)->addDay()->format('d M Y') : null,
                ];
            } else {
                if ($v->attendance_percentage < 80) {
                    $response['has_violation'] = true;
                    $response['details'][$handlingType] = [
                        'attendance_percentage' => $v->attendance_percentage,
                        'handling_type' => $handlingType,
                        'until' => $v->attendance_until ? Carbon::parse($v->attendance_until)->format('d M Y') : '-',
                    ];
                }
            }
        }

        return response()->json($response);
    }
}
