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
        $request->validate([
            'nis' => 'required'
        ]);

        $nis = $request->nis;

        $checkins = StudentPermissionCheckin::with('permission.student.class')
            ->whereHas('permission.student', function ($q) use ($nis) {
                $q->where('nis', $nis);
            })
            ->orderBy('checkout_at', 'desc')
            ->get();

        if ($checkins->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat siswa tidak ditemukan'
            ]);
        }

        $history = $checkins->map(function ($checkin) {
            return [
                'nama'        => $checkin->permission->student->name,
                'kelas'       => $checkin->permission->student->class->name,
                'checkout_at' => $checkin->checkout_at ? $checkin->checkout_at->format('d M Y H:i') : null,
                'checkin_at'  => $checkin->checkin_at ? $checkin->checkin_at->format('d M Y H:i') : null,
                'status'      => $checkin->status,
                'type'        => $checkin->permission->type,
                'reason'      => $checkin->permission->reason,
                'nis'         => $checkin->permission->student->nis,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $history
        ]);
    }
}
