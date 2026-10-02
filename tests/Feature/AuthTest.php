<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;

class AuthTest extends TestCase
{
    public function test_login_page_renders_successfully()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Aplikasi Pilketos');
    }

    public function test_admin_can_login_and_redirect_to_dashboard()
    {
        $response = $this->post('/login', [
            'password' => 'admin123'
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_siswa_can_login_with_nis_and_redirect_to_home()
    {
        $response = $this->post('/login', [
            'password' => '12345'
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }
}
