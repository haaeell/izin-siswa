<?php

namespace App\Http\Controllers;

use App\Models\StudentPermission;
use Illuminate\Http\Request;

class PermissionVerifyController extends Controller
{
    public function permission(Request $request)
    {
        $token = $request->query('t');

        $permission = StudentPermission::with([
            'student.class'
        ])->where('qr_token', $token)->first();

        if (!$permission) {
            return view('verify.permission-invalid');
        }

        return view('verify.permission', compact('permission'));
    }

    public function walas(Request $request)
    {
        $token = $request->query('t');

        $permission = StudentPermission::with([
            'student.class',
            'waliKelas'
        ])->where('qr_token', $token)->first();

        if (!$permission || !$permission->waliKelas) {
            return view('verify.walas-invalid');
        }

        return view('verify.walas-valid', compact('permission'));
    }
}
