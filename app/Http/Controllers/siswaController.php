<?php

namespace App\Http\Controllers;

use App\Paslon;
use App\Voting;
use App\User;
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
        return view('siswa.home', ['data' => $data]);

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

    public function pilihPaslon($id)
    {
        // Validasi jika paslon dengan ID tersebut ada
        $paslon = Paslon::find($id);
        if (!$paslon) {
            Alert::error('Error', 'Paslon tidak ditemukan');
            return redirect('/home');
        }

        $noUrutPaslon = $paslon->no_urut_paslon;
        $idUser = Auth::user()->id;

        // Cek jika siswa sudah pernah memilih
        if (Voting::where('id_user', $idUser)->exists()) {
            Alert::warning('Peringatan', 'Anda sudah memberikan suara sebelumnya!');
            return redirect('/home');
        }

        try {
            DB::beginTransaction(); // Memulai transaksi

            // Simpan data voting
            Voting::create([
                'id_user' => $idUser,
                'no_urut_paslon' => $noUrutPaslon,
            ]);

            // Tambahkan operasi lain jika diperlukan

            DB::commit(); // Menyimpan perubahan jika semua berhasil

            Alert::success('Success', 'Kamu Berhasil Memilih');
        } catch (\Exception $e) {
            DB::rollBack(); // Membatalkan semua perubahan jika ada error
            Alert::error('Error', 'Gagal menyimpan voting: ' . $e->getMessage());
        }

        return redirect('/home');
    }

}
