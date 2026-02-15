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
}
