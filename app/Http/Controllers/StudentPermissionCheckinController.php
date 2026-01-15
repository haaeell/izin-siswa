<?php

namespace App\Http\Controllers;

use App\Models\StudentPermission;
use App\Models\StudentPermissionCheckin;
use Carbon\Carbon;

class StudentPermissionCheckinController extends Controller
{
    public function scan($qr_token)
    {
        return view('checkin.scan', [
            'permission' => StudentPermission::where('qr_token', $qr_token)
                ->where('status', 'approved')
                ->firstOrFail()
        ]);
    }

    public function store($qr_token)
    {
        $permission = StudentPermission::where('qr_token', $qr_token)
            ->where('status', 'approved')
            ->firstOrFail();

        $now = Carbon::now();

        $status = $now->lte($permission->end_at)
            ? 'tepat_waktu'
            : 'terlambat';

        StudentPermissionCheckin::create([
            'student_permission_id' => $permission->id,
            'checkin_at'            => $now,
            'status'                => $status,
        ]);

        return redirect()->back()->with('success', 'Check-in berhasil');
    }
}
