<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

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
}
