<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentPermission::with(['student.class']);

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
                $c->where('wali_kelas_id', Auth::user()->id)
            )
        )->get();


        return view('permissions.index', [
            'permissions' => $query->latest()->get(),
            'students' => $students
        ]);
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'wali_kelas', 403);

        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type'       => 'required|string',
            'start_at'   => 'required|date',
            'end_at'     => 'required|date|after_or_equal:start_at',
            'reason'     => 'required|string|min:5',
        ]);

        StudentPermission::create([
            ...$data,
            'wali_kelas_id' => Auth::user()->id,
            'status'        => 'pending',
        ]);

        return redirect()->back()->with('success', 'Permohonan izin berhasil diajukan');
    }

    public function show($id)
    {
        $permission = StudentPermission::with('student')->findOrFail($id);

        return view('student_permissions.show', compact('permission'));
    }
}
