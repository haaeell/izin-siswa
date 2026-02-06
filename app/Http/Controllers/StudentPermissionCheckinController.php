<?php

namespace App\Http\Controllers;

use App\Models\StudentPermission;
use App\Models\StudentPermissionCheckin;
use Illuminate\Http\Request;

class StudentPermissionCheckinController extends Controller
{
    /* ===================== VIEW ===================== */

    public function checkinView()
    {
        // Sudah kembali (check-in)
        $checkins = StudentPermissionCheckin::with('permission.student.class')
            ->whereNotNull('checkin_at')
            ->latest('checkin_at')
            ->get();

        return view('checkin.checkin', compact('checkins'));
    }

    public function checkoutView()
    {
        // Masih di luar
        $checkins = StudentPermissionCheckin::with('permission.student.class')
            ->whereNotNull('checkout_at')
            ->whereNull('checkin_at')
            ->latest('checkout_at')
            ->get();

        return view('checkin.checkout', compact('checkins'));
    }

    /* ===================== CHECK-OUT ===================== */
    // SISWA KELUAR SEKOLAH

    public function checkout(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $permission = $this->findPermission($request->code);

        if (!$permission) {
            return $this->error('Data siswa / izin tidak ditemukan');
        }

        // Sudah checkout
        if ($permission->checkin && $permission->checkin->checkout_at) {
            return $this->error('Siswa sudah keluar');
        }

        // Create / Update record keluar
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

    /* ===================== CHECK-IN ===================== */
    // SISWA KEMBALI KE SEKOLAH

    public function checkin(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $permission = $this->findPermission($request->code);

        if (!$permission || !$permission->checkin) {
            return $this->error('Siswa belum melakukan check-out');
        }

        // Sudah check-in
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

    /* ===================== HELPER ===================== */

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
            'nis' => 'required', // bisa juga ganti 'nis' dengan 'student_id' sesuai kebutuhan
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
