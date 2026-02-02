<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolClass;
use App\Models\Dormitory;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date
            ?? Carbon::now()->startOfMonth()->toDateString();

        $end = $request->end_date
            ?? Carbon::now()->endOfMonth()->toDateString();

        $classId = $request->class_id;
        $dormitoryId = $request->dormitory_id;

        $classes = SchoolClass::all();
        $dormitories = Dormitory::all();

        /* =======================
         * SUMMARY
         * ======================= */
        $summary = [
            'total_permission' => DB::table('student_permissions')
                ->join('students', 'students.id', '=', 'student_permissions.student_id')
                ->when($start, fn($q) => $q->whereBetween('start_at', [$start, $end]))
                ->when($classId, fn($q) => $q->where('students.class_id', $classId))
                ->when($dormitoryId, fn($q) => $q->where('students.dormitory_id', $dormitoryId))
                ->count(),

            'late_checkin' => DB::table('student_permission_checkins')
                ->join('student_permissions', 'student_permissions.id', '=', 'student_permission_checkins.student_permission_id')
                ->join('students', 'students.id', '=', 'student_permissions.student_id')
                ->where('student_permission_checkins.status', 'TERLAMBAT')
                ->when($start, fn($q) => $q->whereBetween('checkin_at', [$start, $end]))
                ->when($classId, fn($q) => $q->where('students.class_id', $classId))
                ->when($dormitoryId, fn($q) => $q->where('students.dormitory_id', $dormitoryId))
                ->count(),

            'total_violation' => DB::table('student_violations')
                ->join('students', 'students.id', '=', 'student_violations.student_id')
                ->when($start, fn($q) => $q->whereBetween('occurred_at', [$start, $end]))
                ->when($classId, fn($q) => $q->where('students.class_id', $classId))
                ->when($dormitoryId, fn($q) => $q->where('students.dormitory_id', $dormitoryId))
                ->count(),

            'heavy_violation' => DB::table('student_violations')
                ->join('students', 'students.id', '=', 'student_violations.student_id')
                ->where('type', 'berat')
                ->when($start, fn($q) => $q->whereBetween('occurred_at', [$start, $end]))
                ->when($classId, fn($q) => $q->where('students.class_id', $classId))
                ->when($dormitoryId, fn($q) => $q->where('students.dormitory_id', $dormitoryId))
                ->count(),
        ];

        /* =======================
         * TABLE
         * ======================= */
        $rows = DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('dormitories', 'students.dormitory_id', '=', 'dormitories.id')
            ->select(
                'students.id',
                'students.name',
                'classes.name as class_name',
                'dormitories.name as dormitory_name',

                DB::raw('(SELECT COUNT(*) FROM student_permissions
                    WHERE student_id = students.id) as permission_count'),

                DB::raw('(SELECT COUNT(*) FROM student_permission_checkins
                    JOIN student_permissions sp
                        ON sp.id = student_permission_checkins.student_permission_id
                    WHERE sp.student_id = students.id
                    AND student_permission_checkins.status = "TERLAMBAT") as late_count'),

                DB::raw('(SELECT COUNT(*) FROM student_violations
                    WHERE student_id = students.id AND type = "ringan") as light'),

                DB::raw('(SELECT COUNT(*) FROM student_violations
                    WHERE student_id = students.id AND type = "sedang") as medium'),

                DB::raw('(SELECT COUNT(*) FROM student_violations
                    WHERE student_id = students.id AND type = "berat") as heavy')
            )
            ->when($classId, fn($q) => $q->where('students.class_id', $classId))
            ->when($dormitoryId, fn($q) => $q->where('students.dormitory_id', $dormitoryId))
            ->get();

        return view('reports.index', compact(
            'classes',
            'dormitories',
            'summary',
            'rows'
        ));
    }

    /* =======================
     * EXPORT PDF
     * ======================= */
    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);

        $pdf = FacadePdf::loadView('reports.pdf', $data)
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-siswa.pdf');
    }

    /* =======================
     * EXPORT EXCEL
     * ======================= */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new ReportExport($request),
            'laporan-siswa.xlsx'
        );
    }

    /* =======================
     * SHARED DATA (PDF & EXCEL)
     * ======================= */
    private function getReportData(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        $classId = $request->class_id;
        $dormitoryId = $request->dormitory_id;

        $rows = DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('dormitories', 'students.dormitory_id', '=', 'dormitories.id')
            ->select(
                'students.name',
                'classes.name as class',
                'dormitories.name as dormitory',

                DB::raw('(SELECT COUNT(*) FROM student_permissions
                    WHERE student_id = students.id) as izin'),

                DB::raw('(SELECT COUNT(*) FROM student_permission_checkins
                    JOIN student_permissions sp
                        ON sp.id = student_permission_checkins.student_permission_id
                    WHERE sp.student_id = students.id
                    AND status="TERLAMBAT") as terlambat'),

                DB::raw('(SELECT COUNT(*) FROM student_violations
                    WHERE student_id = students.id AND type="ringan") as ringan'),

                DB::raw('(SELECT COUNT(*) FROM student_violations
                    WHERE student_id = students.id AND type="sedang") as sedang'),

                DB::raw('(SELECT COUNT(*) FROM student_violations
                    WHERE student_id = students.id AND type="berat") as berat')
            )
            ->when($classId, fn($q) => $q->where('students.class_id', $classId))
            ->when($dormitoryId, fn($q) => $q->where('students.dormitory_id', $dormitoryId))
            ->get();

        return compact('rows', 'start', 'end');
    }
}
