<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function makeMenu(array $override = []): Menu
    {
        $category = Category::firstOrCreate(['name' => 'Bakso Spesial']);

        return Menu::create(array_merge([
            'category_id'  => $category->id,
            'name'         => 'Bakso Urat Spesial',
            'description'  => 'Bakso urat besar dengan kuah gurih.',
            'price'        => 25000,
            'is_available' => true,
        ], $override));
    }

    public function test_guest_sees_menu_data_from_database_on_home(): void
    {
        $this->makeMenu();

        $this->get('/')
            ->assertOk()
            ->assertSee('Bakso Urat Spesial')
            ->assertSee('Rp 25.000')
            ->assertSee('Bakso Spesial');
    }

    public function test_unavailable_menu_shows_habis_label_by_default(): void
    {
        config(['bakso_atmo.hide_unavailable' => false]);
        $this->makeMenu(['name' => 'Menu Kosong', 'is_available' => false]);

        $this->get('/')->assertOk()->assertSee('Menu Kosong')->assertSee('Habis');
    }

    public function test_unavailable_menu_is_hidden_when_rule_says_so(): void
    {
        config(['bakso_atmo.hide_unavailable' => true]);
        $menu = $this->makeMenu(['name' => 'Menu Kosong', 'is_available' => false]);

        $this->get('/')->assertOk()->assertDontSee('Menu Kosong');
        $this->get(route('public.menus.show', $menu))->assertNotFound();
    }

    public function test_guest_can_open_menu_detail(): void
    {
        $menu = $this->makeMenu();

        $this->get(route('public.menus.show', $menu))
            ->assertOk()
            ->assertSee('Bakso Urat Spesial')
            ->assertSee('Rp 25.000')
            ->assertSee('Bakso urat besar dengan kuah gurih.')
            ->assertSee('Bakso Spesial');
    }

    public function test_missing_menu_returns_404(): void
    {
        $this->get('/menu/999999')->assertNotFound();
    }
}