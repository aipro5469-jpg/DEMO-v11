<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'الرئيسية';
    protected static ?string $title = 'لوحة التحكم';

    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\StatsOverviewWidget::class,
            \App\Filament\Admin\Widgets\RevenueChartWidget::class,
            \App\Filament\Admin\Widgets\TopMarketersWidget::class,
            \App\Filament\Admin\Widgets\RecentActivityWidget::class,
        ];
    }
}
