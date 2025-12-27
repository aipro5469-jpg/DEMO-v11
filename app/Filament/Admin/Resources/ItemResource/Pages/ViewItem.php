<?php

namespace App\Filament\Admin\Resources\ItemResource\Pages;

use App\Filament\Admin\Resources\ItemResource;
use Filament\Resources\Pages\ViewRecord;

class ViewItem extends ViewRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ItemResource\Widgets\ItemStockChartWidget::class,
        ];
    }
}
