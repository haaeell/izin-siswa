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
        // ── DEBUG: tampilkan semua nama kelas & asrama yang ada di DB ─────────
        $this->command->info('📋 Kelas tersedia: ' . SchoolClass::pluck('name')->join(', '));
        $this->command->info('🏠 Asrama tersedia: ' . Dormitory::pluck('name')->join(', '));

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

        // Ambil semua NIS yang sudah ada agar tidak duplicate
        $usedNis = DB::table('students')->pluck('nis')->toArray();
        $now     = now();

        $batches = [
            ['class' => 'X IPA 1', 'dormitory' => 'Asrama Putra', 'count' => 100],
            ['class' => 'X IPA 2', 'dormitory' => 'Heritage II',  'count' => 100],
            ['class' => 'X IPA 3', 'dormitory' => 'Heritage II',  'count' => 100],
        ];

        foreach ($batches as $batch) {
            $kelas = SchoolClass::where('name', $batch['class'])->first();
            if (! $kelas) {
                $this->command->error("❌ Kelas '{$batch['class']}' tidak ditemukan! Lewati batch ini.");
                continue;
            }

            $asrama = Dormitory::where('name', $batch['dormitory'])->first();
            if (! $asrama) {
                $this->command->error("❌ Asrama '{$batch['dormitory']}' tidak ditemukan! Lewati batch ini.");
                continue;
            }

            $students = [];
            for ($i = 1; $i <= $batch['count']; $i++) {
                do {
                    $nis = (string) rand(10000000, 99999999);
                } while (in_array($nis, $usedNis));
                $usedNis[] = $nis;

                $students[] = [
                    'nis'          => $nis,
                    'name'         => $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)],
                    'class_id'     => $kelas->id,
                    'gender' => rand(0, 1) ? 'L' : 'P',
                    'dormitory_id' => $asrama->id,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }

            DB::table('students')->insert($students);
            $this->command->info("✅ {$batch['count']} siswa berhasil di-seed ke kelas {$kelas->name} - {$asrama->name}!");
        }
    }
}
