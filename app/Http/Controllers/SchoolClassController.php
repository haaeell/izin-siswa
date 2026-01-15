<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        return view('master.classes.index', [
            'classes' => SchoolClass::with(['academicYear', 'waliKelas'])->get(),
            'years'   => AcademicYear::all(),
            'walas'   => User::where('role', 'wali_kelas')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required',
            'academic_year_id' => 'required|exists:academic_years,id',
            'wali_kelas_id'    => 'required|exists:users,id',
        ]);

        SchoolClass::create($request->all());

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'             => 'required',
            'academic_year_id' => 'required|exists:academic_years,id',
            'wali_kelas_id'    => 'required|exists:users,id',
        ]);

        SchoolClass::findOrFail($id)->update($request->all());

        return redirect()->back()->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy($id)
    {
        SchoolClass::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kelas berhasil dihapus');
    }
}
