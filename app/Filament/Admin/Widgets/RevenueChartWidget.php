<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'حركة المخزون (آخر 30 يوم)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2, // Reduced from 3 to 2
    ];
    protected static ?string $maxHeight = '300px'; // Limit height

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'صادر (بيع)',
                    'data' => [65, 59, 80, 81, 56, 55, 40, 50, 60, 70, 80, 90],
                    'borderColor' => '#D4AF37', // Jabali Gold
                    'backgroundColor' => 'rgba(212, 175, 55, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'وارد (شراء/مرتجع)',
                    'data' => [28, 48, 40, 19, 86, 27, 90, 80, 70, 60, 50, 40],
                    'borderColor' => '#0F4C81', // Jabali Blue
                    'backgroundColor' => 'rgba(15, 76, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
