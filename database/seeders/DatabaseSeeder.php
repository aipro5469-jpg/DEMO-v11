<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Roles and Permissions first
            RoleSeeder::class,

            // Users (depends on roles)
            UserSeeder::class,

            // Clients (depends on users for created_by)
            ClientSeeder::class,

            // Campaigns (depends on users)
            CampaignSeeder::class,

            // Tasks (depends on campaigns, clients, users)
            TaskSeeder::class,
        ]);
    }
}
