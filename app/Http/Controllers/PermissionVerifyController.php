<?php

namespace App\Http\Controllers;

use App\Models\StudentPermission;

class PermissionVerifyController extends Controller
{
    public function __invoke($token)
    {
        $permission = StudentPermission::where('qr_token', $token)->first();

        if (!$permission) {
            return view('verify.invalid');
        }

        return view('verify.permission', compact('permission'));
    }
}
