<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentPermission;
use App\Models\StudentPermissionCheckin;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ===============================
         * USER
         * =============================== */
        $perizinan = User::create([
            'name' => 'Petugas Perizinan',
            'email' => 'perizinan@example.com',
            'password' => Hash::make('password'),
            'role' => 'perizinan',
        ]);

        $waliKelas = User::create([
            'name' => 'Wali Kelas 1',
            'email' => 'walikelas@example.com',
            'password' => Hash::make('password'),
            'role' => 'wali_kelas',
        ]);

        /* ===============================
         * TAHUN AKADEMIK
         * =============================== */
        $academicYear = AcademicYear::create([
            'name' => '2024 / 2025',
            'is_active' => true,
        ]);

        /* ===============================
         * KELAS
         * =============================== */
        $class = SchoolClass::create([
            'name' => 'X IPA 1',
            'academic_year_id' => $academicYear->id,
            'wali_kelas_id' => $waliKelas->id,
        ]);

        /* ===============================
         * SISWA
         * =============================== */
        $student = Student::create([
            'nis' => '1234567890',
            'name' => 'Ahmad Fauzi',
            'class_id' => $class->id,
        ]);
    }
}
