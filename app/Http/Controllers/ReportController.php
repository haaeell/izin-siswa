<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use App\Models\SchoolClass;
use App\Models\StudentPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate   = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate     = $request->end_date   ? Carbon::parse($request->end_date)->endOfDay()     : now()->endOfMonth();
        $classId = $this->getClassIdForUser($request);
        $dormitoryId = $request->dormitory_id;
        $jamMulai    = $request->jam_mulai ?? '00:00';
        $jamAkhir    = $request->jam_akhir ?? '23:59';
        $gender      = $request->gender;

        // ── Summary: Total Izin (sudah checkout) ────────────────────
        $totalIzinQuery = StudentPermission::where('status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate])
            ->whereHas('checkin', fn($q) => $q->whereNotNull('checkout_at'));
        $this->applyStudentFilters($totalIzinQuery, $classId, $dormitoryId, $gender);

        // ── Summary: Terlambat ───────────────────────────────────────
        $lateQuery = StudentPermission::where('status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate])
            ->whereHas('checkin', fn($q) => $q->whereNotNull('checkin_at')->where('status', 'TERLAMBAT'));
        $this->applyStudentFilters($lateQuery, $classId, $dormitoryId, $gender);

        // ── Summary: Pelanggaran ─────────────────────────────────────
        $violationCount = DB::table('student_violations')
            ->join('students', 'students.id', '=', 'student_violations.student_id')
            ->whereBetween('student_violations.created_at', [$startDate, $endDate])
            ->when($classId,     fn($q) => $q->where('students.class_id', $classId))
            ->when($dormitoryId, fn($q) => $q->where('students.dormitory_id', $dormitoryId))
            ->when($gender,      fn($q) => $q->where('students.gender', $gender))
            ->count();

        // ── Summary: Check-in ────────────────────────────────────────
        $semuaIzinQuery = StudentPermission::with(['student', 'checkin'])
            ->where('status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate]);
        $this->applyStudentFilters($semuaIzinQuery, $classId, $dormitoryId, $gender);
        $semuaIzin = $semuaIzinQuery->get();

        $sudahCheckin = $semuaIzin->filter(function ($p) use ($jamMulai, $jamAkhir) {
            if (!$p->checkin || !$p->checkin->checkin_at) return false;
            $waktu = Carbon::parse($p->checkin->checkin_at)->format('H:i');
            return $waktu >= $jamMulai && $waktu <= $jamAkhir;
        });
        $belumCheckin = $semuaIzin->filter(fn($p) => !$p->checkin || !$p->checkin->checkin_at);

        $checkinSummary = [
            'sudah_l' => $sudahCheckin->filter(fn($p) => $p->student?->gender === 'L')->count(),
            'sudah_p' => $sudahCheckin->filter(fn($p) => $p->student?->gender === 'P')->count(),
            'belum_l' => $belumCheckin->filter(fn($p) => $p->student?->gender === 'L')->count(),
            'belum_p' => $belumCheckin->filter(fn($p) => $p->student?->gender === 'P')->count(),
            'total'   => $semuaIzin->count(),
        ];

        $summary = [
            'total_izin'      => $totalIzinQuery->count(),
            'total_late'      => $lateQuery->count(),
            'total_violation' => $violationCount,
        ];

        $user = Auth::user();
        if ($user->role === 'wali_kelas') {
            $classes = SchoolClass::where('id', $user->class->id)->get();
        } else {
            $classes = SchoolClass::orderBy('name')->get();
        }
        $dormitories = Dormitory::orderBy('name')->get();

        return view('reports.index', compact(
            'summary',
            'checkinSummary',
            'startDate',
            'endDate',
            'jamMulai',
            'jamAkhir',
            'classes',
            'dormitories'
        ));
    }

    public function dataCheckin(Request $request)
    {
        $startDate   = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate     = $request->end_date   ? Carbon::parse($request->end_date)->endOfDay()     : now()->endOfMonth();
        $classId = $this->getClassIdForUser($request);
        $dormitoryId = $request->dormitory_id;
        $jamMulai    = $request->jam_mulai ?? '00:00';
        $jamAkhir    = $request->jam_akhir ?? '23:59';
        $gender      = $request->gender;

        $query = StudentPermission::with(['student.class', 'student.dormitory', 'checkin'])
            ->where('status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate]);

        $this->applyStudentFilters($query, $classId, $dormitoryId, $gender);

        $records = $query->get()->filter(function ($p) use ($jamMulai, $jamAkhir) {
            // tampilkan semua: sudah checkin dalam rentang jam + belum checkin
            if (!$p->checkin || !$p->checkin->checkin_at) return true;
            $waktu = Carbon::parse($p->checkin->checkin_at)->format('H:i');
            return $waktu >= $jamMulai && $waktu <= $jamAkhir;
        })->values();

        return DataTables::of($records)
            ->addIndexColumn()
            ->addColumn('nis', fn($p) => $p->student?->nis ?? '-')
            ->addColumn('nama', fn($p) => $p->student?->name ?? '-')
            ->addColumn('gender_badge', function ($p) {
                $g = $p->student?->gender;
                $baseClass = "inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold whitespace-nowrap shadow-sm border";

                if ($g === 'L') {
                    return '<span class="' . $baseClass . ' bg-sky-50 text-sky-700 border-sky-200" title="Laki-laki">L</span>';
                }
                if ($g === 'P') {
                    return '<span class="' . $baseClass . ' bg-rose-50 text-rose-700 border-rose-200" title="Perempuan">P</span>';
                }

                return '<span class="text-slate-300">—</span>';
            })
            ->addColumn('kelas', fn($p) => $p->student?->class?->name ?? '-')
            ->addColumn('alasan', fn($p) => $p->reason ?? '-')
            ->addColumn('keperluan', function ($p) {
                $typeMap = ['sakit' => 'Sakit', 'pulang' => 'Pulang', 'pesiar' => 'Pesiar', 'perpulangan' => 'Perpulangan'];
                $label   = $typeMap[$p->type] ?? ucfirst($p->type);
                $bg      = match ($p->type) {
                    'sakit'       => 'bg-red-100 text-red-700',
                    'pulang'      => 'bg-orange-100 text-orange-700',
                    'pesiar'      => 'bg-purple-100 text-purple-700',
                    'perpulangan' => 'bg-yellow-100 text-yellow-700',
                    default       => 'bg-slate-100 text-slate-700',
                };
                return "<span class=\"text-[11px] px-2 py-0.5 rounded-full font-medium {$bg}\">{$label}</span>";
            })
            ->addColumn('checkin_at', function ($p) {
                return $p->checkin?->checkin_at
                    ? Carbon::parse($p->checkin->checkin_at)->format('d/m/Y H:i')
                    : '—';
            })
            ->addColumn('checkout_at', function ($p) {
                return $p->checkin?->checkout_at
                    ? Carbon::parse($p->checkin->checkout_at)->format('d/m/Y H:i')
                    : '—';
            })
            ->addColumn('status_badge', function ($p) {
                $sudah   = $p->checkin && $p->checkin->checkin_at;
                $kembali = $sudah && $p->checkin->checkout_at;

                $baseClass = "inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wide whitespace-nowrap border shadow-sm";

                if ($kembali) {
                    return '<span class="' . $baseClass . ' bg-emerald-50 text-emerald-700 border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Sudah Kembali
                            </span>';
                }

                if ($sudah) {
                    return '<span class="' . $baseClass . ' bg-indigo-50 text-indigo-700 border-indigo-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                Sudah Check-in
                            </span>';
                }

                return '<span class="' . $baseClass . ' bg-amber-50 text-amber-700 border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Belum Check-in
                        </span>';
            })
            ->rawColumns(['status_badge'])
            ->rawColumns(['gender_badge', 'keperluan', 'status_badge'])
            ->make(true);
    }

    public function dataTerlambat(Request $request)
    {
        $startDate   = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate     = $request->end_date   ? Carbon::parse($request->end_date)->endOfDay()     : now()->endOfMonth();
        $classId = $this->getClassIdForUser($request);
        $dormitoryId = $request->dormitory_id;
        $gender      = $request->gender;

        $query = StudentPermission::with(['student.class', 'checkin'])
            ->where('status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate])
            ->whereHas('checkin', fn($q) => $q->whereNotNull('checkin_at')->where('status', 'TERLAMBAT'));

        $this->applyStudentFilters($query, $classId, $dormitoryId, $gender);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nis',  fn($p) => $p->student?->nis ?? '-')
            ->addColumn('nama', fn($p) => $p->student?->name ?? '-')
            ->addColumn('kelas', fn($p) => $p->student?->class?->name ?? '-')
            ->addColumn('waktu_datang', function ($p) {
                return $p->checkin?->checkin_at
                    ? Carbon::parse($p->checkin->checkin_at)->format('d/m/Y H:i')
                    : '-';
            })
            ->addColumn('durasi_terlambat', function ($p) {
                if (!$p->checkin?->checkin_at || !$p->end_at) return '-';
                $diff   = Carbon::parse($p->end_at)->diff(Carbon::parse($p->checkin->checkin_at));
                $parts  = [];
                if ($diff->d > 0) $parts[] = $diff->d . ' hari';
                if ($diff->h > 0) $parts[] = $diff->h . ' jam';
                if ($diff->i > 0) $parts[] = $diff->i . ' menit';
                $text = implode(' ', $parts) ?: '< 1 menit';
                return "<span class=\"px-3 py-1 text-xs rounded-full bg-red-100 text-red-700\">{$text}</span>";
            })
            ->addColumn('lampiran', function ($p) {
                if ($p->surat_terlambat) {
                    $url = asset('storage/' . $p->surat_terlambat);
                    return "<a href=\"{$url}\" target=\"_blank\"
                                class=\"inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-100 text-orange-700 text-xs font-medium rounded-lg hover:bg-orange-200 transition\">
                                <i class=\"fa-solid fa-file-lines\"></i> Lihat
                            </a>";
                }
                return '<span class="text-slate-400 text-xs">Tidak Ada</span>';
            })
            ->rawColumns(['durasi_terlambat', 'lampiran'])
            ->make(true);
    }

    public function dataPelanggaran(Request $request)
    {
        $startDate   = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate     = $request->end_date   ? Carbon::parse($request->end_date)->endOfDay()     : now()->endOfMonth();
        $classId = $this->getClassIdForUser($request);
        $dormitoryId = $request->dormitory_id;
        $gender      = $request->gender;

        $query = DB::table('student_violations')
            ->join('students',    'students.id',    '=', 'student_violations.student_id')
            ->join('classes',     'classes.id',     '=', 'students.class_id')
            ->leftJoin('dormitories', 'dormitories.id', '=', 'students.dormitory_id')
            ->select(
                'students.name',
                'students.nis',
                'students.gender',
                'classes.name as class_name',
                'dormitories.name as dormitory_name',
                'student_violations.handling_type',
                'student_violations.occurred_at',
                'student_violations.description',
                'student_violations.attendance_percentage'
            )
            ->whereBetween('student_violations.created_at', [$startDate, $endDate])
            ->when($classId,     fn($q) => $q->where('students.class_id', $classId))
            ->when($dormitoryId, fn($q) => $q->where('students.dormitory_id', $dormitoryId))
            ->when($gender,      fn($q) => $q->where('students.gender', $gender))
            ->orderBy('student_violations.occurred_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('jenis', function ($v) {
                return "<span class=\"px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-700 uppercase border border-amber-200\">"
                    . e($v->handling_type) . "</span>";
            })
            ->addColumn('deskripsi', function ($v) {
                return $v->description ?: 'Absensi ' . $v->attendance_percentage . '%';
            })
            ->addColumn('tanggal', fn($v) => Carbon::parse($v->occurred_at)->format('d/m/Y'))
            ->rawColumns(['jenis'])
            ->make(true);
    }

    public function exportPdf(Request $request)
    {
        $startDate   = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate     = $request->end_date   ? Carbon::parse($request->end_date)->endOfDay()     : now()->endOfMonth();
        $classId = $this->getClassIdForUser($request);
        $dormitoryId = $request->dormitory_id;
        $jamMulai    = $request->jam_mulai ?? '00:00';
        $jamAkhir    = $request->jam_akhir ?? '23:59';
        $gender      = $request->gender;

        $semuaIzinQuery = StudentPermission::with(['student.class', 'student.dormitory', 'checkin'])
            ->where('status', 'approved')
            ->whereBetween('start_at', [$startDate, $endDate]);
        $this->applyStudentFilters($semuaIzinQuery, $classId, $dormitoryId, $gender);
        $semuaIzin = $semuaIzinQuery->get();

        $sudahCheckin = $semuaIzin->filter(function ($p) use ($jamMulai, $jamAkhir) {
            if (!$p->checkin || !$p->checkin->checkin_at) return false;
            $waktu = Carbon::parse($p->checkin->checkin_at)->format('H:i');
            return $waktu >= $jamMulai && $waktu <= $jamAkhir;
        });
        $belumCheckin = $semuaIzin->filter(fn($p) => !$p->checkin || !$p->checkin->checkin_at);
        $records      = $sudahCheckin->merge($belumCheckin)->sortBy('student.class.name');

        $summary = [
            'sudah_l' => $sudahCheckin->filter(fn($p) => $p->student?->gender === 'L')->count(),
            'sudah_p' => $sudahCheckin->filter(fn($p) => $p->student?->gender === 'P')->count(),
            'belum_l' => $belumCheckin->filter(fn($p) => $p->student?->gender === 'L')->count(),
            'belum_p' => $belumCheckin->filter(fn($p) => $p->student?->gender === 'P')->count(),
        ];

        $html = view('checkin.pdf', compact(
            'records',
            'summary',
            'request',
            'startDate',
            'endDate',
            'jamMulai',
            'jamAkhir'
        ))->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHtml($html)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'Arial',
            ]);

        return $pdf->download('laporan-checkin-' . now()->format('Ymd-His') . '.pdf');
    }

    private function applyStudentFilters($query, $classId, $dormitoryId, $gender): void
    {
        if ($classId)     $query->whereHas('student', fn($q) => $q->where('class_id', $classId));
        if ($dormitoryId) $query->whereHas('student', fn($q) => $q->where('dormitory_id', $dormitoryId));
        if ($gender)      $query->whereHas('student', fn($q) => $q->where('gender', $gender));
    }

    private function getClassIdForUser(Request $request): ?int
    {
        $user = auth()->user();

        if ($user->role === 'wali_kelas') {
            return $user->class->id;
        }

        return $request->class_id ?: null;
    }
}
