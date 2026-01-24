<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolClass;
use PDF;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;
        $classId = $request->class_id;

        $classes = SchoolClass::all();

        /** SUMMARY */
        $summary = [
            'total_permission' => DB::table('student_permissions')
                ->when($start, fn($q) => $q->whereBetween('start_at', [$start, $end]))
                ->count(),

            'late_checkin' => DB::table('student_permission_checkins')
                ->where('status', 'TERLAMBAT')
                ->when($start, fn($q) => $q->whereBetween('checkin_at', [$start, $end]))
                ->count(),

            'total_violation' => DB::table('student_violations')
                ->when($start, fn($q) => $q->whereBetween('occurred_at', [$start, $end]))
                ->count(),

            'heavy_violation' => DB::table('student_violations')
                ->where('type', 'berat')
                ->when($start, fn($q) => $q->whereBetween('occurred_at', [$start, $end]))
                ->count(),
        ];

        /** TABLE */
        $rows = DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->select(
                'students.id',
                'students.name',
                'classes.name as class_name',

                DB::raw('(SELECT COUNT(*) FROM student_permissions 
                    WHERE student_id = students.id) as permission_count'),

                DB::raw('(SELECT COUNT(*) FROM student_permission_checkins 
                    JOIN student_permissions sp ON sp.id = student_permission_checkins.student_permission_id
                    WHERE sp.student_id = students.id 
                    AND student_permission_checkins.status = "TERLAMBAT") as late_count'),

                DB::raw('(SELECT COUNT(*) FROM student_violations 
                    WHERE student_id = students.id AND type = "ringan") as light'),

                DB::raw('(SELECT COUNT(*) FROM student_violations 
                    WHERE student_id = students.id AND type = "sedang") as medium'),

                DB::raw('(SELECT COUNT(*) FROM student_violations 
                    WHERE student_id = students.id AND type = "berat") as heavy')
            )
            ->when($classId, fn($q) => $q->where('classes.id', $classId))
            ->get();

        return view('reports.index', compact('classes', 'summary', 'rows'));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);

        $pdf = FacadePdf::loadView('reports.pdf', $data)->setPaper('A4', 'landscape');
        return $pdf->download('laporan-siswa.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ReportExport($request), 'laporan-siswa.xlsx');
    }

    private function getReportData(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $rows = DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->select(
                'students.name',
                'classes.name as class',
                DB::raw('(SELECT COUNT(*) FROM student_permissions WHERE student_id = students.id) as izin'),
                DB::raw('(SELECT COUNT(*) FROM student_permission_checkins 
                    JOIN student_permissions sp ON sp.id = student_permission_checkins.student_permission_id
                    WHERE sp.student_id = students.id AND status="TERLAMBAT") as terlambat'),
                DB::raw('(SELECT COUNT(*) FROM student_violations WHERE student_id = students.id AND type="ringan") as ringan'),
                DB::raw('(SELECT COUNT(*) FROM student_violations WHERE student_id = students.id AND type="sedang") as sedang'),
                DB::raw('(SELECT COUNT(*) FROM student_violations WHERE student_id = students.id AND type="berat") as berat')
            )
            ->get();

        return compact('rows', 'start', 'end');
    }
}
