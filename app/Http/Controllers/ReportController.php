<?php

namespace App\Http\Controllers;

use App\Models\StudentPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date
            ? now()->parse($request->start_date)->startOfDay()
            : now()->startOfMonth();

        $endDate = $request->end_date
            ? now()->parse($request->end_date)->endOfDay()
            : now()->endOfMonth();

        /* ======================
     * IZIN + CHECK-IN
     * ====================== */
        $permissions = StudentPermission::with([
            'student.class',
            'checkin'
        ])
            ->where('status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate])
            ->whereHas('checkin', fn($q) => $q->whereNotNull('checkin_at'))
            ->get();

        $latePermissions = $permissions->filter(
            fn($p) => $p->checkin->status === 'TERLAMBAT'
        );

        $totalIzin = StudentPermission::with([
            'student.class',
            'checkin'
        ])
            ->where('status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate])
            ->whereHas('checkin', fn($q) => $q->whereNotNull('checkout_at'))
            ->get();

        /* ======================
     * PELANGGARAN
     * ====================== */
        $violations = DB::table('student_violations')
            ->join('students', 'students.id', '=', 'student_violations.student_id')
            ->join('classes', 'classes.id', '=', 'students.class_id')
            ->select(
                'students.name',
                'classes.name as class_name',
                'student_violations.handling_type',
                'student_violations.occurred_at',
                'student_violations.description'
            )
            ->whereBetween('student_violations.created_at', [$startDate, $endDate])
            ->orderBy('student_violations.occurred_at', 'desc')
            ->get();

        /* ======================
     * SUMMARY CARD
     * ====================== */
        $summary = [
            'total_permission' => $permissions->count(),
            'total_late'       => $latePermissions->count(),
            'total_violation'  => $violations->count(),
            'total_izin'       => $totalIzin->count(),
        ];

        return view('reports.index', compact(
            'permissions',
            'latePermissions',
            'violations',
            'summary',
            'startDate',
            'endDate'
        ));
    }
}
