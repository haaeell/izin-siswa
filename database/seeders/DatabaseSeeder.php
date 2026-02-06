<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Dormitory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ===============================
         * USERS
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

        $security = User::create([
            'name' => 'Security Asrama',
            'email' => 'security@example.com',
            'password' => Hash::make('password'),
            'role' => 'security',
        ]);

        /* ===============================
         * DORMITORIES
         * =============================== */
        $dorm1 = Dormitory::create([
            'name' => 'Asrama Putra',
            'description' => 'Asrama khusus siswa putra.',
        ]);

        $dorm2 = Dormitory::create([
            'name' => 'Asrama Putri',
            'description' => 'Asrama khusus siswa putri.',
        ]);

        /* ===============================
         * TAHUN AKADEMIK
         * =============================== */
        $academicYear = \App\Models\AcademicYear::create([
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
            'dormitory_id' => $dorm1->id,
        ]);

        $student2 = Student::create([
            'nis' => '1234567891',
            'name' => 'Siti Aminah',
            'class_id' => $class->id,
            'dormitory_id' => $dorm2->id,
        ]);
    }
}
