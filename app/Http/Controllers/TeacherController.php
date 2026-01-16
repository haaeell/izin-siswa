<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        return view('master.teachers.index', [
            'teachers' => User::where('role', 'wali_kelas')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ], [
            'name.required'     => 'Nama guru wajib diisi',
            'email.required'    => 'Email wajib diisi',
            'email.unique'      => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'wali_kelas', // FIXED
        ]);

        return redirect()->back()->with('success', 'Guru berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $teacher = User::where('role', 'wali_kelas')->findOrFail($id);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->update($data);

        return redirect()->back()->with('success', 'Guru berhasil diperbarui');
    }

    public function destroy($id)
    {
        $teacher = User::where('role', 'wali_kelas')->findOrFail($id);

        if ($teacher->classes()->exists()) {
            return redirect()->back()->with('error', 'Guru masih digunakan di kelas');
        }

        $teacher->delete();

        return redirect()->back()->with('success', 'Guru berhasil dihapus');
    }
}
