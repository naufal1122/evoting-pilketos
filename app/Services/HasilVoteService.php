<?php

namespace App\Services;

use App\Paslon;
use App\User;
use App\Voting;
use App\HasilVoting;
use App\Setting;
use Illuminate\Support\Facades\DB;

class HasilVoteService
{
    /**
     * Mengambil statistik partisipasi per kelas.
     *
     * @return array
     */
    public function getKelasStats(): array
    {
        $siswaData = User::where('role', 'siswa')->with('voting')->get();

        $kelasGrouped = $siswaData->groupBy(function($u) {
            return !empty(trim($u->kelas)) ? trim($u->kelas) : 'Tanpa Kelas';
        });

        $kelasStats = [];
        foreach ($kelasGrouped as $namaKelas => $members) {
            $totalInKelas = $members->count();
            $votedInKelas = $members->filter(function($u) {
                return $u->voting !== null;
            })->count();
            $percentage = $totalInKelas > 0 ? round(($votedInKelas / $totalInKelas) * 100, 1) : 0;

            $kelasStats[] = [
                'kelas' => $namaKelas,
                'total' => $totalInKelas,
                'voted' => $votedInKelas,
                'unvoted' => $totalInKelas - $votedInKelas,
                'percentage' => $percentage
            ];
        }

        usort($kelasStats, function($a, $b) {
            return strnatcasecmp($a['kelas'], $b['kelas']);
        });

        return $kelasStats;
    }

    /**
     * Mengambil rekapitulasi data hasil voting kandidat.
     *
     * @param bool $roundTwoDecimals
     * @return array
     */
    public function getHasilVoteRekap(bool $roundTwoDecimals = false): array
    {
        $paslons = Paslon::orderBy('no_urut_paslon', 'asc')->get();
        $totalSuara = Voting::count();

        $hasilVote = [];
        foreach ($paslons as $p) {
            $count = Voting::where('no_urut_paslon', $p->no_urut_paslon)->count();
            $decimals = $roundTwoDecimals ? 2 : 1;
            $pct = $totalSuara > 0 ? round(($count / $totalSuara) * 100, $decimals) : 0;

            $hasilVote[] = [
                'id' => $p->id,
                'no_urut_paslon' => $p->no_urut_paslon,
                'ketua_paslon' => $p->ketua_paslon,
                'wakil_paslon' => $p->wakil_paslon,
                'img_ketua' => $p->img_ketua,
                'img_wakil' => $p->img_wakil,
                'jumlah_vote' => $count,
                'percentage' => $pct
            ];
        }

        return $hasilVote;
    }

    /**
     * Mengunci dan menyelesaikan pemilihan dengan menyimpan rekapitulasi permanen ke HasilVoting.
     *
     * @return void
     */
    public function selesaikanVoting(): void
    {
        $paslons = Paslon::all();
        foreach ($paslons as $p) {
            $noUrut = $p->no_urut_paslon;
            $hasilNoUrut = Voting::where('no_urut_paslon', $noUrut)->count();

            HasilVoting::updateOrCreate(
                ['no_urut_paslon' => $noUrut],
                ['jumlah_vote' => $hasilNoUrut]
            );
        }
    }

    /**
     * Mereset seluruh transaksi voting dan hasil akhir voting.
     *
     * @return void
     */
    public function resetVoting(): void
    {
        HasilVoting::truncate();
        Voting::truncate();
    }
}
