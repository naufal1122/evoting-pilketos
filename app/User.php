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

        // Palet warna yang variatif, cerah, dan elegan
        $colors = [
            '3B82F6', // Blue
            '10B981', // Emerald
            '6366F1', // Indigo
            'F59E0B', // Amber
            'EC4899', // Pink
            '8B5CF6', // Purple
            '14B8A6', // Teal
            'F97316', // Orange
            '06B6D4', // Cyan
            'EF4444', // Red
        ];

        $key = !empty($this->username) ? $this->username : $name;
        $colorIndex = abs(crc32($key)) % count($colors);
        $bgColor = $colors[$colorIndex];

        return "https://ui-avatars.com/api/?name={$encoded}&background={$bgColor}&color=ffffff&bold=true&format=svg";
    }
}
