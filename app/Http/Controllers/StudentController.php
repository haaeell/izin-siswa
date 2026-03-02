<?php

namespace App\Http\Controllers;

use App\Exports\StudentsTemplateExport;
use App\Imports\StudentsImport;
use App\Models\Dormitory;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $isWalikelas = auth()->user()->role == 'wali_kelas';

        if ($isWalikelas) {
            $classes = SchoolClass::where('id', auth()->user()->class->id)->get();
        } else {
            $classes = SchoolClass::orderBy('name')->get();
        }

        $dormitories = Dormitory::orderBy('name')->get();

        $pulangQuery = Student::whereHas('permissions', function ($q) {
            $q->where('status', 'approved')
                ->whereHas('checkin', function ($q2) {
                    $q2->whereNotNull('checkout_at')
                        ->whereNull('checkin_at');
                });
        });

        if ($isWalikelas) {
            $pulangQuery->where('class_id', auth()->user()->class->id);
        } elseif ($request->class_id) {
            $pulangQuery->where('class_id', $request->class_id);
        }

        $sedangPulangCount = $pulangQuery->count();

        return view('master.students.index', [
            'classes'           => $classes,
            'dormitories'       => $dormitories,
            'sedangPulangCount' => $sedangPulangCount,
            'filterPulang'      => $request->filter === 'pulang',
            'filterClass'       => $request->class_id,
            'filterDormitory'   => $request->dormitory_id,
        ]);
    }

    public function data(Request $request)
    {
        $isWalikelas = auth()->user()->role == 'wali_kelas';

        $query = Student::with(['class', 'dormitory'])->select('students.*');

        if ($isWalikelas) {
            $query->where('class_id', auth()->user()->class->id);
        } elseif ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->dormitory_id) {
            $query->where('dormitory_id', $request->dormitory_id);
        }

        if ($request->filter === 'pulang') {
            $query->whereHas('permissions', function ($q) {
                $q->where('status', 'approved')
                    ->whereHas('checkin', function ($q2) {
                        $q2->whereNotNull('checkout_at')
                            ->whereNull('checkin_at');
                    });
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('class_name', fn($row) => $row->class->name ?? '-')
            ->addColumn('gender_badge', function ($row) {

                if ($row->gender === 'L') {
                    return '<span class="inline-flex items-center px-3 font-bold py-1 text-xs rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                                Laki-Laki
                            </span>';
                }

                if ($row->gender === 'P') {
                    return '<span class="inline-flex items-center px-3 font-bold py-1 text-xs rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                Perempuan
                            </span>';
                }

                return '<span class="text-slate-300">—</span>';
            })
            ->addColumn('dormitory_name', fn($row) => $row->dormitory->name ?? '-')
            ->addColumn('aksi', function ($row) {
                $edit   = json_encode($row);
                return '
                    <div class="flex justify-center gap-2">
                        <button onclick=\'openEditModal(' . $edit . ')\'
                            class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button onclick="deleteStudent(' . $row->id . ')"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['aksi', 'gender_badge'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis'          => 'required|unique:students,nis',
            'name'         => 'required',
            'gender'       => 'required|in:L,P',
            'class_id'     => 'required|exists:classes,id',
            'dormitory_id' => 'nullable|exists:dormitories,id',
        ]);

        Student::create($request->all());

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nis'          => 'required|unique:students,nis,' . $id,
            'name'         => 'required',
            'gender'       => 'required|in:L,P',
            'class_id'     => 'required|exists:classes,id',
            'dormitory_id' => 'nullable|exists:dormitories,id',
        ]);

        Student::findOrFail($id)->update($request->all());

        return redirect()->back()->with('success', 'Siswa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        if ($student->permissions()->exists()) {
            return redirect()->back()
                ->with('error', 'Siswa tidak bisa dihapus karena masih memiliki data izin.');
        }

        $student->delete();

        return redirect()->back()->with('success', 'Siswa berhasil dihapus');
    }

    public function template()
    {
        return Excel::download(new StudentsTemplateExport, 'template_import_siswa.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx']);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('import_error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Data siswa berhasil diimport');
    }
}
