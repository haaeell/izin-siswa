<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use App\Models\SchoolClass;
use App\Models\StudentPermission;
use App\Models\StudentPermissionCheckin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StudentPermissionCheckinController extends Controller
{
    public function checkinView()
    {
        return view('checkin.checkin');
    }

    public function checkinData(Request $request)
    {
        $query = StudentPermissionCheckin::with([
            'permission.student.class',
            'permission.student.dormitory',
        ])
            ->whereNotNull('checkin_at')
            ->select('student_permission_checkins.*');

        if ($request->start_date) {
            $query->whereDate('checkin_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('checkin_at', '<=', $request->end_date);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nis',      fn($i) => $i->permission->student->nis ?? '-')
            ->addColumn('name',     fn($i) => $i->permission->student->name ?? '-')
            ->addColumn('class',    fn($i) => $i->permission->student->class->name ?? '-')
            ->addColumn('asrama',   fn($i) => $i->permission->student->dormitory->name ?? '-')
            ->addColumn('keperluan', fn($i) => $i->permission->reason ?? '-')
            ->addColumn('checkin_fmt', function ($i) {
                return $i->checkin_at
                    ? '<span class="text-green-600 font-medium whitespace-nowrap">'
                    . Carbon::parse($i->checkin_at)->translatedFormat('l, d F Y H:i')
                    . '</span>'
                    : '<span class="text-slate-400">-</span>';
            })
            ->addColumn('checkout_fmt', function ($i) {
                return $i->checkout_at
                    ? '<span class="text-blue-600 font-medium whitespace-nowrap">'
                    . Carbon::parse($i->checkout_at)->translatedFormat('l, d F Y H:i')
                    . '</span>'
                    : '<span class="text-slate-400">-</span>';
            })
            ->addColumn('gender_badge', function ($p) {
                $g = $p->student->gender ?? null;
                if ($g === 'L') {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Laki Laki</span>';
                }
                if ($g === 'P') {
                    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-pink-100 text-pink-700">Perempuan</span>';
                }
                return '<span class="text-slate-400 text-xs">—</span>';
            })
            ->addColumn('status_badge', function ($i) {
                if ($i->status === 'TERLAMBAT') {
                    $checkin = Carbon::parse($i->checkin_at);
                    $endAt   = Carbon::parse($i->permission->end_at);
                    $diff    = $checkin->diff($endAt);
                    $parts   = [];
                    if ($diff->d > 0) $parts[] = $diff->d . ' hari';
                    if ($diff->h > 0) $parts[] = $diff->h . ' jam';
                    if ($diff->i > 0) $parts[] = $diff->i . ' menit';
                    $label = 'TERLAMBAT' . ($parts ? ' ' . implode(' ', $parts) : '');
                    return '<span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full bg-red-100 text-red-700 whitespace-nowrap">'
                        . $label . '</span>';
                }
                if ($i->status === 'TEPAT WAKTU') {
                    return '<span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 whitespace-nowrap">TEPAT WAKTU</span>';
                }
                if ($i->status === 'DI LUAR') {
                    return '<span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 whitespace-nowrap">PULANG</span>';
                }
                return '<span class="text-slate-400">-</span>';
            })
            ->rawColumns(['checkin_fmt', 'checkout_fmt', 'status_badge'])
            ->make(true);
    }

    public function checkoutView()
    {
        return view('checkin.checkout');
    }

    public function checkoutData()
    {
        $query = StudentPermissionCheckin::with(['permission.student.class'])
            ->whereNotNull('checkout_at')
            ->whereNull('checkin_at')
            ->select('student_permission_checkins.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nis',      fn($i) => $i->permission->student->nis ?? '-')
            ->addColumn('name',     fn($i) => $i->permission->student->name ?? '-')
            ->addColumn('class',    fn($i) => $i->permission->student->class->name ?? '-')
            ->addColumn('keperluan', fn($i) => $i->permission->reason ?? '-')
            ->addColumn('checkin_fmt', function ($i) {
                return $i->checkin_at
                    ? '<span class="text-green-600 font-medium">' . Carbon::parse($i->checkin_at)->format('H:i') . '</span>'
                    : '<span class="text-slate-400">-</span>';
            })
            ->addColumn('checkout_fmt', function ($i) {
                return $i->checkout_at
                    ? '<span class="text-blue-600 font-medium">' . Carbon::parse($i->checkout_at)->format('H:i') . '</span>'
                    : '<span class="text-slate-400">-</span>';
            })
            ->addColumn('status_badge', function ($i) {
                if ($i->checkin_at && !$i->checkout_at) {
                    return '<span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-700 whitespace-nowrap">Selesai</span>';
                }
                return '<span class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 whitespace-nowrap">Di luar</span>';
            })
            ->rawColumns(['checkin_fmt', 'checkout_fmt', 'status_badge'])
            ->make(true);
    }

    public function checkout(Request $request)
    {
        $request->validate(['code' => 'required']);

        $permission = $this->findPermission($request->code);

        if (!$permission) {
            return $this->error('Data siswa / izin tidak ditemukan');
        }

        try {
            $result = DB::transaction(function () use ($permission) {
                $checkin = StudentPermissionCheckin::where('student_permission_id', $permission->id)
                    ->lockForUpdate()->first();

                if ($checkin && $checkin->checkout_at) {
                    return ['error' => 'Siswa sudah melakukan check-out'];
                }

                StudentPermissionCheckin::updateOrCreate(
                    ['student_permission_id' => $permission->id],
                    ['checkout_at' => now(), 'checkin_at' => null, 'status' => 'DI LUAR']
                );

                return ['success' => true];
            });

            if (isset($result['error'])) return $this->error($result['error']);

            return $this->success('Check-out berhasil', $permission);
        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan, silakan scan ulang');
        }
    }

    public function checkin(Request $request)
    {
        $request->validate(['code' => 'required']);

        $permission = $this->findPermission($request->code);

        if (!$permission) {
            return $this->error('Data siswa / izin tidak ditemukan');
        }

        try {
            $result = DB::transaction(function () use ($permission) {
                $checkin = StudentPermissionCheckin::where('student_permission_id', $permission->id)
                    ->lockForUpdate()->first();

                if (!$checkin || !$checkin->checkout_at) return ['error' => 'Siswa belum melakukan check-out'];
                if ($checkin->checkin_at) return ['error' => 'Siswa sudah check-in'];

                $now    = now();
                $status = $now->lte($permission->end_at) ? 'TEPAT WAKTU' : 'TERLAMBAT';

                $checkin->update(['checkin_at' => $now, 'status' => $status]);

                return ['success' => true, 'status' => $status];
            });

            if (isset($result['error'])) return $this->error($result['error']);

            return $this->success('Check-in berhasil', $permission->fresh(), $result['status']);
        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan, silakan scan ulang');
        }
    }

    private function findPermission(string $code): ?StudentPermission
    {
        return StudentPermission::with(['student.class', 'checkin'])
            ->where('status', 'approved')
            ->where(function ($q) use ($code) {
                $q->where('qr_token', $code)
                    ->orWhereHas('student', fn($s) => $s->where('nis', $code));
            })
            ->first();
    }

    private function success(string $message, StudentPermission $permission, string $status = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => [
                'nama'   => $permission->student->name,
                'kelas'  => $permission->student->class->name,
                'waktu'  => now()->format('d M Y H:i'),
                'status' => $status,
            ],
        ]);
    }

    private function error(string $message)
    {
        return response()->json(['success' => false, 'message' => $message]);
    }

    public function tracking(Request $request)
    {
        $request->validate(['nis' => 'required']);

        $checkins = StudentPermissionCheckin::with('permission.student.class')
            ->whereHas('permission.student', fn($q) => $q->where('nis', $request->nis))
            ->orderBy('checkout_at', 'desc')
            ->get();

        if ($checkins->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data riwayat siswa tidak ditemukan']);
        }

        return response()->json([
            'success' => true,
            'data'    => $checkins->map(fn($c) => [
                'nama'        => $c->permission->student->name,
                'kelas'       => $c->permission->student->class->name,
                'checkout_at' => $c->checkout_at?->format('d M Y H:i'),
                'checkin_at'  => $c->checkin_at?->format('d M Y H:i'),
                'status'      => $c->status,
            ]),
        ]);
    }
}
