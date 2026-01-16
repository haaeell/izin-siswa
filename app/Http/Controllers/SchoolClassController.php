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
            'classes' => SchoolClass::with(['academicYear', 'waliKelas'])
                ->latest()
                ->get(),
            'waliKelas' => User::where('role', 'wali_kelas')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'wali_kelas_id' => 'required|exists:users,id',
        ], [
            'name.required' => 'Nama kelas harus diisi',
            'wali_kelas_id.required' => 'Wali kelas harus dipilih',
        ]);

        SchoolClass::create([
            'name' => $request->name,
            'wali_kelas_id' => $request->wali_kelas_id,
        ]);

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'wali_kelas_id' => 'required|exists:users,id',
        ], [
            'name.required' => 'Nama kelas harus diisi',
            'wali_kelas_id.required' => 'Wali kelas harus dipilih',
        ]);

        SchoolClass::findOrFail($id)->update([
            'name' => $request->name,
            'wali_kelas_id' => $request->wali_kelas_id,
        ]);

        return redirect()->back()->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy($id)
    {
        SchoolClass::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kelas berhasil dihapus');
    }
}
