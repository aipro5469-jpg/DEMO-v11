<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    use \BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('managePermissions')
                ->label('إدارة الأدوار والصلاحيات')
                ->icon('heroicon-o-shield-check')
                ->color('primary')
                ->record($this->getRecord())
                ->fillForm(function (\App\Models\User $record): array {
                    $data = [];

                    // Fill roles
                    $data['roles'] = $record->roles->pluck('id')->toArray();

                    // Fill permissions
                    // If user has super_admin role, they effectively have ALL permissions
                    if ($record->hasRole('super_admin')) {
                        $allPermissions = \Spatie\Permission\Models\Permission::pluck('name')->toArray();
                    } else {
                        $allPermissions = $record->getAllPermissions()->pluck('name')->toArray();
                    }

                    $resources = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getResources();
                    foreach ($resources as $resource) {
                        $resourcePermissions = \BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents::getResourcePermissionOptions($resource);
                        $resourceKey = $resource['resource'];
                        $safeKey = str_replace('::', '_', $resourceKey);

                        $data[$safeKey] = array_intersect(
                            $allPermissions,
                            array_keys($resourcePermissions)
                        );
                    }

                    $pages = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getPages();
                    foreach ($pages as $page) {
                        $permission = $page['permission'];
                        if (in_array($permission, $allPermissions)) {
                            $data['pages_tab'][] = $permission;
                        }
                    }

                    $widgets = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getWidgets();
                    foreach ($widgets as $widget) {
                        $permission = $widget['permission'];
                        if (in_array($permission, $allPermissions)) {
                            $data['widgets_tab'][] = $permission;
                        }
                    }

                    $customPermissions = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getCustomPermissions();
                    foreach ($customPermissions as $permission) {
                        if (in_array($permission, $allPermissions)) {
                            $data['custom_permissions'][] = $permission;
                        }
                    }

                    return $data;
                })
                ->form([
                    Forms\Components\Select::make('roles')
                        ->options(\Spatie\Permission\Models\Role::pluck('name', 'id'))
                        ->multiple()
                        ->searchable()
                        ->dehydrated(false)
                        ->label('الأدوار')
                        ->helperText('تعيين دور سيمنح جميع صلاحياته.')
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            // If no roles selected, clear all permissions
                            if (blank($state)) {
                                $resources = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getResources();
                                foreach ($resources as $resource) {
                                    $set($resource['resource'], []);
                                }
                                $set('pages_tab', []);
                                $set('widgets_tab', []);
                                $set('custom_permissions', []);
                                return;
                            }

                            $roles = \Spatie\Permission\Models\Role::with('permissions')->whereIn('id', $state)->get();

                            // Check if super_admin is selected
                            $hasSuperAdmin = $roles->contains('name', 'super_admin');

                            if ($hasSuperAdmin) {
                                $permissions = \Spatie\Permission\Models\Permission::pluck('name')->toArray();
                            } else {
                                $permissions = $roles->flatMap->permissions->pluck('name')->unique()->toArray();
                            }

                            // Debug notification removed

                            $resources = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getResources();
                            foreach ($resources as $resource) {
                                $resourcePermissions = \BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents::getResourcePermissionOptions($resource);
                                $resourceKey = $resource['resource'];
                                $safeKey = str_replace('::', '_', $resourceKey);

                                $matchingPermissions = array_intersect(
                                    $permissions,
                                    array_keys($resourcePermissions)
                                );

                                $set($safeKey, array_values($matchingPermissions));
                            }

                            // Update other tabs
                            $pages = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getPages();
                            $pagePermissions = [];
                            foreach ($pages as $page) {
                                if (in_array($page['permission'], $permissions)) {
                                    $pagePermissions[] = $page['permission'];
                                }
                            }
                            $set('pages_tab', $pagePermissions);

                            $widgets = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getWidgets();
                            $widgetPermissions = [];
                            foreach ($widgets as $widget) {
                                if (in_array($widget['permission'], $permissions)) {
                                    $widgetPermissions[] = $widget['permission'];
                                }
                            }
                            $set('widgets_tab', $widgetPermissions);

                            $customPermissions = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getCustomPermissions();
                            $customPermissionsList = [];
                            foreach ($customPermissions as $permission) {
                                if (in_array($permission, $permissions)) {
                                    $customPermissionsList[] = $permission;
                                }
                            }
                            $set('custom_permissions', $customPermissionsList);
                        }),

                    Forms\Components\Section::make('ملخص الصلاحيات الممنوحة')
                        ->schema([
                            Forms\Components\Placeholder::make('permissions_summary')
                                ->label('')
                                ->content(function (Forms\Get $get) {
                                    $groupedPermissions = [];

                                    // Resources
                                    $resources = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getResources();
                                    foreach ($resources as $resource) {
                                        $resourceKey = $resource['resource'];
                                        $safeKey = str_replace('::', '_', $resourceKey);
                                        $selected = $get($safeKey) ?? [];

                                        if (!empty($selected)) {
                                            $modelClass = $resource['model'];
                                            $label = $resource['model'];
                                            if (class_exists($modelClass) && method_exists($modelClass, 'getPluralModelLabel')) {
                                                $label = $modelClass::getPluralModelLabel();
                                            }

                                            // Extract verbs from permission names
                                            $verbs = collect($selected)->map(function ($perm) use ($resourceKey) {
                                                $parts = explode('_', $perm);
                                                return $parts[0] . (isset($parts[1]) && $parts[1] === 'any' ? '_any' : '');
                                            })->unique();

                                            $groupedPermissions[$label] = $verbs->toArray();
                                        }
                                    }

                                    // Pages
                                    $pages = $get('pages_tab') ?? [];
                                    if (!empty($pages)) {
                                        $groupedPermissions['الصفحات'] = $pages;
                                    }

                                    // Widgets
                                    $widgets = $get('widgets_tab') ?? [];
                                    if (!empty($widgets)) {
                                        $groupedPermissions['الويدجت'] = $widgets;
                                    }

                                    // Custom
                                    $custom = $get('custom_permissions') ?? [];
                                    if (!empty($custom)) {
                                        $groupedPermissions['صلاحيات أخرى'] = $custom;
                                    }

                                    if (empty($groupedPermissions)) {
                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="flex flex-col items-center justify-center p-6 text-gray-500 border border-dashed border-gray-300 rounded-xl dark:border-gray-700">
                                                <div class="mb-3 p-3 bg-gray-50 rounded-full dark:bg-gray-800">
                                                    <svg class="w-6 h-6 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                </div>
                                                <p class="text-sm font-medium">لا توجد صلاحيات محددة حتى الآن</p>
                                                <p class="text-xs text-gray-400 mt-1">قم باختيار دور أو تحديد صلاحيات لإظهار الملخص</p>
                                            </div>'
                                        );
                                    }

                                    $html = '<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">';
                                    foreach ($groupedPermissions as $group => $perms) {
                                        $badges = collect($perms)->map(function ($p) {
                                            $details = match ($p) {
                                                'view', 'view_any' => ['label' => 'عرض', 'bg' => 'bg-blue-50 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'ring' => 'ring-blue-700/10 dark:ring-blue-400/20'],
                                                'create' => ['label' => 'إضافة', 'bg' => 'bg-green-50 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400', 'ring' => 'ring-green-600/20 dark:ring-green-400/20'],
                                                'update' => ['label' => 'تعديل', 'bg' => 'bg-amber-50 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'ring' => 'ring-amber-600/20 dark:ring-amber-400/20'],
                                                'delete', 'delete_any' => ['label' => 'حذف', 'bg' => 'bg-red-50 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'ring' => 'ring-red-600/10 dark:ring-red-400/20'],
                                                'restore', 'restore_any' => ['label' => 'استرجاع', 'bg' => 'bg-cyan-50 dark:bg-cyan-900/30', 'text' => 'text-cyan-700 dark:text-cyan-400', 'ring' => 'ring-cyan-600/10 dark:ring-cyan-400/20'],
                                                'force_delete', 'force_delete_any' => ['label' => 'حذف نهائي', 'bg' => 'bg-red-100 dark:bg-red-900/50', 'text' => 'text-red-800 dark:text-red-300', 'ring' => 'ring-red-700/10 dark:ring-red-300/20'],
                                                default => ['label' => $p, 'bg' => 'bg-gray-50 dark:bg-gray-800', 'text' => 'text-gray-700 dark:text-gray-300', 'ring' => 'ring-gray-600/10 dark:ring-gray-400/20'],
                                            };

                                            return "<span class='inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {$details['bg']} {$details['text']} ring-1 ring-inset {$details['ring']}'>{$details['label']}</span>";
                                        })->implode(' ');

                                        $html .= "
                                            <div class='flex flex-col p-4 rounded-xl border border-gray-200 bg-white shadow-sm dark:bg-gray-900 dark:border-gray-800 transition-all hover:shadow-md'>
                                                <div class='flex items-center gap-2 mb-3 pb-2 border-b border-gray-100 dark:border-gray-800'>
                                                    <div class='p-1.5 rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400'>
                                                        <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'></path></svg>
                                                    </div>
                                                    <span class='text-sm font-bold text-gray-900 dark:text-white truncate'>{$group}</span>
                                                </div>
                                                <div class='flex flex-wrap gap-2'>{$badges}</div>
                                            </div>
                                        ";
                                    }
                                    $html .= '</div>';

                                    return new \Illuminate\Support\HtmlString($html);
                                }),
                        ])
                        ->collapsible()
                        ->collapsed(false)
                        ->icon('heroicon-o-presentation-chart-bar')
                        ->description('نظرة عامة على جميع الصلاحيات الممنوحة للمستخدم حالياً'),

                    Forms\Components\Tabs::make('Permissions')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('المصادر')
                                ->schema(function () {
                                    $resources = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getResources();
                                    $schema = [];
                                    foreach ($resources as $resource) {
                                        $resourcePermissions = \BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents::getResourcePermissionOptions($resource);
                                        $resourceKey = $resource['resource'];
                                        $safeKey = str_replace('::', '_', $resourceKey);
                                        $modelClass = $resource['model'];

                                        // Try to get Arabic label
                                        $label = $resource['model'];
                                        if (class_exists($modelClass) && method_exists($modelClass, 'getPluralModelLabel')) {
                                            $label = $modelClass::getPluralModelLabel();
                                        }

                                        $schema[] = Forms\Components\Section::make($label)
                                            ->description($resource['model'])
                                            ->schema([
                                                Forms\Components\CheckboxList::make($safeKey)
                                                    ->label('')
                                                    ->options($resourcePermissions)
                                                    ->searchable()
                                                    ->bulkToggleable()
                                                    ->columns(4)
                                                    ->gridDirection('row')
                                                    ->dehydrated(true) // Ensure it's always submitted
                                                    ->disabled(false) // Ensure it's always editable
                                                    ->live(),
                                            ])
                                            ->collapsible()
                                            ->collapsed(false);
                                    }
                                    return $schema;
                                }),

                            Forms\Components\Tabs\Tab::make('الصفحات')
                                ->schema([
                                    Forms\Components\CheckboxList::make('pages_tab')
                                        ->label('صلاحيات الصفحات')
                                        ->options(fn() => collect(\BezhanSalleh\FilamentShield\Facades\FilamentShield::getPages())
                                            ->mapWithKeys(fn($page) => [$page['permission'] => $page['permission']])
                                            ->toArray())
                                        ->searchable()
                                        ->bulkToggleable()
                                        ->columns(4)
                                        ->gridDirection('row')
                                        ->live(),
                                ]),

                            Forms\Components\Tabs\Tab::make('الويدجت')
                                ->schema([
                                    Forms\Components\CheckboxList::make('widgets_tab')
                                        ->label('صلاحيات الويدجت')
                                        ->options(fn() => collect(\BezhanSalleh\FilamentShield\Facades\FilamentShield::getWidgets())
                                            ->mapWithKeys(fn($widget) => [$widget['permission'] => $widget['permission']])
                                            ->toArray())
                                        ->searchable()
                                        ->bulkToggleable()
                                        ->columns(4)
                                        ->gridDirection('row')
                                        ->live(),
                                ]),

                            Forms\Components\Tabs\Tab::make('صلاحيات مخصصة')
                                ->schema([
                                    Forms\Components\CheckboxList::make('custom_permissions')
                                        ->label('صلاحيات أخرى')
                                        ->options(fn() => collect(\BezhanSalleh\FilamentShield\Facades\FilamentShield::getCustomPermissions())
                                            ->mapWithKeys(fn($permission) => [$permission => $permission]) // Custom permissions might be just strings or arrays, let's verify
                                            ->toArray())
                                        ->searchable()
                                        ->bulkToggleable()
                                        ->columns(4)
                                        ->gridDirection('row')
                                        ->live(),
                                ]),
                        ])
                        ->columnSpanFull(),
                ])
                ->action(function (\App\Models\User $record, array $data): void {
                    $selectedRoles = $data['roles'] ?? [];
                    $intendedPermissions = collect($data)
                        ->filter(fn($value, $key) => !in_array($key, ['roles']))
                        ->flatten()
                        ->unique()
                        ->toArray();

                    // If user selected roles, we check if they kept all permissions of those roles.
                    // If they did, we assign the role. If they removed even one, we detach the role and use direct permissions.

                    $rolesToAssign = [];

                    if (!empty($selectedRoles)) {
                        $roles = \Spatie\Permission\Models\Role::whereIn('id', $selectedRoles)->get();
                        foreach ($roles as $role) {
                            if ($role->name === 'super_admin') {
                                $rolesToAssign[] = $role->id;
                                continue;
                            }

                            $rolePermissions = $role->permissions->pluck('name')->toArray();
                            $missingPermissions = array_diff($rolePermissions, $intendedPermissions);

                            if (empty($missingPermissions)) {
                                $rolesToAssign[] = $role->id;
                            }
                        }
                    }

                    $record->syncRoles($rolesToAssign);

                    // Filter out any permissions that don't exist in the database to prevent errors
                    $validPermissions = \Spatie\Permission\Models\Permission::whereIn('name', $intendedPermissions)
                        ->pluck('name')
                        ->toArray();

                    $record->syncPermissions($validPermissions);

                    \Filament\Notifications\Notification::make()
                        ->title('تم تحديث الصلاحيات بنجاح')
                        ->success()
                        ->send();
                })
                ->modalWidth('7xl'),

            Actions\DeleteAction::make(),
        ];
    }
}
