<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithColumnWidths
{
    public function headings(): array
    {
        return [
            'NIS',
            'Nama Siswa',
            'Kelas',
        ];
    }

    public function array(): array
    {
        return [
            ['123456', 'Contoh Nama Siswa', 'X IPA 1'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [

            // HEADER
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '2563EB'], // blue-600
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical'   => 'center',
                ],
            ],

            // DATA ROW
            2 => [
                'font' => [
                    'italic' => true,
                    'color' => ['rgb' => '475569'], // slate
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 20,
        ];
    }
}
