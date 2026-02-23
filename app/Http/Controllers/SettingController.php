<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'max_active_permissions' => 'required|integer|min:1|max:100',
        ], [
            'max_active_permissions.required' => 'Batas izin aktif wajib diisi.',
            'max_active_permissions.integer'  => 'Batas izin aktif harus berupa angka.',
            'max_active_permissions.min'      => 'Batas izin aktif minimal 1.',
            'max_active_permissions.max'      => 'Batas izin aktif maksimal 100.',
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
            Cache::forget("setting_{$key}");
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
