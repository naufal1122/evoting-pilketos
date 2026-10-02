<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Normalize keys (trim whitespace and lowercase)
        $normalized = [];
        foreach ($row as $key => $value) {
            $cleanedKey = strtolower(trim(str_replace(' ', '_', $key)));
            $normalized[$cleanedKey] = is_string($value) ? trim($value) : $value;
        }

        $username = $normalized['username'] ?? null;
        $namaPanjang = $normalized['nama_panjang'] ?? $normalized['nama'] ?? $normalized['nama_lengkap'] ?? null;
        $kelas = $normalized['kelas'] ?? null;
        $nis = $normalized['nis'] ?? $normalized['password'] ?? null;

        // Skip rows without username or nis
        if (empty($username) || empty($nis)) {
            return null;
        }

        // Check if user already exists
        $user = User::where('username', $username)->first();
        if ($user) {
            $user->update([
                'nama_panjang' => $namaPanjang ?? $user->nama_panjang,
                'kelas' => $kelas ?? $user->kelas,
                'password' => (string) $nis,
                'role' => 'siswa',
            ]);
            return null;
        }

        return new User([
            'username' => (string) $username,
            'nama_panjang' => $namaPanjang,
            'kelas' => $kelas,
            'password' => (string) $nis,
            'role' => 'siswa',
        ]);
    }
}


