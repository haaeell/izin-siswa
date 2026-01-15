<?php

namespace App\Http\Controllers;

use App\Models\StudentPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPermissionApprovalController extends Controller
{
    public function approve($id)
    {
        $permission = StudentPermission::findOrFail($id);

        $permission->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'reject_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Izin disetujui');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required',
        ]);

        $permission = StudentPermission::findOrFail($id);

        $permission->update([
            'status'        => 'rejected',
            'approved_by'   => Auth::id(),
            'reject_reason' => $request->reject_reason,
        ]);

        return redirect()->back()->with('success', 'Izin ditolak');
    }
}
