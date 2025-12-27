<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'المدير العام',
            'username' => 'admin',
            'email' => 'admin@algabli.com',
            'phone' => [['number' => '0500000000']],
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // Assign general_manager role
        $admin->assignRole('general_manager');

        $this->command->info('Default admin user created successfully!');
        $this->command->info('Username: admin');
        $this->command->info('Password: password');
        $this->command->warn('⚠️  Please change the password in production!');

        // Create additional test users for each role

        // System Admin
        $systemAdmin = User::create([
            'name' => 'مدير النظام',
            'username' => 'sysadmin',
            'email' => 'sysadmin@algabli.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $systemAdmin->assignRole('system_admin');

        // PR Manager
        $prManager = User::create([
            'name' => 'مدير العلاقات العامة',
            'username' => 'prmanager',
            'email' => 'prmanager@algabli.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $prManager->assignRole('pr_manager');

        // PR Officer
        $prOfficer = User::create([
            'name' => 'موظف علاقات',
            'username' => 'profficer',
            'email' => 'profficer@algabli.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $prOfficer->assignRole('pr_officer');

        // Field Marketer
        $fieldMarketer = User::create([
            'name' => 'مسوق ميداني',
            'username' => 'fieldmarketer',
            'email' => 'fieldmarketer@algabli.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $fieldMarketer->assignRole('field_marketer');

        // Sales Officer
        $salesOfficer = User::create([
            'name' => 'موظف مبيعات',
            'username' => 'sales',
            'email' => 'sales@algabli.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $salesOfficer->assignRole('sales_officer');

        // Customer Service
        $customerService = User::create([
            'name' => 'خدمة عملاء',
            'username' => 'support',
            'email' => 'support@algabli.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $customerService->assignRole('customer_service');

        // Digital Marketer
        $digitalMarketer = User::create([
            'name' => 'مسوق إلكتروني',
            'username' => 'digital',
            'email' => 'digital@algabli.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $digitalMarketer->assignRole('digital_marketer');

        $this->command->info('Created 8 test users (one for each role)');
        $this->command->info('All passwords are: password');
    }
}
