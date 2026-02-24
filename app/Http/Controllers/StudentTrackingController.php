<?php

namespace App\Http\Controllers;

use App\Models\StudentPermissionCheckin;
use Illuminate\Http\Request;

class StudentTrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function tracking(Request $request)
    {
        $request->validate(['nis' => 'required']);
        $nis = $request->nis;

        $checkins = StudentPermissionCheckin::with('permission.student.class')
            ->whereHas('permission.student', function ($q) use ($nis) {
                $q->where('nis', $nis);
            })
            ->orderBy('checkout_at', 'desc')
            ->get();

        if ($checkins->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $history = $checkins->map(function ($checkin) {
            $permission = $checkin->permission;

            $start    = \Carbon\Carbon::parse($permission->start_at)->startOfDay();
            $end      = \Carbon\Carbon::parse($permission->end_at)->startOfDay();
            $duration = $start->diffInDays($end) + 1;

            return [
                'nama'        => $permission->student->name,
                'nis'         => $permission->student->nis,
                'kelas'       => $permission->student->class->name,
                'checkout_at' => $checkin->checkout_at ? $checkin->checkout_at->format('d M Y H:i') : null,
                'checkin_at'  => $checkin->checkin_at  ? $checkin->checkin_at->format('d M Y H:i')  : null,
                'start_at'    => $permission->start_at->format('d M Y'),
                'end_at'      => $permission->end_at->format('d M Y'),
                'duration'    => $duration,
                'status'      => $checkin->status,
                'type'        => $permission->type,
                'reason'      => $permission->reason,
            ];
        });

        return response()->json(['success' => true, 'data' => $history]);
    }
}
