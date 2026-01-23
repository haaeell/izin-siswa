<?php

namespace App\Http\Controllers;

use App\Models\StudentPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentPermissionApprovalController extends Controller
{
    public function approve($id)
    {
        abort_if(Auth::user()->role !== 'perizinan', 403);

        $permission = StudentPermission::findOrFail($id);

        abort_if($permission->status !== 'pending', 400);

        $permission->update([
            'status'      => 'approved',
            'approved_by' => Auth::user()->id,
            'qr_token'    => Str::uuid(),
        ]);

        return redirect()->back()
            ->with('success', 'Permohonan disetujui & QR berhasil dibuat');
    }

    public function reject(Request $request, $id)
    {
        abort_if(Auth::user()->role !== 'perizinan', 403);

        $data = $request->validate([
            'reject_reason' => 'required|string|min:5',
        ]);

        $permission = StudentPermission::findOrFail($id);

        abort_if($permission->status !== 'pending', 400);

        $permission->update([
            'status'        => 'rejected',
            'reject_reason' => $data['reject_reason'],
            'approved_by'   => Auth::user()->id,
        ]);

        return redirect()->back()->with('success', 'Permohonan izin ditolak');
    }
}
