<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $nisInFile = [];

        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), [
                'nis' => 'required',
                'nama_siswa' => 'required',
                'kelas' => 'required',
            ]);

            if ($validator->fails()) {
                throw new \Exception('Validasi gagal pada baris ' . ($index + 2));
            }

            if (in_array($row['nis'], $nisInFile)) {
                throw new \Exception('Duplikat NIS di file pada baris ' . ($index + 2));
            }

            if (Student::where('nis', $row['nis'])->exists()) {
                throw new \Exception('NIS sudah terdaftar pada baris ' . ($index + 2));
            }

            if (!SchoolClass::where('name', $row['kelas'])->exists()) {
                throw new \Exception('Kelas tidak ditemukan pada baris ' . ($index + 2));
            }

            $nisInFile[] = $row['nis'];
        }

        foreach ($rows as $row) {
            $class = SchoolClass::where('name', $row['kelas'])->first();

            Student::create([
                'nis' => $row['nis'],
                'name' => $row['nama_siswa'],
                'class_id' => $class->id,
            ]);
        }
    }
}
