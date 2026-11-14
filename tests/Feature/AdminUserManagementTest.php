<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    private function createCustomer(): User
    {
        return User::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_user_list(): void
    {
        $admin = $this->createAdmin();

        $customer = $this->createCustomer();

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee($customer->name);
        $response->assertSee($customer->email);
    }

    public function test_admin_can_search_users(): void
    {
        $admin = $this->createAdmin();

        $customer = $this->createCustomer();

        $response = $this->actingAs($admin)
            ->get(route('admin.users.index', [
                'search' => $customer->email,
            ]));

        $response->assertOk();
        $response->assertSee($customer->email);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Staff Baru',
                'email' => 'staff.baru@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => 'staff',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'staff.baru@example.com',
            'role' => 'staff',
            'is_active' => true,
        ]);

        $user = User::where('email', 'staff.baru@example.com')->firstOrFail();

        $this->assertTrue(
            Hash::check('Password123!', $user->password)
        );
    }

    public function test_admin_cannot_create_user_with_duplicate_email(): void
    {
        $admin = $this->createAdmin();

        $existingUser = $this->createCustomer();

        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Pengguna Duplikat',
                'email' => $existingUser->email,
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => 'customer',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_update_user_without_changing_password(): void
    {
        $admin = $this->createAdmin();

        $customer = $this->createCustomer();

        $oldPassword = $customer->password;

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $customer), [
                'name' => 'Nama Diperbarui',
                'email' => $customer->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => 'staff',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'Nama Diperbarui',
            'role' => 'staff',
        ]);

        $this->assertSame(
            $oldPassword,
            $customer->fresh()->password
        );
    }

    public function test_admin_can_update_user_password(): void
    {
        $admin = $this->createAdmin();

        $customer = $this->createCustomer();

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $customer), [
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
                'role' => 'customer',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertTrue(
            Hash::check(
                'NewPassword123!',
                $customer->fresh()->password
            )
        );
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin));

        $response->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => 'admin',
                'is_active' => '0',
            ]);

        $response->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = $this->createAdmin();

        $customer = $this->createCustomer();

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $customer));

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $customer->id,
        ]);
    }

    public function test_last_active_admin_cannot_be_demoted_or_deactivated(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => 'staff',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_delete_another_admin_when_it_is_not_the_last_active_admin(): void
    {
        $admin = $this->createAdmin();

        $anotherAdmin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $anotherAdmin));

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $anotherAdmin->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
