<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Setting extends Model
{
    protected $table = 'tbl_settings';
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Mengecek status pemilihan apakah buka, tutup, atau mengikuti jadwal
     * @return array [
     *   'is_buka' => bool,
     *   'status' => 'buka'|'tutup'|'belum_mulai'|'selesai',
     *   'mode' => 'otomatis'|'buka'|'tutup',
     *   'pesan' => string,
     *   'waktu_mulai' => string|null,
     *   'waktu_selesai' => string|null,
     *   'countdown_target' => string|null
     * ]
     */
    public static function getVotingStatus(): array
    {
        $mode = self::get('status_pemilihan', 'otomatis');
        $waktuMulai = self::get('waktu_mulai');
        $waktuSelesai = self::get('waktu_selesai');

        // Mode manual override oleh admin
        if ($mode === 'buka') {
            return [
                'is_buka' => true,
                'status' => 'buka',
                'mode' => 'buka',
                'pesan' => 'Pemilihan dibuka secara manual oleh Admin.',
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'countdown_target' => $waktuSelesai
            ];
        }

        if ($mode === 'tutup') {
            return [
                'is_buka' => false,
                'status' => 'tutup',
                'mode' => 'tutup',
                'pesan' => 'Pemilihan telah ditutup secara manual oleh Admin.',
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'countdown_target' => null
            ];
        }

        // Mode Otomatis Berdasarkan Waktu
        $now = Carbon::now();

        if ($waktuMulai && $waktuSelesai) {
            $mulai = Carbon::parse($waktuMulai);
            $selesai = Carbon::parse($waktuSelesai);

            if ($now->lt($mulai)) {
                return [
                    'is_buka' => false,
                    'status' => 'belum_mulai',
                    'mode' => 'otomatis',
                    'pesan' => 'Pemilihan belum dibuka. Harap menunggu jadwal pemilihan dimulai.',
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'countdown_target' => $waktuMulai
                ];
            } elseif ($now->gt($selesai)) {
                return [
                    'is_buka' => false,
                    'status' => 'selesai',
                    'mode' => 'otomatis',
                    'pesan' => 'Waktu pemilihan telah berakhir. Terima kasih atas partisipasi Anda.',
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'countdown_target' => null
                ];
            } else {
                return [
                    'is_buka' => true,
                    'status' => 'buka',
                    'mode' => 'otomatis',
                    'pesan' => 'Pemilihan sedang berlangsung sesuai jadwal.',
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'countdown_target' => $waktuSelesai
                ];
            }
        }

        // Jika belum ada jadwal yang disetel, default buka
        return [
            'is_buka' => true,
            'status' => 'buka',
            'mode' => 'otomatis',
            'pesan' => 'Pemilihan sedang aktif (jadwal belum diatur).',
            'waktu_mulai' => null,
            'waktu_selesai' => null,
            'countdown_target' => null
        ];
    }
}
