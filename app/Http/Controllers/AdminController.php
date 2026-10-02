<?php

namespace App\Http\Controllers;

use App\HasilVoting;
use App\Imports\UserImport;
use App\Exports\SiswaExport;
use App\Paslon;
use App\User;
use App\Voting;
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

        $data = Paslon::all();
        $totalPaslon = count($data); // Hitung jumlah paslon
        $siswaData = User::where('role', 'siswa')
            ->with('voting') // Mengambil data voting
            ->get();
        $totalSiswa = count($siswaData); // Hitung jumlah siswa
        // Hitung total suara
        $hasilVote = [];
        for ($i = 1; $i <= 3; $i++) { // Sesuaikan jumlah paslon sesuai kebutuhan
            $jumlahVote = Voting::where('no_urut_paslon', $i)->count();
            $hasilVote[] = [
                'no_urut_paslon' => $i,
                'jumlah_vote' => $jumlahVote
            ];
        }
        $totalSuara = array_sum(array_column($hasilVote, 'jumlah_vote')); // Menghitung total suara

        return view('admin.dashboard', compact('data', 'totalPaslon','siswaData', 'totalSiswa', 'totalSuara'));
            // Mengambil data siswa yang berstatus 'siswa'
        $data = User::where('role', 'siswa')
            ->with('voting') // Mengambil data voting
            ->get();

        return view('siswa.home', ['data' => $data]);

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
        for ($i = 1; $i <= 3; $i++) { // Gantilah 3 dengan jumlah kandidat yang sebenarnya
            $hasilNoUrut = Voting::where('no_urut_paslon', $i)->count();

            // Jika hasil voting untuk no urut belum ada, buat baru
            if (!HasilVoting::where('no_urut_paslon', $i)->exists()) {
                HasilVoting::create([
                    'no_urut_paslon' => $i,
                    'jumlah_vote' => $hasilNoUrut,
                ]);
            } else {
                // Jika sudah ada, update jumlah vote
                $hasil = HasilVoting::where('no_urut_paslon', $i)->first();
                $hasil->update([
                    'jumlah_vote' => $hasilNoUrut,
                ]);
            }
        }
        return redirect('/dashboard');

    }

    public function hasilVote() {
        // Inisialisasi array untuk menyimpan hasil vote
        $hasilVote = [];

        // Loop untuk setiap nomor urut paslon
        for ($i = 1; $i <= 3; $i++) { // Sesuaikan jumlah paslon sesuai kebutuhan
            // Hitung jumlah vote untuk no urut paslon dari tabel voting
            $jumlahVote = Voting::where('no_urut_paslon', $i)->count();

            // Simpan hasil ke array
            $hasilVote[] = [
                'no_urut_paslon' => $i,
                'jumlah_vote' => $jumlahVote
            ];
        }

        // Hitung total suara
        $totalSuara = array_sum(array_column($hasilVote, 'jumlah_vote'));

        // Kembalikan view dengan data hasil vote
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

        $namaFile = rand().$file->getClientOriginalName();
        $file->move('file_user', $namaFile);

        Excel::import(new UserImport, public_path('/file_user' . '/' . $namaFile));

        Alert::success('Success', 'Import Data Berhasil');

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
        // Tangkap parameter search, perPage, dan status dari request
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10); // Default 10 data per halaman
        $status = $request->input('status'); // Tambahkan ini untuk status voting

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

        // Ambil data dengan paginasi
        $data = $query->paginate($perPage);

        // Return partial view untuk AJAX atau seluruh view jika tidak AJAX
        if ($request->ajax()) {
            return view('admin.partials._listSiswaTable', compact('data'))->render();
        }

        return view('admin.listSiswa', compact('data'));
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
        // Hitung total suara seperti sebelumnya
        $hasilVote = [];
        $totalPaslon = Paslon::count();
        for ($i = 1; $i <= $totalPaslon; $i++) {
            $jumlahVote = Voting::where('no_urut_paslon', $i)->count();
            $hasilVote[] = [
                'no_urut_paslon' => $i,
                'jumlah_vote' => $jumlahVote
            ];
        }
        $totalSuara = array_sum(array_column($hasilVote, 'jumlah_vote'));

        return response()->json(['total' => $totalSuara]);
    }

}
