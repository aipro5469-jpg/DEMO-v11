<?php

namespace App\Filament\Admin\Widgets;

use App\Models\StockMovement;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StockTrendsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'حركة المخزون (آخر 30 يوم)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Get data for last 30 days
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $movements = StockMovement::query()
            ->select(
                DB::raw('DATE(created_at) as date'),
                'type',
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'type')
            ->get();

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $labels = [];
        $inData = [];
        $outData = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $labels[] = $date->format('d M');

            // Get 'in' movements for this day
            $in = $movements->where('date', $dateString)->whereIn('type', ['in', 'return'])->sum('total_quantity');
            $inData[] = $in;

            // Get 'out' movements for this day
            $out = $movements->where('date', $dateString)->where('type', 'out')->sum('total_quantity');
            $outData[] = $out;
        }

        return [
            'datasets' => [
                [
                    'label' => 'وارد (شراء/مرتجع)',
                    'data' => $inData,
                    'backgroundColor' => '#10b981', // Emerald 500
                    'borderColor' => '#059669',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'صادر (بيع)',
                    'data' => $outData,
                    'backgroundColor' => '#ef4444', // Red 500
                    'borderColor' => '#dc2626',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
