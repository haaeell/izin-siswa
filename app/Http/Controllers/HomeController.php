<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\StudentPermission;
use App\Models\StudentPermissionCheckin;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $data = [
            'totalStudents' => Student::count(),
            'totalClasses'  => SchoolClass::count(),
            'pendingCount'  => StudentPermission::where('status', 'pending')->count(),
            'todayCount'    => StudentPermission::whereDate('created_at', now())->count(),
        ];

        if ($user->role === 'wali_kelas') {
            $data['myPermissions'] = StudentPermission::where('wali_kelas_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        if ($user->role === 'perizinan') {
            $data['pendingPermissions'] = StudentPermission::where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get();

            $data['todayCheckins'] = StudentPermissionCheckin::whereDate('checkin_at', now())->count();
        }

        return view('home', $data);
    }
}
