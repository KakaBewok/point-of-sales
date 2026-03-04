<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StoreRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_store_and_owner_can_register(): void
    {
        Livewire::test(\App\Livewire\StoreRegistration::class)
            ->set('storeName', 'My New Store')
            ->set('storeSlug', 'my-new-store')
            ->set('ownerName', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        // Assert Owner User Created
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'owner',
        ]);

        $user = User::where('email', 'john@example.com')->first();

        // Assert Store Created
        $this->assertDatabaseHas('stores', [
            'id' => $user->store_id,
            'name' => 'My New Store',
            'slug' => 'my-new-store',
            'subscription_status' => 'active',
        ]);

        // Assert Default Category Created
        $this->assertDatabaseHas('categories', [
            'store_id' => $user->store_id,
            'name' => 'Umum',
        ]);

        // Assert Settings Created
        $this->assertDatabaseHas('settings', [
            'store_id' => $user->store_id,
            'key' => 'store_name',
            'value' => 'My New Store',
        ]);

        // Assert Authenticated
        $this->assertAuthenticatedAs($user);
    }
}
