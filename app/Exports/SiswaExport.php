<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::where('role', 'siswa')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Kelas',
            'NIS',
        ];
    }

    public function map($siswa): array
    {
        return [
            $siswa->nama_panjang ?? $siswa->username,
            $siswa->kelas ?? '',
            $siswa->password, // Password adalah NIS siswa
        ];
    }
}
