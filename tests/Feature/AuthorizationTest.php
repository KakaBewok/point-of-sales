<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // ─── Unauthenticated Access ────────────────────────────────

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $routes = [
            '/dashboard',
            '/pos',
            '/products',
            '/categories',
            '/stock',
            '/vouchers',
            '/reports',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    // ─── Admin Access ──────────────────────────────────────────

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/settings');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
    }

    // ─── Cashier Cannot Access Admin Routes ────────────────────

    public function test_cashier_cannot_access_admin_routes(): void
    {
        $cashier = User::factory()->cashier()->create();

        $response = $this->actingAs($cashier)->get('/admin/settings');
        $response->assertStatus(403);

        $response = $this->actingAs($cashier)->get('/admin/users');
        $response->assertStatus(403);
    }

    // ─── Admin Can Access Shared Routes ────────────────────────

    public function test_admin_can_access_shared_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $sharedRoutes = ['/dashboard', '/pos', '/products', '/categories'];

        foreach ($sharedRoutes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
        }
    }

    // ─── User Roles ────────────────────────────────────────────

    public function test_user_is_admin_helper(): void
    {
        $admin = User::factory()->admin()->create();
        $cashier = User::factory()->cashier()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($cashier->isAdmin());
    }

    public function test_user_is_cashier_helper(): void
    {
        $cashier = User::factory()->cashier()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($cashier->isCashier());
        $this->assertFalse($admin->isCashier());
    }

    // ─── Permission Check ──────────────────────────────────────

    public function test_admin_has_all_permissions(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->hasPermission('dashboard'));
        $this->assertTrue($admin->hasPermission('pos'));
        $this->assertTrue($admin->hasPermission('anything'));
    }

    public function test_cashier_only_has_assigned_permissions(): void
    {
        $cashier = User::factory()->cashier()->create([
            'permissions' => ['dashboard', 'pos'],
        ]);

        $this->assertTrue($cashier->hasPermission('dashboard'));
        $this->assertTrue($cashier->hasPermission('pos'));
        $this->assertFalse($cashier->hasPermission('reports'));
        $this->assertFalse($cashier->hasPermission('categories'));
    }
}
