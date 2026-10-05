<?php

namespace App\Http\Controllers;

use App\Paslon;
use App\Voting;
use App\User;
use App\HasilVoting;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan mengimpor Auth
use Illuminate\Database\QueryException; // Untuk menangani kesalahan saat query
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use App\Jobs\StoreVotingJob;

class siswaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data = Paslon::all();
        $votingSchedule = Setting::getVotingStatus();
        return view('siswa.home', compact('data', 'votingSchedule'));
    }


    public function detail( $id ) {

        $data = Paslon::find($id);
        return view('siswa.detail', ['data' => $data]);

    }

    public function hapusSiswa($id)
    {
        // Temukan siswa berdasarkan ID
        $siswa = User::find($id); // Ganti User dengan model yang sesuai jika berbeda

        if (!$siswa) {
            return redirect()->back()->with('error', 'Siswa tidak ditemukan.');
        }

        // Hapus siswa
        $siswa->delete();

        return redirect()->back()->with('success', 'Siswa berhasil dihapus.');
    }

    public function pilihPaslon($id, Request $request)
    {
        $idUser = Auth::id();

        // 1. Validasi jadwal pemilihan (Fitur 1)
        $schedule = Setting::getVotingStatus();
        if (!$schedule['is_buka']) {
            $msg = $schedule['pesan'];
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            Alert::warning('Pemilihan Ditutup', $msg);
            return redirect('/home');
        }

        // 2. Validasi keberadaan paslon
        $paslon = Paslon::find($id);
        if (!$paslon) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Paslon tidak ditemukan.'], 404);
            }
            Alert::error('Error', 'Paslon tidak ditemukan');
            return redirect('/home');
        }

        $noUrutPaslon = $paslon->no_urut_paslon;

        // 3. Gunakan DB transaction dengan pessimistic row locking pada tabel users untuk mencegah race condition double vote
        try {
            DB::transaction(function () use ($idUser, $noUrutPaslon) {
                // Kunci baris user agar voting konkuren dari user yang sama di-serialize
                $lockedUser = User::where('id', $idUser)->lockForUpdate()->first();

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
            }, 3); // Coba ulang hingga 3 kali jika terjadi deadlock sementara
        } catch (\RuntimeException $re) {
            $msg = $re->getMessage() === 'ALREADY_VOTED'
                ? 'Anda sudah memberikan suara sebelumnya!'
                : 'Pemilihan sudah ditutup!';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            Alert::warning('Peringatan', $msg);
            return redirect('/home');
        } catch (QueryException $qe) {
            // Tangani duplicate key jika unik constraint terpukul
            if ($qe->errorInfo[1] == 1062) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Anda sudah memberikan suara sebelumnya!'], 422);
                }
                Alert::warning('Peringatan', 'Anda sudah memberikan suara sebelumnya!');
                return redirect('/home');
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
            }
            Alert::error('Error', 'Gagal menyimpan voting.');
            return redirect('/home');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Vote error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan voting: ' . $e->getMessage()], 500);
            }
            Alert::error('Error', 'Gagal menyimpan voting: ' . $e->getMessage());
            return redirect('/home');
        }

        // Kiosk Mode Auto-Logout (Fitur 2)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih, kamu berhasil memilih! Sistem akan otomatis logout.',
                'kiosk_logout' => true,
                'auto_logout_seconds' => 3
            ]);
        }

        Alert::success('Success', 'Kamu Berhasil Memilih! Sistem akan otomatis logout.');
        return redirect('/home')->with('auto_logout', true);
    }

}
