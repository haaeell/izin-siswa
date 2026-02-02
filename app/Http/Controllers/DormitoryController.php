<?php

namespace App\Http\Controllers;

use App\Models\Dormitory;
use Illuminate\Http\Request;

class DormitoryController extends Controller
{
    public function index()
    {
        return view('master.dormitories.index', [
            'dormitories' => Dormitory::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ], [
            'name.required' => 'Nama asrama harus diisi',
        ]);

        Dormitory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Asrama berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ], [
            'name.required' => 'Nama asrama harus diisi',
        ]);

        Dormitory::findOrFail($id)->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Asrama berhasil diperbarui');
    }

    public function destroy($id)
    {
        Dormitory::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Asrama berhasil dihapus');
    }
}
