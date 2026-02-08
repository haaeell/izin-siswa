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

        if (!in_array($permission->status, ['approved', 'rejected'])) {
            abort(404);
        }

        $view = $permission->status === 'approved'
            ? 'letters.permission-approved'
            : 'letters.permission-rejected';

        $logoPath = public_path('images/logosekolah.jpg');

        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
        }


        $school = [
            'name'    => config('school.name', 'SMA Contoh Negeri'),
            'address' => config('school.address', 'Jl. Pendidikan No. 1'),
            'phone'   => config('school.phone', '0274-000000'),
            'email'   => config('school.email', 'info@sekolah.sch.id'),
            'logo'    => $logoBase64,
        ];

        $nomor = sprintf(
            '421/%03d/%s/%s',
            $permission->id,
            strtoupper(now()->format('m')),
            now()->year
        );

        $city = config('school.city', 'Yogyakarta');

        $pdf = Pdf::loadView($view, compact(
            'permission',
            'school',
            'nomor',
            'city'
        ))
            ->setPaper('A4')
            ->setOptions([
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream(
            'Surat-Izin-' . str_replace(' ', '-', $permission->student->name) . '.pdf'
        );
    }
}
