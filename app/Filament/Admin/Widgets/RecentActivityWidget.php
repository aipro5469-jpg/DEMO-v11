<?php

namespace App\Filament\Admin\Widgets;

use App\Models\StockMovement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'أحدث العمليات';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StockMovement::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('الصنف')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية')
                    ->badge()
                    ->color(fn(string $state, StockMovement $record): string => match ($record->type) {
                        'in' => 'success',
                        'out' => 'danger',
                        'return' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'توريد',
                        'out' => 'صرف',
                        'return' => 'مرتجع',
                        'adjustment' => 'تسويه',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التوقيت')
                    ->since(),
            ])
            ->paginated(false);
    }
}
