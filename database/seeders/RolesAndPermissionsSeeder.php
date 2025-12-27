<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Clients
            'clients.view_all',
            'clients.view_assigned',
            'clients.create',
            'clients.update_all',
            'clients.update_assigned',
            'clients.delete',

            // Users
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.manage_roles',

            // Campaigns
            'campaigns.view',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',
            'campaigns.manage',

            // Reports
            'reports.view_all',
            'reports.view_own',
            'reports.export',

            // Settings
            'settings.view',
            'settings.update',

            // Tickets
            'tickets.view_all',
            'tickets.view_assigned',
            'tickets.create',
            'tickets.update',
            'tickets.assign',

            // Sales
            'sales.view',
            'sales.create',
            'sales.update',

            // Tasks
            'tasks.view_all',
            'tasks.view_assigned',
            'tasks.create',
            'tasks.update',
            'tasks.delete',

            // Branches
            'branches.view',
            'branches.create',
            'branches.update',
            'branches.delete',

            // Agents
            'agents.view',
            'agents.create',
            'agents.update',
            'agents.delete',

            // Ad Assets
            'ad_assets.view',
            'ad_assets.create',
            'ad_assets.update',
            'ad_assets.delete',

            // Evaluations
            'evaluations.view_all',
            'evaluations.view_own',
            'evaluations.create',
            'evaluations.update',
            'evaluations.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. General Manager - المدير العام
        $generalManager = Role::create(['name' => 'general_manager']);
        $generalManager->givePermissionTo(Permission::all());

        // 2. System Admin - مدير النظام
        $systemAdmin = Role::create(['name' => 'system_admin']);
        $systemAdmin->givePermissionTo([
            'users.view',
            'users.create',
            'users.update',
            'users.manage_roles',
            'clients.view_all',
            'clients.create',
            'clients.update_all',
            'clients.delete',
            'campaigns.view',
            'campaigns.create',
            'campaigns.update',
            'campaigns.delete',
            'reports.view_all',
            'reports.export',
            'settings.view',
            'settings.update',
            'tasks.view_all',
            'tasks.create',
            'tasks.update',
            'tasks.delete',
            'branches.view',
            'branches.create',
            'branches.update',
            'agents.view',
            'agents.create',
            'agents.update',
            'ad_assets.view',
            'ad_assets.create',
            'ad_assets.update',
            'evaluations.view_all',
            'evaluations.create',
            'evaluations.update',
        ]);

        // 3. PR Manager - مدير العلاقات العامة
        $prManager = Role::create(['name' => 'pr_manager']);
        $prManager->givePermissionTo([
            'clients.view_all',
            'clients.create',
            'clients.update_all',
            'tickets.view_all',
            'tickets.create',
            'tickets.update',
            'tickets.assign',
            'reports.view_all',
            'reports.export',
            'tasks.view_all',
            'tasks.create',
            'tasks.update',
            'evaluations.view_all',
            'evaluations.create',
        ]);

        // 4. PR Officer - موظف علاقات
        $prOfficer = Role::create(['name' => 'pr_officer']);
        $prOfficer->givePermissionTo([
            'clients.view_assigned',
            'clients.update_assigned',
            'tickets.view_assigned',
            'tickets.create',
            'reports.view_own',
            'tasks.view_assigned',
            'tasks.update',
            'evaluations.view_own',
            'evaluations.create',
        ]);

        // 5. Field Marketer - مسوق ميداني
        $fieldMarketer = Role::create(['name' => 'field_marketer']);
        $fieldMarketer->givePermissionTo([
            'clients.view_assigned',
            'clients.create',
            'clients.update_assigned',
            'tasks.view_assigned',
            'tasks.update',
            'reports.view_own',
            'ad_assets.view',
            'ad_assets.create',
            'ad_assets.update',
        ]);

        // 6. Sales Officer - موظف مبيعات
        $salesOfficer = Role::create(['name' => 'sales_officer']);
        $salesOfficer->givePermissionTo([
            'clients.view_assigned',
            'clients.update_assigned',
            'sales.view',
            'sales.create',
            'sales.update',
            'reports.view_own',
            'tasks.view_assigned',
        ]);

        // 7. Customer Service - خدمة عملاء
        $customerService = Role::create(['name' => 'customer_service']);
        $customerService->givePermissionTo([
            'clients.view_all',
            'tickets.view_all',
            'tickets.create',
            'tickets.update',
            'tickets.assign',
            'reports.view_own',
        ]);

        // 8. Digital Marketer - مسوق إلكتروني
        $digitalMarketer = Role::create(['name' => 'digital_marketer']);
        $digitalMarketer->givePermissionTo([
            'campaigns.view',
            'campaigns.create',
            'campaigns.update',
            'clients.view_assigned',
            'clients.create',
            'reports.view_own',
            'reports.export',
            'ad_assets.view',
            'ad_assets.create',
            'ad_assets.update',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
