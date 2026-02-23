<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentViolation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentViolationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'wali_kelas') {
            $classes = SchoolClass::where('id', $user->class->id)->get();
        } else {
            $classes = SchoolClass::orderBy('name')->get();
        }

        $students = Student::when(
            $user->role === 'wali_kelas',
            fn($q) => $q->where('class_id', $user->class->id)
        )
            ->orderBy('name')
            ->get();

        $violations = StudentViolation::with(['student.class'])
            ->when(
                $user->role === 'wali_kelas',
                fn($q) =>
                $q->whereHas(
                    'student',
                    fn($s) =>
                    $s->where('class_id', $user->class->id)
                )
            )
            ->when($request->class_id && $user->role !== 'wali_kelas', function ($q) use ($request) {
                $q->whereHas('student', function ($s) use ($request) {
                    $s->where('class_id', $request->class_id);
                });
            })
            ->when($request->handling_type, function ($q) use ($request) {
                $q->where('handling_type', $request->handling_type);
            })
            ->latest()
            ->get();

        return view('violations.index', compact(
            'violations',
            'students',
            'classes'
        ));
    }



    public function store(Request $request)
    {

        $request->validate([
            'student_id'            => 'required|exists:students,id',
            'category'         => 'nullable|in:pengasuhan,pengajaran,pelatihan',
            'type'                  => 'nullable|in:ringan,sedang,berat',
            'description'           => 'nullable|string',
            'occurred_at'           => 'nullable|date',

            'no_phone'              => 'nullable',
            'no_phone_until'        => 'nullable|date',

            'no_permission'         => 'nullable',
            'no_permission_until'   => 'nullable|date',

            'attendance_percentage' => 'nullable|integer|min:1|max:100',
            'attendance_until'      => 'nullable|date',
        ]);

        StudentViolation::create([
            'student_id'            => $request->student_id,
            'handling_type'         => $request->category,
            'type'                  => $request->type,
            'description'           => $request->description,
            'occurred_at'           => $request->occurred_at,

            'no_phone'              => $request->no_phone == "on" ? true : false,
            'no_phone_until'        => $request->no_phone_until,

            'no_permission'         => $request->no_permission == "on" ? true : false,
            'no_permission_until'   => $request->no_permission_until,

            'attendance_percentage' => $request->attendance_percentage,
            'attendance_until'      => $request->attendance_until,

            'reported_by'           => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pelanggaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $violation = StudentViolation::findOrFail($id);

        $request->validate([
            'student_id'            => 'required|exists:students,id',
            'handling_type'         => 'nullable|in:pengasuhan,pengajaran,pelatihan',
            'type'                  => 'nullable|in:ringan,sedang,berat',
            'description'           => 'nullable|string',
            'occurred_at'           => 'nullable|date',

            'no_phone'              => 'nullable',
            'no_phone_until'        => 'nullable|date',

            'no_permission'         => 'nullable',
            'no_permission_until'   => 'nullable|date',

            'attendance_percentage' => 'nullable|integer|min:1|max:100',
            'attendance_until'      => 'nullable|date',
        ]);

        $violation->update([
            'student_id'            => $request->student_id,
            'handling_type'         => $request->category,
            'type'                  => $request->type,
            'description'           => $request->description,
            'occurred_at'           => $request->occurred_at,

            'no_phone'              => $request->no_phone == "on" ? true : false,
            'no_phone_until'        => $request->no_phone_until,

            'no_permission'         => $request->no_permission == "on" ? true : false,
            'no_permission_until'   => $request->no_permission_until,

            'attendance_percentage' => $request->attendance_percentage,
            'attendance_until'      => $request->attendance_until,
        ]);

        return redirect()->back()->with('success', 'Pelanggaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        StudentViolation::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Pelanggaran berhasil dihapus');
    }
}
