<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolClass;
use App\Models\Dormitory;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $kelas  = SchoolClass::where('name', 'X IPA 1')->firstOrFail();
        $asrama = Dormitory::where('name', 'Asrama Putra')->firstOrFail();

        $namaDepan = [
            'Ahmad',
            'Muhammad',
            'Rizky',
            'Fajar',
            'Dani',
            'Budi',
            'Hendra',
            'Andi',
            'Reza',
            'Bagas',
            'Taufik',
            'Irfan',
            'Yusuf',
            'Agus',
            'Dimas',
            'Gilang',
            'Hafiz',
            'Ilham',
            'Joko',
            'Kevin',
            'Luthfi',
            'Maulana',
            'Nanda',
            'Oscar',
            'Pandu',
            'Rafli',
            'Satria',
            'Teguh',
            'Umar',
            'Wahyu',
            'Yoga',
            'Zaki',
            'Arif',
            'Bayu',
            'Chandra',
            'Dafa',
            'Eka',
            'Faisal',
            'Galih',
            'Haris',
        ];

        $namaBelakang = [
            'Pratama',
            'Saputra',
            'Wijaya',
            'Santoso',
            'Kurniawan',
            'Hidayat',
            'Susanto',
            'Rahmadi',
            'Nugroho',
            'Wibowo',
            'Setiawan',
            'Purnomo',
            'Hartono',
            'Kusuma',
            'Firmansyah',
            'Ramadan',
            'Hakim',
            'Fauzan',
            'Aziz',
            'Sholeh',
            'Maulana',
            'Ardiansyah',
            'Budiman',
            'Cahyono',
            'Darmawan',
            'Effendi',
            'Gunawan',
            'Handoko',
            'Iskandar',
            'Jatmiko',
            'Kuncoro',
            'Laksono',
            'Mardiyanto',
            'Nasution',
            'Oktavian',
            'Pangestu',
            'Rohman',
            'Surya',
            'Taslim',
            'Utomo',
        ];

        $students = [];
        $usedNis  = [];
        $now      = now();

        for ($i = 1; $i <= 100; $i++) {
            do {
                $nis = (string) rand(10000000, 99999999);
            } while (in_array($nis, $usedNis));
            $usedNis[] = $nis;

            $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];

            $students[] = [
                'nis'          => $nis,
                'name'         => $nama,
                'class_id'     => $kelas->id,
                'dormitory_id' => $asrama->id,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        DB::table('students')->insert($students);

        $this->command->info("✅ 100 siswa berhasil di-seed ke kelas {$kelas->name} - {$asrama->name}!");
    }
}
