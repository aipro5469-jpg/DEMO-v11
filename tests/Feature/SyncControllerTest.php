<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SyncControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'sales_associate']);
    }

    public function test_super_admin_can_see_all_clients()
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        // Create another user to be the creator
        $otherUser = User::factory()->create();

        // Create clients created by others and not assigned to this user
        Client::factory()->count(3)->create([
            'created_by' => $otherUser->id,
            'agent_id' => null,
        ]);

        $response = $this->getJson('/api/sync/pull?entities[]=clients');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data.clients'));
    }

    public function test_manager_can_see_all_clients()
    {
        $user = User::factory()->create();
        $user->assignRole('manager');
        $this->actingAs($user);

        $otherUser = User::factory()->create();

        Client::factory()->count(3)->create([
            'created_by' => $otherUser->id,
            'agent_id' => null,
        ]);

        $response = $this->getJson('/api/sync/pull?entities[]=clients');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data.clients'));
    }

    public function test_regular_user_can_only_see_their_clients()
    {
        $user = User::factory()->create();
        $user->assignRole('sales_associate');

        $otherUser = User::factory()->create();

        // Create client by other user (before login as user)
        $this->actingAs($otherUser);
        Client::factory()->create([
            'created_by' => $otherUser->id, // This will be respected or overwritten by actingAs(otherUser) which is same
            'agent_id' => null,
        ]);

        // Now login as regular user
        $this->actingAs($user);

        // Client created by user
        Client::factory()->create([
            'created_by' => $user->id,
            'agent_id' => null,
        ]);

        $response = $this->getJson('/api/sync/pull?entities[]=clients');

        $response->assertStatus(200);
        // Should only see 1 client (the one created by user)
        $this->assertCount(1, $response->json('data.clients'));
    }
}
