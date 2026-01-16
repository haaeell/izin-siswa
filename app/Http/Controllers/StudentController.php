<?php

namespace App\Http\Controllers;

use App\Exports\StudentsTemplateExport;
use App\Imports\StudentsImport;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index()
    {
        return view('master.students.index', [
            'students' => Student::with('class')->get(),
            'classes'  => SchoolClass::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis'      => 'required|unique:students,nis',
            'name'     => 'required',
            'class_id' => 'required|exists:classes,id',
        ]);

        Student::create($request->all());

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nis'      => 'required|unique:students,nis,' . $id,
            'name'     => 'required',
            'class_id' => 'required|exists:classes,id',
        ]);

        Student::findOrFail($id)->update($request->all());

        return redirect()->back()->with('success', 'Siswa berhasil diperbarui');
    }

    public function destroy($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Siswa berhasil dihapus');
    }

    public function template()
    {
        return Excel::download(
            new StudentsTemplateExport,
            'template_import_siswa.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('import_error', $e->getMessage());
        }

        return redirect()
            ->back()
            ->with('success', 'Data siswa berhasil diimport');
    }
}
