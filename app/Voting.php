<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Voting extends Model // Ubah 'voting' menjadi 'Voting'
{
    protected $table = 'tbl_voting'; // Jika tabel tidak mengikuti konvensi
    protected $fillable = ['id_user', 'no_urut_paslon'];

    public static function createVoting($userId, $noUrutPaslon)
    {
        DB::transaction(function () use ($userId, $noUrutPaslon) {
            // Simpan data voting
            self::create([
                'id_user' => $userId,
                'no_urut_paslon' => $noUrutPaslon,
            ]);

            // Tambahkan operasi lain jika diperlukan
        });
    }
}
