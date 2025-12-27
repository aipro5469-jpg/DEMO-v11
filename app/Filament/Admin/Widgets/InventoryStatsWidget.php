<?php

namespace App\Filament\Admin\Widgets;

use App\Models\InventoryAssignment;
use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalItems = Item::count();
        $totalStock = Item::sum('total_stock');
        $activeAssignments = InventoryAssignment::where('status', 'active')->count();
        $lowStockItems = Item::where('total_stock', '<', 10)->count();

        return [
            Stat::make('إجمالي الأصناف', $totalItems)
                ->description('عدد الأصناف المسجلة')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('primary'),

            Stat::make('إجمالي المخزون', number_format($totalStock))
                ->description('مجموع الكميات المتوفرة')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('success'),

            Stat::make('العهد النشطة', $activeAssignments)
                ->description('لدى المسوقين حالياً')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),

            Stat::make('تنبيهات المخزون', $lowStockItems)
                ->description('أصناف أوشكت على النفاذ')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockItems > 0 ? 'danger' : 'success'),
        ];
    }
}
