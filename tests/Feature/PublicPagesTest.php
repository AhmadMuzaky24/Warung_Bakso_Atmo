<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_guest_can_open_home(): void
    {
        $this->get('/')->assertOk()->assertSee(config('bakso_atmo.name'));
    }

    public function test_guest_can_open_about(): void
    {
        $this->get('/perjalanan-kami')->assertOk()->assertSee('Perjalanan kami');
    }

    public function test_home_shows_branches_and_marketplace_links(): void
    {
        config([
            'bakso_atmo.gofood'     => 'https://gofood.example/test',
            'bakso_atmo.shopeefood' => 'https://shopeefood.example/test',
        ]);

        $response = $this->get('/')->assertOk();

        foreach (config('bakso_atmo.branches') as $branch) {
            $response->assertSee($branch['name']);
        }

        $response->assertSee('href="https://gofood.example/test"', false)
                 ->assertSee('href="https://shopeefood.example/test"', false)
                 ->assertSee('target="_blank"', false)
                 ->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_old_location_url_redirects_to_home_section(): void
    {
        $this->get('/lokasi')->assertRedirect('/#lokasi');
    }

    public function test_public_routes_do_not_use_auth_middleware(): void
    {
        $publicRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => str_starts_with((string) $r->getName(), 'public.'));

        $this->assertNotEmpty($publicRoutes, 'Tidak ada route public.* terdaftar.');

        foreach ($publicRoutes as $route) {
            foreach ($route->gatherMiddleware() as $middleware) {
                $this->assertStringNotContainsString(
                    'auth', (string) $middleware,
                    "Route {$route->getName()} memakai middleware {$middleware}"
                );
                $this->assertStringNotContainsString('role', (string) $middleware);
            }
        }
    }

    public function test_no_cart_or_checkout_routes_exist(): void
    {
        foreach (Route::getRoutes()->getRoutes() as $route) {
            $this->assertDoesNotMatchRegularExpression(
                '/cart|checkout|keranjang/i',
                $route->uri() . ' ' . $route->getName(),
                "Route terlarang ditemukan: {$route->uri()}"
            );
        }
    }
}