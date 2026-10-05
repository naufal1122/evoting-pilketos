<?php

namespace App\Services;

use App\Voting;
use App\Paslon;
use App\User;
use App\HasilVoting;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class VotingService
{
    /**
     * Memproses voting transaksi dengan pessimistic locking dan validasi lengkap.
     *
     * @param int $idPaslon
     * @param int $idUser
     * @return array
     * @throws \Exception
     */
    public function prosesVote(int $idPaslon, int $idUser): array
    {
        // 1. Validasi jadwal pemilihan
        $schedule = Setting::getVotingStatus();
        if (!$schedule['is_buka']) {
            throw new \DomainException($schedule['pesan']);
        }

        // 2. Validasi keberadaan paslon
        $paslon = Paslon::find($idPaslon);
        if (!$paslon) {
            throw new \InvalidArgumentException('Paslon tidak ditemukan.');
        }

        $noUrutPaslon = $paslon->no_urut_paslon;

        // 3. Eksekusi database transaction dengan row locking untuk mencegah race condition double vote
        try {
            DB::transaction(function () use ($idUser, $noUrutPaslon) {
                // Kunci baris user agar voting konkuren dari user yang sama di-serialize
                User::where('id', $idUser)->lockForUpdate()->first();

                // Cek apakah user sudah pernah memilih
                $alreadyVoted = Voting::where('id_user', $idUser)->exists();
                if ($alreadyVoted) {
                    throw new \RuntimeException('ALREADY_VOTED');
                }

                // Cek apakah voting sudah ditutup secara permanen
                if (HasilVoting::count() > 0) {
                    throw new \RuntimeException('VOTING_CLOSED');
                }

                // Simpan data voting (didukung oleh UNIQUE constraint id_user di database)
                Voting::create([
                    'id_user' => $idUser,
                    'no_urut_paslon' => $noUrutPaslon,
                ]);
            }, 3);
        } catch (\RuntimeException $re) {
            $msg = $re->getMessage() === 'ALREADY_VOTED'
                ? 'Anda sudah memberikan suara sebelumnya!'
                : 'Pemilihan sudah ditutup!';
            throw new \DomainException($msg);
        } catch (QueryException $qe) {
            if (isset($qe->errorInfo[1]) && $qe->errorInfo[1] == 1062) {
                throw new \DomainException('Anda sudah memberikan suara sebelumnya!');
            }
            throw $qe;
        }

        return [
            'success' => true,
            'message' => 'Terima kasih, kamu berhasil memilih! Sistem akan otomatis logout.',
            'kiosk_logout' => true,
            'auto_logout_seconds' => 3
        ];
    }
}
