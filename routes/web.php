<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/login', function () {

    return view('auth.login');

})->name('login');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function() {

    return redirect('/login');

});


Route::group(['middleware' => ['auth' => 'CekRole:admin']], function() {

    Route::get('/dashboard', 'AdminController@index')->name('dashboard');
    Route::get('/hapus/{id}', 'AdminController@hapus');
    Route::get('/tambah', 'AdminController@viewTambah');
    Route::post('/proses_tambah', 'AdminController@prosesTambah');
    Route::get('/edit/{id}', 'AdminController@edit');
    Route::put('/proses_edit/{id}', 'AdminController@prosesEdit');
    Route::get('/voteSelesai', 'AdminController@voteSelesai');
    Route::get('/registerSiswa', 'AdminController@registerSiswa');
    Route::post('/prosesRegisterSiswa', 'AdminController@prosesRegisterSiswa');
    Route::get('/detailPaslon/{id}', 'AdminController@detail');
    Route::get('/user/importSiswa', 'AdminController@importSiswa');
    Route::post('/user/import_excel', 'AdminController@importExcel');
    Route::get('/user/export_excel', 'AdminController@exportExcel')->name('user.exportExcel');
    Route::get('/ulangVoting', 'AdminController@ulangVoting')->name('ulangVoting');
    Route::get('/listSiswa', 'AdminController@listSiswa')->name('listSiswa');
    Route::get('/user/hasil_vote_ajax', 'AdminController@hasilVoteAjax');
    Route::get('/kandidat', 'AdminController@kandidat')->name('kandidat');
    Route::get('/backup', 'AdminController@backupDatabase')->name('backup.database');
    Route::delete('/siswa/{id}','AdminController@hapusSiswa')->name('siswa.hapus');
    Route::get('/get-total-suara', 'AdminController@getTotalSuara')->name('get.total.suara');
    Route::post('/update-jadwal', 'AdminController@updateJadwal')->name('admin.updateJadwal');
    Route::get('/berita-acara', 'AdminController@beritaAcara')->name('admin.beritaAcara');
});

Route::group(['middleware' => ['auth' => 'CekRole:admin,siswa']], function() {

    Route::get('/hasilVote', 'AdminController@hasilVote');
    Route::get('/live-count', 'AdminController@liveCount')->name('admin.liveCount');

});

Auth::routes();

Route::group(['middleware' => ['auth' => 'CekRole:siswa']], function() {

    Route::get('/home', 'siswaController@index')->name('home');
    Route::get('/detail/{id}', 'siswaController@detail');
    Route::get('/pilihPaslon/{id}', 'siswaController@pilihPaslon');

});
