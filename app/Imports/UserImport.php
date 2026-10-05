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

        $namaPanjang = $normalized['nama_panjang'] ?? $normalized['nama'] ?? $normalized['nama_lengkap'] ?? null;
        $username = $normalized['username'] ?? null;
        $kelas = $normalized['kelas'] ?? null;
        $nis = $normalized['nis'] ?? $normalized['password'] ?? null;

        // Auto-generate username from the first two words of name if username is empty
        if (empty($username) && !empty($namaPanjang)) {
            $words = array_values(array_filter(explode(' ', trim($namaPanjang))));
            if (count($words) >= 2) {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $words[0] . $words[1]));
            } elseif (count($words) === 1) {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $words[0]));
            }
        }

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


