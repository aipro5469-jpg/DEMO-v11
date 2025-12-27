<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Client;
use App\Models\Item;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.admin.widgets.stats-overview-widget';

    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي العملاء', Client::count())
                ->description('العملاء المسجلين في النظام')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('إجمالي الأصناف', Item::count())
                ->description('عدد الأصناف المسجلة')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('المهام النشطة', Task::where('status', '!=', 'completed')->count())
                ->description('مهام قيد التنفيذ')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('قيمة المخزون', '8,100') // Placeholder for now, needs logic
                ->description('تقديري')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
