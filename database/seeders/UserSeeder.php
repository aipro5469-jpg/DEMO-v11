<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@algabli.com',
            'username' => 'admin',
            'phone' => [['number' => '777000000']],
            'password' => Hash::make('123'),
            'is_active' => true,
        ]);
        $admin->assignRole('super_admin');

        // Create Manager
        $manager = User::create([
            'name' => 'أحمد المدير',
            'email' => 'manager@algabli.com',
            'username' => 'manager',
            'phone' => [['number' => '777111111']],
            'password' => Hash::make('123'),
            'is_active' => true,
        ]);
        $manager->assignRole('manager');

        // Create Field Agents
        $agents = [
            [
                'name' => 'محمد المسوق',
                'email' => 'mohamed@algabli.com',
                'username' => 'mohamed',
                'phone' => [['number' => '777222222']],
            ],
            [
                'name' => 'علي الميداني',
                'email' => 'ali@algabli.com',
                'username' => 'ali',
                'phone' => [['number' => '777333333']],
            ],
            [
                'name' => 'خالد المندوب',
                'email' => 'khaled@algabli.com',
                'username' => 'khaled',
                'phone' => [['number' => '777444444']],
            ],
        ];

        foreach ($agents as $agentData) {
            $agent = User::create([
                'name' => $agentData['name'],
                'email' => $agentData['email'],
                'username' => $agentData['username'],
                'phone' => $agentData['phone'],
                'password' => Hash::make('123'),
                'is_active' => true,
            ]);
            $agent->assignRole('field_agent');
        }

        // Create Viewer
        $viewer = User::create([
            'name' => 'مراقب النظام',
            'email' => 'viewer@algabli.com',
            'username' => 'viewer',
            'phone' => [['number' => '777555555']],
            'password' => Hash::make('123'),
            'is_active' => true,
        ]);
        $viewer->assignRole('viewer');
    }
}
