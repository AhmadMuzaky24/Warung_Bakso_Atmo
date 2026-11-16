<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryMenuManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_non_admin_cannot_access_category_and_menu_management(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
        $this->get(route('admin.menus.index'))->assertRedirect(route('login'));

        $this->actingAs($staff)
            ->get(route('admin.categories.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('admin.menus.index'))
            ->assertForbidden();
    }

    public function test_admin_can_crud_category_and_cannot_delete_category_with_menus(): void
    {
        $admin = $this->makeAdmin();

        // Create
        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Bakso Spesial',
                'description' => 'Menu andalan',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::where('name', 'Bakso Spesial')->firstOrFail();

        // Update
        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Bakso Super Spesial',
                'description' => 'Updated deskripsi',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Bakso Super Spesial']);

        // Tambah menu ke kategori ini
        $menu = Menu::factory()->create(['category_id' => $category->id]);

        // Gagal hapus karena masih punya menu
        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertSessionHasErrors('delete');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);

        // Hapus menu lalu kategori bisa dihapus
        $menu->delete();

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_can_crud_menu_with_photo_upload_and_availability(): void
    {
        Storage::fake('public');

        $admin = $this->makeAdmin();
        $category = Category::factory()->create();
        $photo = UploadedFile::fake()->image('bakso.jpg', 400, 400);

        // Store dengan foto
        $this->actingAs($admin)
            ->post(route('admin.menus.store'), [
                'category_id' => $category->id,
                'name' => 'Bakso Urat',
                'description' => 'Enak dan kenyal',
                'price' => 25000,
                'image' => $photo,
                'is_available' => '1',
            ])
            ->assertRedirect(route('admin.menus.index'));

        $menu = Menu::where('name', 'Bakso Urat')->firstOrFail();
        $this->assertNotNull($menu->image_path);
        Storage::disk('public')->assertExists($menu->image_path);

        // Update harga, ketersediaan, dan ganti foto baru
        $oldPath = $menu->image_path;
        $newPhoto = UploadedFile::fake()->image('bakso-baru.png', 400, 400);

        $this->actingAs($admin)
            ->put(route('admin.menus.update', $menu), [
                'category_id' => $category->id,
                'name' => 'Bakso Urat Jumbo',
                'description' => 'Porsi besar',
                'price' => 30000,
                'image' => $newPhoto,
                'is_available' => '0',
            ])
            ->assertRedirect(route('admin.menus.index'));

        $menu->refresh();
        $this->assertSame(30000, $menu->price);
        $this->assertFalse($menu->is_available);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($menu->image_path);

        // Destroy menu menghapus file foto
        $currentPath = $menu->image_path;

        $this->actingAs($admin)
            ->delete(route('admin.menus.destroy', $menu))
            ->assertRedirect(route('admin.menus.index'));

        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
        Storage::disk('public')->assertMissing($currentPath);
    }

    public function test_menu_validation_rejects_negative_price_and_invalid_image(): void
    {
        Storage::fake('public');

        $admin = $this->makeAdmin();
        $category = Category::factory()->create();
        $invalidFile = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');

        $this->actingAs($admin)
            ->post(route('admin.menus.store'), [
                'category_id' => $category->id,
                'name' => 'Menu Invalid',
                'price' => -5000,
                'image' => $invalidFile,
                'is_available' => '1',
            ])
            ->assertSessionHasErrors(['price', 'image']);
    }
}