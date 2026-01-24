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

        $classes = SchoolClass::orderBy('name')->get();

        $violations = StudentViolation::with(['student.class'])
            ->when($user->role === 'wali_kelas', function ($q) use ($user) {
                $q->whereHas('student', function ($s) use ($user) {
                    $s->where('class_id', $user->class->id);
                });
            })
            ->when($request->class_id && $user->role !== 'wali_kelas', function ($q) use ($request) {
                $q->whereHas('student', function ($s) use ($request) {
                    $s->where('class_id', $request->class_id);
                });
            })
            ->latest()
            ->get();

        $students = Student::when($user->role === 'wali_kelas', function ($q) use ($user) {
            $q->where('class_id', $user->class->id);
        })
            ->orderBy('name')
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
            'student_id'   => 'required|exists:students,id',
            'type'         => 'required|in:ringan,sedang,berat',
            'description'  => 'required',
            'occurred_at' => 'required|date',
            'until'        => 'required|date',
        ], [
            'student_id.required' => 'Siswa wajib dipilih',
            'type.required'       => 'Jenis pelanggaran wajib dipilih',
            'description.required' => 'Deskripsi wajib diisi',
            'until.required'      => 'Durasi hukuman wajib diisi',
            'occurred_at.required' => 'Tanggal pelanggaran wajib diisi',
        ]);

        StudentViolation::create([
            'student_id'    => $request->student_id,
            'type'          => $request->type,
            'description'   => $request->description,
            'occurred_at'   => $request->occurred_at,
            'no_phone'      => $request->has('no_phone'),
            'no_permission' => $request->has('no_permission'),
            'until'         => $request->until,
            'reported_by'   => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pelanggaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $violation = StudentViolation::findOrFail($id);

        $request->validate([
            'student_id'  => 'required|exists:students,id',
            'type'        => 'required|in:ringan,sedang,berat',
            'description' => 'required',
            'occurred_at'   => 'required|date',
            'until'       => 'required|date',
        ]);

        $violation->update([
            'student_id'    => $request->student_id,
            'type'          => $request->type,
            'description'   => $request->description,
            'occurred_at'   => $request->occurred_at,
            'no_phone'      => $request->has('no_phone'),
            'no_permission' => $request->has('no_permission'),
            'until'         => $request->until,
        ]);

        return redirect()->back()->with('success', 'Pelanggaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        StudentViolation::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Pelanggaran berhasil dihapus');
    }
}
