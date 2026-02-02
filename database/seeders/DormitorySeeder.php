<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dormitory;

class DormitorySeeder extends Seeder
{
    public function run(): void
    {
        $dormitories = [
            [
                'name' => 'Asrama Putra',
                'description' => 'Asrama khusus siswa putra',
            ],
            [
                'name' => 'Asrama Putri',
                'description' => 'Asrama khusus siswa putri',
            ],
            [
                'name' => 'Asrama A',
                'description' => 'Asrama umum blok A',
            ],
            [
                'name' => 'Asrama B',
                'description' => 'Asrama umum blok B',
            ],
        ];

        foreach ($dormitories as $dormitory) {
            Dormitory::create($dormitory);
        }
    }
}
