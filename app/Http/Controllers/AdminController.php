<?php

namespace App\Http\Controllers;

use App\Services\PaslonService;
use App\Services\PemilihService;
use App\Services\HasilVoteService;
use App\Paslon;
use App\User;
use App\Voting;
use App\Setting;
use App\Exports\SiswaExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $paslonService;
    protected $pemilihService;
    protected $hasilVoteService;

    public function __construct(
        PaslonService $paslonService,
        PemilihService $pemilihService,
        HasilVoteService $hasilVoteService
    ) {
        $this->paslonService = $paslonService;
        $this->pemilihService = $pemilihService;
        $this->hasilVoteService = $hasilVoteService;
    }

    /**
     * Dashboard Utama Admin
     */
    public function index()
    {
        $data = Paslon::orderBy('no_urut_paslon', 'asc')->get();
        $totalPaslon = count($data);
        $siswaData = User::where('role', 'siswa')->with('voting')->get();
        $totalSiswa = count($siswaData);
        $totalSuara = Voting::count();

        $kelasStats = $this->hasilVoteService->getKelasStats();
        $votingSchedule = Setting::getVotingStatus();

        return view('admin.dashboard', compact('data', 'totalPaslon', 'siswaData', 'totalSiswa', 'totalSuara', 'kelasStats', 'votingSchedule'));
    }

    /**
     * Hapus Paslon
     */
    public function hapus($id)
    {
        $this->paslonService->deletePaslon((int) $id);
        Alert::success('Success', 'Data Berhasil Di Hapus');
        return redirect('/dashboard');
    }

    /**
     * Form Tambah Paslon
     */
    public function viewTambah()
    {
        return view('admin.tambah');
    }

    /**
     * Proses Tambah Paslon
     */
    public function prosesTambah(Request $request)
    {
        $this->validate($request, [
            'no_urut_paslon' => 'required|integer|unique:tbl_paslon,no_urut_paslon',
            'ketua_paslon' => 'required',
            'visi_paslon' => 'required',
            'misi_paslon' => 'required',
            'img_ketua' => 'required|max:5000|file|image',
        ]);

        $this->paslonService->createPaslon($request->all(), $request->file('img_ketua'));

        Alert::success('Success', 'Upload Paslon Berhasil');
        return redirect()->route('dashboard');
    }

    /**
     * Form Edit Paslon
     */
    public function edit($id)
    {
        $data = Paslon::findOrFail($id);
        return view('admin.edit', ['data' => $data]);
    }

    /**
     * Proses Edit Paslon
     */
    public function prosesEdit($id, Request $request)
    {
        $this->validate($request, [
            'no_urut_paslon' => 'required|integer',
            'ketua_paslon' => 'required',
            'visi_paslon' => 'required',
            'misi_paslon' => 'required',
            'img_ketua' => 'nullable|max:2000|file|mimes:jpg,png,jpeg|image',
        ]);

        $this->paslonService->updatePaslon((int) $id, $request->all(), $request->file('img_ketua'));

        Alert::success('Success', 'Data Berhasil Di Ubah');
        return redirect('/kandidat');
    }

    /**
     * Detail Paslon (Admin)
     */
    public function detail($id)
    {
        $data = Paslon::findOrFail($id);
        return view('admin.detail', ['data' => $data]);
    }

    /**
     * Form Manual Register Siswa
     */
    public function registerSiswa()
    {
        return view('admin.register-siswa');
    }

    /**
     * Proses Manual Register Siswa
     */
    public function prosesRegisterSiswa(Request $request)
    {
        $this->validate($request, [
            'nama_panjang' => 'required',
            'kelas' => 'required',
            'password' => 'required'
        ]);

        $this->pemilihService->registerManual($request->all());

        Alert::success('Success', 'Register Siswa Berhasil');
        return redirect('/registerSiswa');
    }

    /**
     * Mengunci dan Menyelesaikan Pemilihan
     */
    public function voteSelesai()
    {
        $this->hasilVoteService->selesaikanVoting();
        Alert::success('Success', 'Pemilihan telah resmi diselesaikan!');
        return redirect('/dashboard');
    }

    /**
     * Halaman Hasil Vote
     */
    public function hasilVote()
    {
        $hasilVote = $this->hasilVoteService->getHasilVoteRekap(false);
        $totalSuara = Voting::count();

        return view('admin.hasil-vote', ['data' => $hasilVote, 'totalSuara' => $totalSuara]);
    }

    /**
     * Endpoint JSON data hasil voting untuk auto-refresh
     */
    public function hasilVoteAjax()
    {
        $hasilVote = $this->hasilVoteService->getHasilVoteRekap(false);
        return response()->json($hasilVote);
    }

    /**
     * Form Import Siswa
     */
    public function importSiswa()
    {
        return view('admin.import-siswa');
    }

    /**
     * Download Template / Export Siswa
     */
    public function exportExcel()
    {
        $siswa = User::where('role', 'siswa')->get();

        if ($siswa->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data siswa untuk diekspor.');
        }

        return Excel::download(new SiswaExport, 'siswa.xlsx');
    }

    /**
     * Proses Import Excel Siswa
     */
    public function importExcel(Request $request)
    {
        $this->validate($request, [
            'file_excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            $this->pemilihService->importExcel($request->file('file_excel'));
            Alert::success('Success', 'Import Data Berhasil');
        } catch (\Throwable $e) {
            Log::error('Import error: ' . $e->getMessage());
            Alert::error('Gagal Import', 'Terjadi kesalahan saat import data: ' . $e->getMessage());
        }

        return redirect('/dashboard');
    }

    /**
     * Reset / Ulang Voting
     */
    public function ulangVoting()
    {
        $this->hasilVoteService->resetVoting();
        Alert::success('Success', 'Voting telah diulang');
        return redirect('/dashboard');
    }

    /**
     * List Data Pemilih (Siswa)
     */
    public function listSiswa(Request $request)
    {
        $result = $this->pemilihService->getFilteredPemilih($request->all());
        $data = $result['data'];
        $kelasList = $result['kelasList'];

        if ($request->ajax()) {
            return view('admin.partials._list-siswa-table', compact('data'))->render();
        }

        return view('admin.list-siswa', compact('data', 'kelasList'));
    }

    /**
     * Halaman Data Paslon (Kandidat)
     */
    public function kandidat()
    {
        $data = Paslon::all();
        return view('admin.kandidat', ['data' => $data]);
    }

    /**
     * Backup Database SQL
     */
    public function backupDatabase()
    {
        $backupFileName = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
        $backupFilePath = storage_path('app/backups/' . $backupFileName);

        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0777, true);
        }

        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME', 'root');

        $command = "mysqldump -h $dbHost -P $dbPort -u $dbUser $dbName tbl_voting > $backupFilePath";

        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            Alert::error('Error', 'Gagal melakukan backup database: ' . implode("\n", $output));
            return redirect()->back();
        } else {
            Alert::success('Success', 'Backup database berhasil: ' . $backupFileName);
            return redirect()->back();
        }
    }

    /**
     * Hapus Data Siswa
     */
    public function hapusSiswa($id)
    {
        $success = $this->pemilihService->hapusSiswa((int) $id);

        if ($success) {
            Alert::success('Success', 'Siswa berhasil dihapus.');
        } else {
            Alert::error('Error', 'Siswa tidak ditemukan.');
        }

        return redirect()->back();
    }

    /**
     * AJAX Total Suara
     */
    public function getTotalSuara()
    {
        $totalSuara = Voting::count();
        return response()->json(['total' => $totalSuara]);
    }

    /**
     * Update Jadwal & Status Pemilihan
     */
    public function updateJadwal(Request $request)
    {
        $this->validate($request, [
            'status_pemilihan' => 'required|in:otomatis,buka,tutup',
            'waktu_mulai' => 'nullable|string',
            'waktu_selesai' => 'nullable|string',
        ]);

        Setting::set('status_pemilihan', $request->status_pemilihan);
        if ($request->has('waktu_mulai')) {
            Setting::set('waktu_mulai', $request->waktu_mulai);
        }
        if ($request->has('waktu_selesai')) {
            Setting::set('waktu_selesai', $request->waktu_selesai);
        }

        Alert::success('Berhasil', 'Pengaturan jadwal pemilihan berhasil diperbarui.');
        return redirect()->route('dashboard');
    }

    /**
     * Layar Live Count Projector
     */
    public function liveCount()
    {
        $hasilVote = $this->hasilVoteService->getHasilVoteRekap(false);
        $totalSuara = Voting::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $persentasePartisipasi = $totalSiswa > 0 ? round(($totalSuara / $totalSiswa) * 100, 1) : 0;

        return view('admin.live-count', compact('hasilVote', 'totalSuara', 'totalSiswa', 'persentasePartisipasi'));
    }

    /**
     * Cetak Berita Acara Rekapitulasi
     */
    public function beritaAcara()
    {
        $hasilVote = $this->hasilVoteService->getHasilVoteRekap(true);
        $totalSuara = Voting::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $golput = max(0, $totalSiswa - $totalSuara);
        $persentasePartisipasi = $totalSiswa > 0 ? round(($totalSuara / $totalSiswa) * 100, 2) : 0;
        $persentaseGolput = $totalSiswa > 0 ? round(($golput / $totalSiswa) * 100, 2) : 0;
        $votingSchedule = Setting::getVotingStatus();

        return view('admin.berita-acara', compact('hasilVote', 'totalSuara', 'totalSiswa', 'golput', 'persentasePartisipasi', 'persentaseGolput', 'votingSchedule'));
    }
}
