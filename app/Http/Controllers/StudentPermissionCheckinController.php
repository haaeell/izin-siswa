<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPermission;
use App\Models\StudentPermissionCheckin;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StudentPermissionCheckinController extends Controller
{
    public function index()
    {
        $checkins = StudentPermissionCheckin::with([
            'permission.student',
            'permission.student.class'
        ])->latest()->get();

        $students = StudentPermission::with('student')->where('status', 'approved')->whereDoesntHave('checkin')->get();

        return view('checkin.index', compact('checkins', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'qr_token' => 'required'
        ]);

        $permission = StudentPermission::with('checkin', 'student.class')
            ->where('qr_token', $request->qr_token)
            ->where('status', 'approved')
            ->first();

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'QR tidak valid atau izin belum disetujui'
            ]);
        }

        if ($permission->checkin) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah melakukan check-in'
            ]);
        }

        $status = now()->lte($permission->end_at)
            ? 'TEPAT WAKTU'
            : 'TERLAMBAT';

        $checkin = StudentPermissionCheckin::create([
            'student_permission_id' => $permission->id,
            'checkin_at' => now(),
            'status' => $status
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'nama' => $permission->student->name,
                'kelas' => $permission->student->class->name,
                'waktu' => $checkin->checkin_at->format('d M Y H:i'),
                'status' => $status
            ]
        ]);
    }
    public function manual(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $student = Student::with('class')->findOrFail($request->student_id);
        $already = StudentPermissionCheckin::where('student_permission_id', $student->id)->exists();

        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah check-in'
            ]);
        }

        $now = Carbon::now();
        $batasMasuk = Carbon::today()->setTime(7, 0);

        $status = $now->lte($batasMasuk)
            ? 'TEPAT WAKTU'
            : 'TERLAMBAT';

        $checkin = StudentPermissionCheckin::create([
            'student_permission_id' => $student->id,
            'checkin_at' => $now,
            'status'     => strtolower(str_replace(' ', '_', $status))
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'nama'   => $student->name,
                'kelas'  => $student->class->name,
                'waktu'  => Carbon::parse($checkin->checkin_at)->format('d M Y H:i'),
                'status' => $status
            ]
        ]);
    }
}
