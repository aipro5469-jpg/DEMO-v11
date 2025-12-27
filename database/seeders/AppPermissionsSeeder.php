<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'app_view_map' => 'عرض الخريطة التفاعلية',
            'app_view_reports' => 'عرض شاشة التقارير',
            'app_delete_client' => 'حذف العملاء (تطبيق)',
            'app_edit_client_unlimited' => 'تعديل العملاء بلا قيود (تطبيق)',
        ];

        foreach ($permissions as $name => $label) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Assign all to super_admin
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(array_keys($permissions));

        // Assign basic view permissions to marketer for testing
        $marketer = Role::firstOrCreate(['name' => 'marketer', 'guard_name' => 'web']);
        $marketer->givePermissionTo(['app_view_map', 'app_view_reports']);
    }
}
