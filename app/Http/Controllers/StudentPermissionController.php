<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPermission;
use App\Models\StudentViolation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            $activePermissionCount = StudentPermission::where('wali_kelas_id', Auth::user()->id)
                ->where('status', 'approved')
                ->where('start_at', '<=', now())
                ->where('end_at', '>=', now())
                ->count();
        }

        return view('permissions.index', [
            'permissions' => $query->latest()->get(),
            'students' => $students,
            'activePermissionCount' => $activePermissionCount,
        ]);
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
            'surat_walas'  => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'surat_ortu'   => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'surat_dokter' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ], [
            'student_id.required' => 'Siswa wajib dipilih',
            'type.required' => 'Jenis izin wajib dipilih',
            'start_at.required' => 'Tanggal mulai wajib diisi',
            'end_at.required' => 'Tanggal selesai wajib diisi',
            'reason.required' => 'Alasan wajib diisi',
            'end_at.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai',
            'start_at.before_or_equal' => 'Tanggal mulai harus sebelum tanggal selesai',
            'surat_walas.file' => 'Surat walas harus berupa file PDF, JPG, atau PNG',
            'surat_ortu.file' => 'Surat ortu harus berupa file PDF, JPG, atau PNG',
            'surat_dokter.file' => 'Surat dokter harus berupa file PDF, JPG, atau PNG',
            'surat_walas.mimes' => 'Surat walas harus berupa file PDF, JPG, atau PNG',
            'surat_ortu.mimes' => 'Surat ortu harus berupa file PDF, JPG, atau PNG',
            'surat_dokter.mimes' => 'Surat dokter harus berupa file PDF, JPG, atau PNG',
            'surat_walas.max' => 'Surat walas maksimal berukuran 2MB',
            'surat_ortu.max' => 'Surat ortu maksimal berukuran 2MB',
            'surat_dokter.max' => 'Surat dokter maksimal berukuran 2MB',
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

        if ($request->hasFile('surat_walas')) {
            $file = $request->file('surat_walas');
            $filename = 'walas_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

            $data['surat_walas'] = $file->storeAs(
                'permissions/surat-walas',
                $filename,
                'public'
            );
        }

        if ($request->hasFile('surat_ortu')) {
            $file = $request->file('surat_ortu');
            $filename = 'ortu_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

            $data['surat_ortu'] = $file->storeAs(
                'permissions/surat-ortu',
                $filename,
                'public'
            );
        }

        if ($request->hasFile('surat_dokter')) {
            $file = $request->file('surat_dokter');
            $filename = 'dokter_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

            $data['surat_dokter'] = $file->storeAs(
                'permissions/surat-dokter',
                $filename,
                'public'
            );
        }

        StudentPermission::create([
            ...$data,
            'wali_kelas_id' => Auth::user()->id,
            'status' => 'pending',
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
        $violation = StudentViolation::where('student_id', $studentId)->where('until', '>=', now())->latest()->first();

        if (!$violation) {
            return response()->json([
                'has_violation' => false
            ]);
        }

        return response()->json([
            'has_violation' => true,
            'type' => ucfirst($violation->type),
            'description' => $violation->description,
            'until' => Carbon::parse($violation->until)->format('d M Y'),
            'can_apply_at' => Carbon::parse($violation->until)->addDay()->format('d M Y'),
        ]);
    }
}
