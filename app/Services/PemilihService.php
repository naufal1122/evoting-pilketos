<?php

namespace App\Services;

use App\User;
use App\Imports\UserImport;
use App\Exports\SiswaExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PemilihService
{
    /**
     * Mendaftarkan satu siswa secara manual dengan otomasi username jika kosong.
     *
     * @param array $data
     * @return User
     */
    public function registerManual(array $data): User
    {
        $username = $data['username'] ?? null;
        $namaPanjang = $data['nama_panjang'] ?? '';

        if (empty($username) && !empty($namaPanjang)) {
            $words = array_values(array_filter(explode(' ', trim($namaPanjang))));
            if (count($words) >= 2) {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $words[0] . $words[1]));
            } elseif (count($words) === 1) {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $words[0]));
            }
        }

        return User::create([
            'username' => $username,
            'nama_panjang' => $namaPanjang,
            'kelas' => $data['kelas'] ?? null,
            'role' => 'siswa',
            'password' => (string) ($data['password'] ?? ''),
        ]);
    }

    /**
     * Menghapus siswa berdasarkan ID.
     *
     * @param int $id
     * @return bool
     */
    public function hapusSiswa(int $id): bool
    {
        $siswa = User::find($id);
        if ($siswa) {
            return (bool) $siswa->delete();
        }
        return false;
    }

    /**
     * Memproses file upload excel untuk import siswa.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     * @throws \Exception
     */
    public function importExcel($file): void
    {
        $namaFile = time() . '_' . rand(1000, 9999) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $destinationPath = public_path('file_user');

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        $filePath = $destinationPath . DIRECTORY_SEPARATOR . $namaFile;
        $file->move($destinationPath, $namaFile);

        try {
            Excel::import(new UserImport, $filePath);
        } finally {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }

    /**
     * Mengambil query filter pemilih.
     *
     * @param array $filters
     * @return array ['data' => Paginator, 'kelasList' => Collection]
     */
    public function getFilteredPemilih(array $filters): array
    {
        $search = $filters['search'] ?? null;
        $perPage = (int) ($filters['perPage'] ?? 10);
        $status = $filters['status'] ?? null;
        $kelas = $filters['kelas'] ?? null;

        $query = User::where('role', 'siswa')->with('voting');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_panjang', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'voted') {
            $query->whereHas('voting');
        } elseif ($status === 'not_voted') {
            $query->doesntHave('voting');
        }

        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        $data = $query->paginate($perPage)->withQueryString();

        $kelasList = User::where('role', 'siswa')
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        return compact('data', 'kelasList');
    }
}
