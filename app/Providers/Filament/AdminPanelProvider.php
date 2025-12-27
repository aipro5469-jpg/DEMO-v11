<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationBuilder;
use App\Filament\Admin\Resources\ClientResource;
use App\Filament\Admin\Resources\AgentResource;
use App\Filament\Admin\Resources\BranchResource;
use App\Filament\Admin\Resources\EvaluationResource;
use App\Filament\Admin\Resources\EvaluationCriterionResource;
use App\Filament\Admin\Resources\EvaluationTemplateResource;
use App\Filament\Admin\Resources\CampaignResource;
use App\Filament\Admin\Resources\TaskResource;
use App\Filament\Admin\Resources\UserResource;
use BezhanSalleh\FilamentShield\Resources\RoleResource;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->brandName('الجبلي')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('4rem')
            ->colors([
                'primary' => Color::hex('#0F4C81'), // Jabali Blue
                'secondary' => Color::hex('#D4AF37'), // Jabali Gold
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'info' => Color::Cyan,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->maxContentWidth(\Filament\Support\Enums\MaxWidth::Full)
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->groups([

                        NavigationGroup::make()
                            ->label('إدارة المستخدمين')
                            ->icon('heroicon-o-user-group')
                            ->items([
                                ...UserResource::getNavigationItems(),
                                ...RoleResource::getNavigationItems(),
                            ]),

                        NavigationGroup::make()
                            ->label('إدارة العملاء')
                            ->icon('heroicon-o-user-group')
                            ->items([
                                ...ClientResource::getNavigationItems(),
                                ...AgentResource::getNavigationItems(),
                                ...BranchResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make()
                            ->label('التقييمات')
                            // ->icon('heroicon-o-user-group')
                            ->items([
                                ...EvaluationResource::getNavigationItems(),
                                ...EvaluationCriterionResource::getNavigationItems(),
                                ...EvaluationTemplateResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make()
                            ->label('إدارة الحملات والمهام')
                            ->icon('heroicon-o-rectangle-stack')
                            ->items([
                                ...CampaignResource::getNavigationItems(),
                                ...TaskResource::getNavigationItems(),

                            ]),
                        NavigationGroup::make()
                            ->label('إدارة المخزون')
                            ->icon('heroicon-o-archive-box')
                            ->items([
                                ...\App\Filament\Admin\Resources\CategoryResource::getNavigationItems(),
                                ...\App\Filament\Admin\Resources\ItemResource::getNavigationItems(),
                                ...\App\Filament\Admin\Resources\StockMovementResource::getNavigationItems(),
                                ...\App\Filament\Admin\Resources\InventoryAssignmentResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make()
                            ->label('النظام')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->items([
                                ...\App\Filament\Admin\Pages\Settings::getNavigationItems(),
                            ]),
                    ]);
            })
            ->font('Cairo')
            ->favicon(asset('images/logo.png'))
            ->sidebarCollapsibleOnDesktop()
            ->login()
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                \App\Filament\Admin\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ]);
    }
}
