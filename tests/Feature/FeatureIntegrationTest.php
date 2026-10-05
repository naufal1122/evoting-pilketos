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

    public function test_user_role_badges_rendered_with_dark_badge()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $responseAdmin = $this->actingAs($admin)->get('/dashboard');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('badge-dark');
        $responseAdmin->assertSee('Admin');

        $siswa = User::where('role', 'siswa')->first();
        $this->assertNotNull($siswa);

        $responseSiswa = $this->actingAs($siswa)->get('/home');
        $responseSiswa->assertStatus(200);
        $responseSiswa->assertSee('badge-dark');
        $responseSiswa->assertSee('Siswa');
    }
}

