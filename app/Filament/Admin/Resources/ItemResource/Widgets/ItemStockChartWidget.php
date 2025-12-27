<?php

namespace App\Filament\Admin\Resources\ItemResource\Widgets;

use Filament\Widgets\ChartWidget;

class ItemStockChartWidget extends ChartWidget
{
    protected static ?string $heading = 'تحركات المخزون (آخر 30 يوم)';

    public ?\Illuminate\Database\Eloquent\Model $record = null;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Get current stock
        $currentStock = $this->record->total_stock;

        // Get movements for the last 30 days, ordered by date descending (newest first)
        $movements = $this->record->stockMovements()
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get();

        // Create a period of last 30 days
        $period = \Carbon\CarbonPeriod::create(now()->subDays(29), now());

        // We need to calculate stock at the END of each day.
        // Working backwards:
        // Stock at end of today = currentStock (assuming no future movements)
        // Stock at end of yesterday = Stock at end of today - (movements today)

        $dailyStock = [];
        $tempStock = $currentStock;

        // Group movements by date (Y-m-d)
        $movementsByDate = $movements->groupBy(function ($date) {
            return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
        });

        // Iterate backwards from today to 30 days ago
        foreach ($period->toArray() as $date) {
            $dateString = $date->format('Y-m-d');
            // Store the stock at the END of this day
            // But since we are iterating forward in this loop (CarbonPeriod is forward), 
            // we actually need to calculate backwards or pre-calculate.
        }

        // Let's try a different approach:
        // 1. Calculate stock 30 days ago.
        //    Stock_start = Current - Sum(All movements in last 30 days)
        //    Wait, "Sum" depends on type (in/out).

        $netChange = 0;
        foreach ($movements as $movement) {
            if (in_array($movement->type, ['in', 'return'])) {
                $netChange += $movement->quantity;
            } elseif ($movement->type === 'out') {
                $netChange -= $movement->quantity;
            } elseif ($movement->type === 'adjustment') {
                $netChange += $movement->quantity;
            }
        }

        // Stock at start of 30 days ago
        $runningStock = $currentStock - $netChange;

        // Now iterate forward
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dayMovements = $movementsByDate->get($dateString) ?? collect();

            // Apply this day's movements to get end-of-day stock
            foreach ($dayMovements->reverse() as $movement) { // Reverse to process in chronological order if multiple per day
                if (in_array($movement->type, ['in', 'return'])) {
                    $runningStock += $movement->quantity;
                } elseif ($movement->type === 'out') {
                    $runningStock -= $movement->quantity;
                } elseif ($movement->type === 'adjustment') {
                    $runningStock += $movement->quantity;
                }
            }

            $labels[] = $date->format('d M');
            $data[] = $runningStock;
        }

        return [
            'datasets' => [
                [
                    'label' => 'المخزون',
                    'data' => $data,
                    'borderColor' => '#4338ca', // Royal Blue (Indigo 700)
                    'backgroundColor' => 'rgba(67, 56, 202, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
