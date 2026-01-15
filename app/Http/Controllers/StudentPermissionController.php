<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentPermissionController extends Controller
{
    public function index()
    {
        return view('permissions.index', [
            'permissions' => StudentPermission::with(['student', 'waliKelas'])->latest()->get()
        ]);
    }

    public function create()
    {
        return view('permissions.create', [
            'students' => Student::with('class')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type'       => 'required',
            'start_at'   => 'required|date',
            'end_at'     => 'required|date|after:start_at',
            'reason'     => 'required',
        ]);

        StudentPermission::create([
            'student_id'    => $request->student_id,
            'wali_kelas_id' => Auth::id(),
            'type'          => $request->type,
            'start_at'      => $request->start_at,
            'end_at'        => $request->end_at,
            'reason'        => $request->reason,
            'status'        => 'pending',
            'qr_token'      => Str::uuid(),
        ]);

        return redirect()->route('permissions.index')->with('success', 'Pengajuan izin berhasil dibuat');
    }

    public function show($id)
    {
        return view('permissions.show', [
            'permission' => StudentPermission::with(['student.class', 'checkins'])->findOrFail($id)
        ]);
    }
}
