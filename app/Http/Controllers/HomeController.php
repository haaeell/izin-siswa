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

        $data = [];

        // ======================
        // WALI KELAS
        // ======================
        if ($user->role === 'wali_kelas') {

            $classId = $user->class->id;

            $data['totalStudents'] = Student::where('class_id', $classId)->count();
            $data['totalClasses']  = 1;

            $data['pendingCount'] = StudentPermission::where('wali_kelas_id', $user->id)
                ->where('status', 'pending')
                ->count();

            $data['todayCount'] = StudentPermission::where('wali_kelas_id', $user->id)
                ->whereDate('created_at', now())
                ->count();

            $data['myPermissions'] = StudentPermission::where('wali_kelas_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        // ======================
        // ROLE LAIN (ADMIN / PERIZINAN)
        // ======================
        else {

            $data['totalStudents'] = Student::count();
            $data['totalClasses']  = SchoolClass::count();

            $data['pendingCount'] = StudentPermission::where('status', 'pending')->count();
            $data['todayCount']   = StudentPermission::whereDate('created_at', now())->count();
        }

        // ======================
        // PERIZINAN
        // ======================
        if ($user->role === 'perizinan') {

            $data['pendingPermissions'] = StudentPermission::where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get();

            $data['todayCheckins'] = StudentPermissionCheckin::whereDate('checkin_at', now())
                ->count();
        }

        return view('home', $data);
    }
}
