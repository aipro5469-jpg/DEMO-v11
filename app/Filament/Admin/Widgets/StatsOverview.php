<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي العملاء', Client::count())
                ->description('العملاء المسجلين في النظام')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            
            Stat::make('المهام النشطة', Task::where('status', '!=', 'completed')->count())
                ->description('مهام قيد التنفيذ')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            Stat::make('المستخدمين', User::count())
                ->description('إجمالي المستخدمين')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
