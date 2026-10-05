<?php

namespace App\Http\Controllers;

use App\HasilVoting;
use App\Imports\UserImport;
use App\Exports\SiswaExport;
use App\Paslon;
use App\User;
use App\Voting;
use App\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log; // Impor facade Log untuk logging
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    public function index() {

        $data = Paslon::orderBy('no_urut_paslon', 'asc')->get();
        $totalPaslon = count($data); // Hitung jumlah paslon
        $siswaData = User::where('role', 'siswa')
            ->with('voting') // Mengambil data voting
            ->get();
        $totalSiswa = count($siswaData); // Hitung jumlah siswa
        // Hitung total suara
        $totalSuara = Voting::count();

        // Statistik Partisipasi per Kelas (Fitur 3)
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

        // Urutkan kelas secara natural
        usort($kelasStats, function($a, $b) {
            return strnatcasecmp($a['kelas'], $b['kelas']);
        });

        // Pengaturan & Jadwal Pemilihan (Fitur 1)
        $votingSchedule = Setting::getVotingStatus();

        return view('admin.dashboard', compact('data', 'totalPaslon','siswaData', 'totalSiswa', 'totalSuara', 'kelasStats', 'votingSchedule'));
    }

    public function hapus( $id ) {

        $data = Paslon::find($id);

        $imgKetua = $data->img_ketua;
        $imgWakil = $data->img_wakil;

        File::delete('img_ketua/' . $imgKetua);
        File::delete('img_wakil/' . $imgWakil);

        $data->delete();

        Alert::success('Success', 'Data Berhasil Di Hapus');


        return redirect('/dashboard');

    }

    public function viewTambah() {

        return view('admin.tambah');

    }

    public function prosesTambah( Request $request ) {

        $this->validate($request, [

            'no_urut_paslon' => 'required|integer|unique:tbl_paslon,no_urut_paslon',
            'ketua_paslon' => 'required',
            // 'wakil_paslon' => 'required',
            'visi_paslon' => 'required',
            'misi_paslon' => 'required',
            'img_ketua' => 'required|max:2000|file|mimes:jpg,png,jpeg|image',
            // 'img_wakil' => 'required|max:2000|file|mimes:jpg,png,jpeg|image'

        ]);

        $imgKetua = $request->file('img_ketua');
        // $imgWakil = $request->file('img_wakil');

        $namaFileKetua = time() . '_' . $imgKetua->getClientOriginalName();
        // $namaFileWakil = time() . '_' . $imgWakil->getClientOriginalName();

        $folderKetua = 'img_ketua';
        // $folderWakil = 'img_wakil';

        Paslon::create([

            'no_urut_paslon' => $request->no_urut_paslon,
            'ketua_paslon' => $request->ketua_paslon,
            // 'wakil_paslon' => $request->wakil_paslon,
            'visi_paslon' => $request->visi_paslon,
            'misi_paslon' => $request->misi_paslon,
            'img_ketua' => $namaFileKetua,
            // 'img_wakil' => $namaFileWakil

        ]);

        $imgKetua->move($folderKetua, $namaFileKetua);
        // $imgWakil->move($folderWakil, $namaFileWakil);

        Alert::success('Success', 'Upload Paslon Berhasil');


        return redirect()->route('dashboard');

    }

    public function edit( $id ) {

        $data = Paslon::find($id);
        return view('admin.edit', ['data' => $data]);

    }

    public function prosesEdit($id, Request $request)
    {
        $this->validate($request, [
            'no_urut_paslon' => 'required|integer',
            'ketua_paslon' => 'required',
            'visi_paslon' => 'required',
            'misi_paslon' => 'required',
            'img_ketua' => 'nullable|max:2000|file|mimes:jpg,png,jpeg|image',
        ]);

        $data = Paslon::find($id);
        $data->no_urut_paslon = $request->no_urut_paslon;
        $data->ketua_paslon = $request->ketua_paslon;
        $data->visi_paslon = $request->visi_paslon;
        $data->misi_paslon = $request->misi_paslon;

        // Cek jika ada gambar baru yang diunggah
        if ($request->file('img_ketua')) {
            $imgKetua = $request->file('img_ketua');

            // Nama File
            $namaFileKetua = time() . '_' . $imgKetua->getClientOriginalName();
            $folderKetua = 'img_ketua'; // Pastikan folder ini ada dalam folder public

            // Masukkan Gambar Ke Dalam Folder
            $imgKetua->move(public_path($folderKetua), $namaFileKetua);

            // Hapus File Gambar Lama jika ada
            if ($data->img_ketua) {
                File::delete(public_path($folderKetua . '/' . $data->img_ketua));
            }

            // Simpan nama file baru
            $data->img_ketua = $namaFileKetua;
        }

        // Simpan data lainnya
        $data->save();

        Alert::success('Success', 'Data Berhasil Di Ubah');
        return redirect('/kandidat');
    }


    public function detail( $id ) {

        $data = Paslon::find($id);
        return view('admin.detail', ['data' => $data]);

    }

    public function registerSiswa() {

        return view('admin.registerSiswa');

    }

    public function prosesRegisterSiswa( Request $request ) {

        $this->validate($request, [

            'username' => 'required',
            'nama_panjang' => 'required',
            'kelas' => 'required',
            'password' => 'required'

        ]);

        User::create([

            'username' => $request->username,
            'nama_panjang' => $request->nama_panjang,
            'kelas' => $request->kelas,
            'role' => 'siswa',
            'password' => $request->password

        ]);

        Alert::success('Success', 'Register Siswa Berhasil');
        return redirect('/registerSiswa');

    }

    public function voteSelesai() {
        $paslons = Paslon::all();
        foreach ($paslons as $p) {
            $noUrut = $p->no_urut_paslon;
            $hasilNoUrut = Voting::where('no_urut_paslon', $noUrut)->count();

            HasilVoting::updateOrCreate(
                ['no_urut_paslon' => $noUrut],
                ['jumlah_vote' => $hasilNoUrut]
            );
        }
        Alert::success('Success', 'Pemilihan telah resmi diselesaikan!');
        return redirect('/dashboard');
    }

    public function hasilVote() {
        $paslons = Paslon::orderBy('no_urut_paslon', 'asc')->get();
        $hasilVote = [];

        foreach ($paslons as $p) {
            $noUrut = $p->no_urut_paslon;
            $jumlahVote = Voting::where('no_urut_paslon', $noUrut)->count();
            $hasilVote[] = [
                'no_urut_paslon' => $noUrut,
                'ketua_paslon' => $p->ketua_paslon,
                'jumlah_vote' => $jumlahVote
            ];
        }

        $totalSuara = Voting::count();

        return view('admin.hasilVote', ['data' => $hasilVote, 'totalSuara' => $totalSuara]);
    }


    public function importSiswa() {

        return view('admin.importSiswa');

    }

    public function exportExcel()
    {
        // Ambil data siswa dari database
        $siswa = User::where('role', 'siswa')->get();

        // Log jumlah data siswa untuk pemeriksaan
        Log::info('Jumlah Siswa yang ditemukan: ' . $siswa->count());

        // Cek jika tidak ada data siswa
        if ($siswa->isEmpty()) {
            Log::warning('Tidak ada siswa ditemukan untuk diekspor.');
            return redirect()->back()->with('error', 'Tidak ada data siswa untuk diekspor.');
        }

        // Kembalikan file Excel
        return Excel::download(new SiswaExport, 'siswa.xlsx');
    }

    public function importExcel( Request $request ) {

        $this->validate($request, [
            'file_excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        $file = $request->file('file_excel');
        $namaFile = time() . '_' . rand(1000, 9999) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $destinationPath = public_path('file_user');

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        $filePath = $destinationPath . DIRECTORY_SEPARATOR . $namaFile;
        $file->move($destinationPath, $namaFile);

        try {
            Excel::import(new UserImport, $filePath);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            Alert::success('Success', 'Import Data Berhasil');
        } catch (\Throwable $e) {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            Log::error('Import error: ' . $e->getMessage());
            Alert::error('Gagal Import', 'Terjadi kesalahan saat import data: ' . $e->getMessage());
        }

        return redirect('/dashboard');
    }

    public function ulangVoting() {
        // Hapus semua data di tabel HasilVoting
        HasilVoting::truncate();

        // Hapus semua data di tabel voting jika perlu (opsional)
        Voting::truncate();

        Alert::success('Success', 'Voting telah diulang');
        return redirect('/dashboard');
    }

    public function listSiswa(Request $request)
    {
        // Tangkap parameter search, perPage, status, dan kelas dari request
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10); // Default 10 data per halaman
        $status = $request->input('status'); // Status voting
        $kelas = $request->input('kelas'); // Filter kelas

        // Query siswa dengan kondisi pencarian
        $query = User::where('role', 'siswa')
            ->with('voting');

        // Filter berdasarkan nama panjang atau username
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_panjang', 'like', '%' . $search . '%')
                ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan status voting
        if ($status === 'voted') {
            $query->whereHas('voting'); // Menampilkan siswa yang sudah memilih
        } elseif ($status === 'not_voted') {
            $query->doesntHave('voting'); // Menampilkan siswa yang belum memilih
        }

        // Filter berdasarkan kelas
        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        // Ambil data dengan paginasi
        $data = $query->paginate($perPage)->withQueryString();

        // Ambil daftar kelas unik untuk dropdown filter
        $kelasList = User::where('role', 'siswa')
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        // Return partial view untuk AJAX atau seluruh view jika tidak AJAX
        if ($request->ajax()) {
            return view('admin.partials._listSiswaTable', compact('data'))->render();
        }

        return view('admin.listSiswa', compact('data', 'kelasList'));
    }



    public function kandidat()
    {
        // Ambil semua data paslon
        $data = Paslon::all();

        // Kembalikan view dengan data paslon
        return view('admin.kandidat', ['data' => $data]);
    }

    public function backupDatabase()
    {
        $backupFileName = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
        $backupFilePath = storage_path('app/backups/' . $backupFileName);

        // Pastikan direktori backups sudah ada
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0777, true);
        }

        // Ambil konfigurasi dari .env
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME', 'root');

        // Perintah mysqldump
        $command = "mysqldump -h $dbHost -P $dbPort -u $dbUser $dbName tbl_voting > $backupFilePath";

        // Eksekusi perintah
        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        // Cek hasil eksekusi
        if ($returnVar !== 0) {
            Alert::error('Error', 'Gagal melakukan backup database: ' . implode("\n", $output));
            return redirect()->back();
        } else {
            Alert::success('Success', 'Backup database berhasil: ' . $backupFileName);
            return redirect()->back();
        }
    }

    public function hapusSiswa($id)
    {
        $siswa = User::find($id);

        if ($siswa) {
            $siswa->delete();
            Alert::success('Success', 'Siswa berhasil dihapus.');
        } else {
            Alert::error('Error', 'Siswa tidak ditemukan.');
        }

        return redirect()->back(); // Kembali ke halaman sebelumnya
    }

    public function getTotalSuara() {
        $totalSuara = Voting::count();
        return response()->json(['total' => $totalSuara]);
    }

    /**
     * Memperbarui pengaturan jadwal pemilihan (Fitur 1)
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
     * Tampilan Layar Live Count Fullscreen untuk Proyektor Aula / TV Lobi (Fitur 4)
     */
    public function liveCount()
    {
        $paslons = Paslon::orderBy('no_urut_paslon', 'asc')->get();
        $totalSuara = Voting::count();
        $totalSiswa = User::where('role', 'siswa')->count();

        $hasilVote = [];
        foreach ($paslons as $p) {
            $count = Voting::where('no_urut_paslon', $p->no_urut_paslon)->count();
            $pct = $totalSuara > 0 ? round(($count / $totalSuara) * 100, 1) : 0;
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

        $persentasePartisipasi = $totalSiswa > 0 ? round(($totalSuara / $totalSiswa) * 100, 1) : 0;

        return view('admin.liveCount', compact('hasilVote', 'totalSuara', 'totalSiswa', 'persentasePartisipasi'));
    }

    /**
     * Cetak Laporan & Berita Acara Rekapitulasi Resmi Pemilihan (Fitur 5)
     */
    public function beritaAcara()
    {
        $paslons = Paslon::orderBy('no_urut_paslon', 'asc')->get();
        $totalSuara = Voting::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $golput = max(0, $totalSiswa - $totalSuara);
        $persentasePartisipasi = $totalSiswa > 0 ? round(($totalSuara / $totalSiswa) * 100, 2) : 0;
        $persentaseGolput = $totalSiswa > 0 ? round(($golput / $totalSiswa) * 100, 2) : 0;

        $hasilVote = [];
        foreach ($paslons as $p) {
            $count = Voting::where('no_urut_paslon', $p->no_urut_paslon)->count();
            $pct = $totalSuara > 0 ? round(($count / $totalSuara) * 100, 2) : 0;
            $hasilVote[] = [
                'no_urut_paslon' => $p->no_urut_paslon,
                'ketua_paslon' => $p->ketua_paslon,
                'wakil_paslon' => $p->wakil_paslon,
                'jumlah_vote' => $count,
                'percentage' => $pct
            ];
        }

        $votingSchedule = Setting::getVotingStatus();

        return view('admin.beritaAcara', compact('hasilVote', 'totalSuara', 'totalSiswa', 'golput', 'persentasePartisipasi', 'persentaseGolput', 'votingSchedule'));
    }

}
