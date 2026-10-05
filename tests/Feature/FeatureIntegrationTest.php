<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Paslon;
use App\Voting;
use App\HasilVoting;
use Illuminate\Support\Facades\DB;
use App\Exports\SiswaExport;
use App\Imports\UserImport;

class FeatureIntegrationTest extends TestCase
{
    public function test_admin_username_is_admin()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('admin', $admin->username);
    }

    public function test_siswa_export_collection_and_mapping()
    {
        $export = new SiswaExport();
        $collection = $export->collection();
        $this->assertTrue($collection->isNotEmpty());

        $firstRow = $collection->first();
        $mapped = $export->map($firstRow);

        $this->assertIsArray($mapped);
        $this->assertCount(4, $mapped);
        $this->assertEquals($firstRow->username, $mapped[0]);
    }

    public function test_siswa_import_logic()
    {
        $import = new UserImport();
        $testUsername = 'test_unit_' . time();
        $model = $import->model([
            'username' => $testUsername,
            'nama_panjang' => 'Test Siswa Unit',
            'kelas' => 'XII RPL 1',
            'nis' => '99887766'
        ]);

        $this->assertNotNull($model);
        $this->assertEquals($testUsername, $model->username);
        $this->assertEquals('Test Siswa Unit', $model->nama_panjang);
        $this->assertEquals('XII RPL 1', $model->kelas);
        $this->assertEquals('99887766', $model->password);
        $this->assertEquals('siswa', $model->role);
    }

    public function test_user_avatar_url_attribute()
    {
        $user = new User([
            'username' => 'budi',
            'nama_panjang' => 'Budi Santoso',
            'role' => 'siswa'
        ]);

        $avatarUrl = $user->avatar_url;
        $this->assertStringContainsString('ui-avatars.com', $avatarUrl);
        $this->assertStringContainsString('Budi+Santoso', $avatarUrl);
        $this->assertStringContainsString('color=ffffff', $avatarUrl);
        $this->assertMatchesRegularExpression('/background=[0-9A-Fa-f]{6}/', $avatarUrl);

        // Pastikan pengguna berbeda menghasilkan warna deterministik dari palet
        $user2 = new User([
            'username' => 'siti',
            'nama_panjang' => 'Siti Aminah',
            'role' => 'siswa'
        ]);
        $avatarUrl2 = $user2->avatar_url;
        $this->assertStringContainsString('ui-avatars.com', $avatarUrl2);
        $this->assertStringContainsString('Siti+Aminah', $avatarUrl2);
        $this->assertMatchesRegularExpression('/background=[0-9A-Fa-f]{6}/', $avatarUrl2);
        // Pastikan bukan default hijau statis untuk semua user jika hash berbeda
        $this->assertNotEquals($avatarUrl, $avatarUrl2);
    }

    public function test_voting_prevents_duplicate_vote()
    {
        $siswa = User::where('role', 'siswa')->first();
        $this->assertNotNull($siswa);

        $paslon = Paslon::first();
        if (!$paslon) {
            $paslon = Paslon::create([
                'no_urut_paslon' => 1,
                'ketua_paslon' => 'Calon Test',
                'visi_paslon' => 'Visi Test',
                'misi_paslon' => 'Misi Test',
                'img_ketua' => 'default.png'
            ]);
        }

        // Clean previous vote for test user
        Voting::where('id_user', $siswa->id)->delete();

        // Vote first time
        $response = $this->actingAs($siswa)->get('/pilihPaslon/' . $paslon->id, [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Attempt double vote
        $response2 = $this->actingAs($siswa)->get('/pilihPaslon/' . $paslon->id, [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);
        $response2->assertStatus(422);
        $response2->assertJson(['success' => false]);
    }

    public function test_admin_list_siswa_kelas_filter()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // Make sure a student with a specific class exists
        $testSiswa = User::where('role', 'siswa')->whereNotNull('kelas')->first();
        if ($testSiswa) {
            $response = $this->actingAs($admin)->get('/listSiswa?kelas=' . urlencode($testSiswa->kelas));
            $response->assertStatus(200);
            $response->assertSee($testSiswa->kelas);
            $response->assertViewHas('kelasList');
            $kelasList = $response->viewData('kelasList');
            $this->assertTrue($kelasList->contains($testSiswa->kelas));

            // Test AJAX partial response
            $ajaxResponse = $this->actingAs($admin)->get('/listSiswa?kelas=' . urlencode($testSiswa->kelas), [
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ]);
            $ajaxResponse->assertStatus(200);
            $ajaxResponse->assertSee($testSiswa->kelas);

            // Test pagination links preserve query params
            $paginator = $response->viewData('data');
            if ($paginator->hasPages()) {
                $this->assertStringContainsString('kelas=', $paginator->nextPageUrl());
            }
        }
    }

    public function test_admin_hasil_vote_view()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/hasilVote');
        $response->assertStatus(200);
        $response->assertSee('Hasil Vote');
        $response->assertDontSee('bg-primary mt-n4');
    }

    public function test_user_role_labels_rendered_simply()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $responseAdmin = $this->actingAs($admin)->get('/dashboard');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertDontSee('badge-dark');
        $responseAdmin->assertSee('Admin');

        $siswa = User::where('role', 'siswa')->first();
        $this->assertNotNull($siswa);

        $responseSiswa = $this->actingAs($siswa)->get('/home');
        $responseSiswa->assertStatus(200);
        $responseSiswa->assertDontSee('badge-dark');
        $responseSiswa->assertSee('Siswa');
    }

    public function test_voting_schedule_setting_and_blocking_when_closed()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // Update schedule to manual close
        $response = $this->actingAs($admin)->post('/update-jadwal', [
            'status_pemilihan' => 'tutup',
            'waktu_mulai' => now()->subDay()->format('Y-m-d H:i'),
            'waktu_selesai' => now()->addDay()->format('Y-m-d H:i'),
        ]);
        $response->assertRedirect(route('dashboard'));

        $this->assertEquals('tutup', \App\Setting::get('status_pemilihan'));

        // Fresh student attempting to vote while closed
        $siswa = User::create([
            'username' => 'siswatest_schedule',
            'nama_panjang' => 'Siswa Test Schedule',
            'kelas' => 'XII RPL 1',
            'role' => 'siswa',
            'password' => 'schedule123',
        ]);
        $paslon = \App\Paslon::first();

        $voteResponse = $this->actingAs($siswa)->get('/pilihPaslon/' . $paslon->id, [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);

        $voteResponse->assertStatus(422);
        $voteResponse->assertJson(['success' => false]);

        // Re-open
        \App\Setting::set('status_pemilihan', 'buka');

        // Clean up
        $siswa->delete();
    }

    public function test_admin_dashboard_shows_class_participation_and_schedule()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('kelasStats');
        $response->assertViewHas('votingSchedule');
        $response->assertSee('Tingkat Partisipasi Pemilih per Kelas');
        $response->assertSee('Status Waktu Pemilihan');
        $response->assertSee('Simpan Jadwal');
    }

    public function test_live_count_projector_view_renders()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/live-count');
        $response->assertStatus(200);
        $response->assertSee('LIVE COUNT PEMILIHAN KETUA OSIS');
        $response->assertSee('persentasePartisipasiDisplay');
        $response->assertSee('Total Daftar Pemilih (DPT)');
    }

    public function test_berita_acara_view_renders_with_official_rekap()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/berita-acara');
        $response->assertStatus(200);
        $response->assertSee('BERITA ACARA REKAPITULASI HASIL PENGHITUNGAN SUARA');
        $response->assertSee('Pembina OSIS');
        $response->assertSee('Ketua Panitia Pelaksana');
        $response->assertSee('Cetak / Simpan PDF');
    }

    public function test_admin_can_upload_paslon_without_wakil()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        \Illuminate\Support\Facades\Storage::fake('public');
        $fakeImage = \Illuminate\Http\UploadedFile::fake()->image('hatsune_miku.png');

        $maxNoUrut = (int) (\App\Paslon::max('no_urut_paslon') ?? 0) + 1;

        $response = $this->actingAs($admin)->post('/proses_tambah', [
            'no_urut_paslon' => $maxNoUrut,
            'ketua_paslon' => 'Hatsune Miku',
            'visi_paslon' => 'Mewujudkan OSIS yang kreatif',
            'misi_paslon' => 'Misi ceria dan berprestasi',
            'img_ketua' => $fakeImage,
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tbl_paslon', [
            'no_urut_paslon' => $maxNoUrut,
            'ketua_paslon' => 'Hatsune Miku',
        ]);
    }
}


