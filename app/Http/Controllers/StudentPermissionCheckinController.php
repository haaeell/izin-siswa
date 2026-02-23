<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use App\Models\SchoolClass;
use App\Models\StudentPermission;
use App\Models\StudentPermissionCheckin;
use Illuminate\Http\Request;

class StudentPermissionCheckinController extends Controller
{

    public function checkinView(Request $request)
    {
        $query = StudentPermissionCheckin::with('permission.student.class', 'permission.student.dormitory')
            ->whereNotNull('checkin_at');

        if ($request->start_date) {
            $query->whereDate('checkin_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('checkin_at', '<=', $request->end_date);
        }
        if ($request->class_id) {
            $query->whereHas('permission.student', fn($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->dormitory_id) {
            $query->whereHas('permission.student', fn($q) => $q->where('dormitory_id', $request->dormitory_id));
        }

        $checkins = $query->latest('checkin_at')->get();
        $classes = SchoolClass::all();
        $dormitories = Dormitory::all();

        return view('checkin.checkin', compact('checkins', 'classes', 'dormitories'));
    }


    public function checkoutView()
    {
        $checkins = StudentPermissionCheckin::with('permission.student.class')
            ->whereNotNull('checkout_at')
            ->whereNull('checkin_at')
            ->latest('checkout_at')
            ->get();

        return view('checkin.checkout', compact('checkins'));
    }
    public function checkout(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $permission = $this->findPermission($request->code);

        if (!$permission) {
            return $this->error('Data siswa / izin tidak ditemukan');
        }

        if ($permission->checkin && $permission->checkin->checkout_at) {
            return $this->error('Siswa sudah keluar');
        }

        StudentPermissionCheckin::updateOrCreate(
            ['student_permission_id' => $permission->id],
            [
                'checkout_at' => now(),
                'checkin_at'  => null,
                'status'      => 'DI LUAR'
            ]
        );

        return $this->success(
            'Check-out berhasil',
            $permission
        );
    }

    public function checkin(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $permission = $this->findPermission($request->code);

        if (!$permission || !$permission->checkin) {
            return $this->error('Siswa belum melakukan check-out');
        }

        if ($permission->checkin->checkin_at) {
            return $this->error('Siswa sudah check-in');
        }

        $now = now();

        $status = $now->lte($permission->end_at)
            ? 'TEPAT WAKTU'
            : 'TERLAMBAT';

        $permission->checkin->update([
            'checkin_at' => $now,
            'status'     => $status
        ]);

        return $this->success(
            'Check-in berhasil',
            $permission,
            $status
        );
    }

    private function findPermission(string $code): ?StudentPermission
    {
        return StudentPermission::with(['student.class', 'checkin'])
            ->where('status', 'approved')
            ->where(function ($q) use ($code) {
                $q->where('qr_token', $code)
                    ->orWhereHas(
                        'student',
                        fn($s) =>
                        $s->where('nis', $code)
                    );
            })
            ->first();
    }

    private function success(string $message, StudentPermission $permission, string $status = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'nama'   => $permission->student->name,
                'kelas'  => $permission->student->class->name,
                'waktu'  => now()->format('d M Y H:i'),
                'status' => $status
            ]
        ]);
    }

    private function error(string $message)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ]);
    }

    public function tracking(Request $request)
    {
        $request->validate([
            'nis' => 'required',
        ]);

        $studentNis = $request->nis;

        $checkins = StudentPermissionCheckin::with('permission.student.class')
            ->whereHas('permission.student', function ($q) use ($studentNis) {
                $q->where('nis', $studentNis);
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
                'nama'         => $checkin->permission->student->name,
                'kelas'        => $checkin->permission->student->class->name,
                'checkout_at'  => $checkin->checkout_at ? $checkin->checkout_at->format('d M Y H:i') : null,
                'checkin_at'   => $checkin->checkin_at ? $checkin->checkin_at->format('d M Y H:i') : null,
                'status'       => $checkin->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $history
        ]);
    }
}
