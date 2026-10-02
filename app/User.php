<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'role', 'email', 'password', 'nama_panjang', 'kelas',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // Menambahkan relasi ke tabel tbl_voting
    public function voting()
    {
        return $this->hasOne(Voting::class, 'id_user', 'id');
    }

    /**
     * Dapatkan avatar URL berdasarkan nama siswa / username jika NA atau blank
     */
    public function getAvatarUrlAttribute()
    {
        $name = !empty($this->nama_panjang) ? trim($this->nama_panjang) : trim($this->username);
        if (empty($name)) {
            $name = 'Siswa';
        }
        $encoded = urlencode($name);
        // Menggunakan UI Avatars dengan background hijau elegan dan teks putih
        return "https://ui-avatars.com/api/?name={$encoded}&background=10b981&color=ffffff&bold=true&format=svg";
    }
}
