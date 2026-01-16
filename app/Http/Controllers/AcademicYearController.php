<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        return view('master.academic_years.index', [
            'years' => AcademicYear::orderByDesc('is_active')->get()
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Tahun akademik harus diisi'
        ]);

        AcademicYear::create([
            'name' => $request->name,
            'is_active' => $request->is_active == 'on' ? true : false
        ]);
        return redirect()->back()->with('success', 'Tahun akademik berhasil ditambahkan');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Tahun akademik harus diisi'
        ]);

        if ($request->is_active == 'on') {
            AcademicYear::where('id', '!=', $id)->update(['is_active' => false]);
        }

        AcademicYear::findOrFail($id)->update([
            'name' => $request->name,
            'is_active' => $request->is_active == 'on' ? true : false
        ]);

        return redirect()->back()->with('success', 'Tahun akademik diperbarui');
    }


    public function destroy($id)
    {
        AcademicYear::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Tahun akademik dihapus');
    }
}
