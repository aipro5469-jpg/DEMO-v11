<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Client permissions
            'view_client',
            'create_client',
            'update_client',
            'delete_client',

            // Task permissions
            'view_task',
            'create_task',
            'update_task',
            'delete_task',
            'assign_task', // Custom? Keep or check if Shield generates it. Shield generates standard CRUD. assign_task might be custom.

            // Campaign permissions
            'view_campaign',
            'create_campaign',
            'update_campaign',
            'delete_campaign',

            // Field Report permissions
            'view_field_report',
            'create_field_report',
            'update_field_report',
            'delete_field_report',

            // Evaluation permissions
            'view_evaluation',
            'create_evaluation',
            'update_evaluation',
            'delete_evaluation',

            // User management
            'view_user',
            'create_user',
            'update_user',
            'delete_user',

            // Reports (Assuming this is custom or FieldReport? If custom, keep as is or singularize if it maps to a model)
            'view_report',
            'export_report',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Manager - can manage everything except users
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'view_client',
            'create_client',
            'update_client',
            'delete_client',
            'view_task',
            'create_task',
            'update_task',
            'delete_task',
            // 'assign_task',
            'view_campaign',
            'create_campaign',
            'update_campaign',
            'delete_campaign',
            'view_field_report',
            'create_field_report',
            'update_field_report',
            'delete_field_report',
            'view_evaluation',
            'create_evaluation',
            'update_evaluation',
            'delete_evaluation',
            'view_report',
            'export_report',
        ]);

        // Field Agent - can view and create clients, tasks, reports
        $fieldAgent = Role::firstOrCreate(['name' => 'field_agent']);
        $fieldAgent->givePermissionTo([
            'view_client',
            'create_client',
            'update_client',
            'view_task',
            'update_task',
            'view_campaign',
            'view_field_report',
            'create_field_report',
            'update_field_report',
            'view_evaluation',
            'create_evaluation',
        ]);

        // Viewer - read-only access
        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->givePermissionTo([
            'view_client',
            'view_task',
            'view_campaign',
            'view_field_report',
            'view_evaluation',
            'view_report',
        ]);
    }
}
