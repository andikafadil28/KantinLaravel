<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacyBridgeTest extends TestCase
{
    public function test_old_home_route_redirects_to_native_app_home(): void
    {
        $response = $this->get('/home');

        $response->assertRedirect('/app/home');
    }

    public function test_legacy_login_page_is_served_from_bridge(): void
    {
        $response = $this->get('/legacy/login');

        $response->assertOk();
        $response->assertSee('Please sign in');
    }

    public function test_legacy_asset_is_served_through_laravel_route(): void
    {
        $response = $this->get('/legacy/assets/dist/css/bootstrap.min.css');

        $response->assertOk();
        $response->assertHeader('content-type');
    }

    public function test_sensitive_legacy_files_are_not_exposed(): void
    {
        $this->get('/legacy/composer.json')->assertNotFound();
        $this->get('/legacy/vendor/autoload.php')->assertNotFound();
    }

    public function test_old_logout_get_route_no_longer_logs_out_user(): void
    {
        $response = $this->get('/logout');

        $response->assertRedirect('/app/login');
    }

    public function test_legacy_login_post_is_not_blocked_by_csrf(): void
    {
        $response = $this->post('/legacy/validate/validate_login.php', [
            'username' => 'user-tidak-ada',
            'password' => 'salah',
            'submit_validate' => 'isi',
        ]);

        $response->assertStatus(200);
    }

    public function test_legacy_login_post_still_works_with_invalid_php_session_cookie(): void
    {
        $response = $this
            ->withCookie('PHPSESSID', 'eyJinvalid/session==')
            ->post('/legacy/validate/validate_login.php', [
                'username' => 'user-tidak-ada',
                'password' => 'salah',
                'submit_validate' => 'isi',
            ]);

        $response->assertStatus(200);
    }
}
