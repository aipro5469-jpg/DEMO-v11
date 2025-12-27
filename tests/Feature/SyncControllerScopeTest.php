<?php

namespace Tests\Feature;

use App\Models\FieldReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Laravel\Sanctum\Sanctum;

class SyncControllerScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles if necessary or create them
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'agent']);
    }

    /**
     * Test that field reports are scoped to the reporter for non-admin/manager users.
     */
    public function test_field_reports_are_scoped_for_regular_users()
    {
        // Create users
        $user = User::factory()->create();
        $user->assignRole('agent');

        $otherUser = User::factory()->create();
        $otherUser->assignRole('agent');

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        // Create field reports
        $myReport = FieldReport::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'notes' => 'My report',
            'reporter_id' => $user->id,
            'updated_at' => now(),
        ]);

        $otherReport = FieldReport::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'notes' => 'Other report',
            'reporter_id' => $otherUser->id,
            'updated_at' => now(),
        ]);

        // Act: User pulls field reports
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/sync/pull?entities[]=field_reports&last_sync=' . urlencode(now()->subDay()->toIso8601String()));

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data.field_reports');

        // Currently (before fix), user should see both reports
        // After fix, user should only see $myReport

        // I will assert what I expect AFTER the fix, and expect this test to fail initially
        $this->assertCount(1, $data, 'User should only see 1 report');
        $this->assertEquals($myReport->id, $data[0]['id'], 'User should see their own report');
    }

    public function test_field_reports_are_not_scoped_for_managers()
    {
        // Create users
        $user = User::factory()->create();
        $user->assignRole('agent');

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        // Create field reports
        $userReport = FieldReport::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'notes' => 'User report',
            'reporter_id' => $user->id,
            'updated_at' => now(),
        ]);

        $managerReport = FieldReport::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'notes' => 'Manager report',
            'reporter_id' => $manager->id,
            'updated_at' => now(),
        ]);

        // Act: Manager pulls field reports
        Sanctum::actingAs($manager);

        $response = $this->getJson('/api/sync/pull?entities[]=field_reports&last_sync=' . urlencode(now()->subDay()->toIso8601String()));

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data.field_reports');

        // Manager should see all reports
        $this->assertCount(2, $data);
    }
}
