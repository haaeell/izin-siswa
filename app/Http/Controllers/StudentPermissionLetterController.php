<?php

namespace App\Http\Controllers;

use App\Models\StudentPermission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class StudentPermissionLetterController extends Controller
{
    public function show($id)
    {
        $permission = StudentPermission::with([
            'student.class',
            'waliKelas',
            'approver'
        ])->findOrFail($id);

        // wali kelas hanya boleh lihat surat kelasnya
        abort_if(
            Auth::user()->role === 'wali_kelas'
                && $permission->wali_kelas_id !== Auth::id(),
            403
        );

        if (!in_array($permission->status, ['approved', 'rejected'])) {
            abort(404);
        }

        $view = $permission->status === 'approved'
            ? 'letters.permission-approved'
            : 'letters.permission-rejected';

        $pdf = Pdf::loadView($view, compact('permission'))
            ->setPaper('A4');

        return $pdf->stream(
            'Surat-Izin-' . $permission->student->name . '.pdf'
        );
    }
}
