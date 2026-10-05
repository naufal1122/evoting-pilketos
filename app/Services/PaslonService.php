<?php

namespace App\Services;

use App\Paslon;
use Illuminate\Support\Facades\File;

class PaslonService
{
    /**
     * Membuat paslon baru dan memindahkan file gambar ketua.
     *
     * @param array $data
     * @param \Illuminate\Http\UploadedFile $imgKetua
     * @return Paslon
     */
    public function createPaslon(array $data, $imgKetua): Paslon
    {
        $namaFileKetua = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $imgKetua->getClientOriginalName());
        $folderKetua = public_path('img_ketua');

        if (!file_exists($folderKetua)) {
            mkdir($folderKetua, 0755, true);
        }

        $paslon = Paslon::create([
            'no_urut_paslon' => $data['no_urut_paslon'],
            'ketua_paslon' => $data['ketua_paslon'],
            'wakil_paslon' => $data['wakil_paslon'] ?? '-',
            'visi_paslon' => $data['visi_paslon'],
            'misi_paslon' => $data['misi_paslon'],
            'img_ketua' => $namaFileKetua,
            'img_wakil' => null,
        ]);

        $imgKetua->move($folderKetua, $namaFileKetua);

        return $paslon;
    }

    /**
     * Memperbarui paslon dan menangani penggantian gambar bila diunggah.
     *
     * @param int $id
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $imgKetua
     * @return Paslon
     */
    public function updatePaslon(int $id, array $data, $imgKetua = null): Paslon
    {
        $paslon = Paslon::findOrFail($id);
        $paslon->no_urut_paslon = $data['no_urut_paslon'];
        $paslon->ketua_paslon = $data['ketua_paslon'];
        $paslon->visi_paslon = $data['visi_paslon'];
        $paslon->misi_paslon = $data['misi_paslon'];

        if ($imgKetua) {
            $namaFileKetua = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $imgKetua->getClientOriginalName());
            $folderKetua = 'img_ketua';

            $imgKetua->move(public_path($folderKetua), $namaFileKetua);

            if ($paslon->img_ketua) {
                File::delete(public_path($folderKetua . '/' . $paslon->img_ketua));
            }

            $paslon->img_ketua = $namaFileKetua;
        }

        $paslon->save();

        return $paslon;
    }

    /**
     * Menghapus paslon beserta file gambar ketua dan wakil.
     *
     * @param int $id
     * @return void
     */
    public function deletePaslon(int $id): void
    {
        $paslon = Paslon::findOrFail($id);

        if ($paslon->img_ketua) {
            File::delete(public_path('img_ketua/' . $paslon->img_ketua));
        }
        if ($paslon->img_wakil) {
            File::delete(public_path('img_wakil/' . $paslon->img_wakil));
        }

        $paslon->delete();
    }
}
