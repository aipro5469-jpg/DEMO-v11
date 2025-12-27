<?php

namespace App\Filament\Resources\Shield;

use BezhanSalleh\FilamentShield\Resources\RoleResource as BaseRoleResource;
use Filament\Forms;
use Filament\Forms\Form;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Forms\ShieldSelectAllToggle;
use Illuminate\Validation\Rules\Unique;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;

class RoleResource extends BaseRoleResource
{
    protected static ?string $navigationGroup = 'إدارة المستخدمين';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Role Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('البيانات الأساسية')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('filament-shield::filament-shield.field.name'))
                                            ->unique(
                                                ignoreRecord: true,
                                                modifyRuleUsing: fn(Unique $rule) => Utils::isTenancyEnabled() ? $rule->where(Utils::getTenantModelForeignKey(), Filament::getTenant()?->id) : $rule
                                            )
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('guard_name')
                                            ->label(__('filament-shield::filament-shield.field.guard_name'))
                                            ->default(Utils::getFilamentAuthGuard())
                                            ->nullable()
                                            ->maxLength(255),

                                        ShieldSelectAllToggle::make('select_all')
                                            ->onIcon('heroicon-s-shield-check')
                                            ->offIcon('heroicon-s-shield-exclamation')
                                            ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                            ->helperText(fn(): HtmlString => new HtmlString(__('filament-shield::filament-shield.field.select_all.message')))
                                            ->dehydrated(fn(bool $state): bool => $state),
                                    ])
                                    ->columns([
                                        'sm' => 2,
                                        'lg' => 3,
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('صلاحيات التطبيق')
                            ->icon('heroicon-o-device-phone-mobile')
                            ->schema([
                                Forms\Components\CheckboxList::make('custom_permissions')
                                    ->label('خصائص التطبيق')
                                    ->helperText('حدد الخصائص التي يمكن لهذا الدور الوصول إليها في تطبيق الهاتف')
                                    ->options(function () {
                                        return [
                                            'app_view_map' => 'عرض الخريطة التفاعلية',
                                            'app_view_reports' => 'عرض شاشة التقارير',
                                            'app_delete_client' => 'حذف العملاء (تطبيق)',
                                            'app_edit_client_unlimited' => 'تعديل العملاء بلا قيود (تطبيق)',
                                        ];
                                    })
                                    ->bulkToggleable()
                                    ->columns(2)
                                    ->gridDirection('row'),
                            ]),

                        Forms\Components\Tabs\Tab::make('صلاحيات لوحة التحكم')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                static::getShieldFormComponents(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
