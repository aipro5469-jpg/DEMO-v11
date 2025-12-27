<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TopMarketersWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected function getStats(): array
    {
        // Placeholder logic - ideally we query the DB for top performers
        return [
            Stat::make('أفضل مسوق', 'محمد المسوق')
                ->description('150 عملية هذا الشهر')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'), // Gold

            Stat::make('أكثر عميل نشاطاً', 'العميل الفلاني')
                ->description('25 طلبية')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),
        ];
    }
}
