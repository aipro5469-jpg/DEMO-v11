<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StockMovementResource\Pages;
use App\Filament\Admin\Resources\StockMovementResource\RelationManagers;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'إدارة المخزون';

    protected static ?string $navigationLabel = 'حركات المخزون';

    protected static ?string $modelLabel = 'حركة مخزون';

    protected static ?string $pluralModelLabel = 'حركات المخزون';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id')
                    ->label('الصنف')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('نوع الحركة')
                    ->options([
                        'in' => 'إضافة للمخزون (شراء/توريد)',
                        'out' => 'صرف من المخزون (تالف/استخدام)',
                        'adjustment' => 'تسويه جردية',
                        'return' => 'مرتجع',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('الصنف')
                    ->searchable()
                    ->sortable()
                    ->description(fn(StockMovement $record): string => $record->item->code ?? '-')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الحركة')
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        'in' => 'heroicon-m-arrow-down-tray',
                        'out' => 'heroicon-m-arrow-up-tray',
                        'adjustment' => 'heroicon-m-adjustments-horizontal',
                        'return' => 'heroicon-m-arrow-uturn-left',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'warning',
                        'return' => 'info',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'إضافة (شراء)',
                        'out' => 'صرف (بيع)',
                        'adjustment' => 'تسويه',
                        'return' => 'مرتجع',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->sortable()
                    ->weight('bold')
                    ->color(fn(StockMovement $record): string => match ($record->type) {
                        'in', 'return' => 'success',
                        'out' => 'danger',
                        default => 'warning',
                    })
                    ->prefix(fn(StockMovement $record): string => match ($record->type) {
                        'in', 'return' => '+',
                        'out' => '-',
                        default => '',
                    }),
                Tables\Columns\TextColumn::make('source_type')
                    ->label('المصدر / الوجهة')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'out' && $record->destination) {
                            return 'إلى: ' . ($record->destination->name ?? class_basename($record->destination_type));
                        } elseif ($record->type === 'in' && $record->source) {
                            return 'من: ' . ($record->source->name ?? class_basename($record->source_type));
                        }
                        return '-';
                    })
                    ->icon('heroicon-m-arrows-right-left')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('بواسطة')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('item')
                    ->relationship('item', 'name')
                    ->label('الصنف'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'in' => 'إضافة',
                        'out' => 'صرف',
                        'adjustment' => 'تسويه',
                        'return' => 'مرتجع',
                    ])
                    ->label('النوع'),
            ])
            ->actions([
                // View only, usually movements are immutable log
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk delete for audit trail usually
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'view' => Pages\ViewStockMovement::route('/{record}'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
        ];
    }

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make()
                    ->schema([
                        \Filament\Infolists\Components\Split::make([
                            \Filament\Infolists\Components\Grid::make(2)
                                ->schema([
                                    \Filament\Infolists\Components\Group::make([
                                        \Filament\Infolists\Components\TextEntry::make('item.name')
                                            ->label('الصنف')
                                            ->weight('bold')
                                            ->size(\Filament\Infolists\Components\TextEntry\TextEntrySize::Large)
                                            ->icon('heroicon-m-cube'),
                                        \Filament\Infolists\Components\TextEntry::make('item.code')
                                            ->label('كود الصنف')
                                            ->copyable(),
                                    ]),
                                    \Filament\Infolists\Components\Group::make([
                                        \Filament\Infolists\Components\TextEntry::make('type')
                                            ->label('نوع الحركة')
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'in' => 'success',
                                                'out' => 'danger',
                                                'adjustment' => 'warning',
                                                'return' => 'info',
                                            })
                                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                                'in' => 'إضافة (شراء)',
                                                'out' => 'صرف (بيع)',
                                                'adjustment' => 'تسويه',
                                                'return' => 'مرتجع',
                                                default => $state,
                                            })
                                            ->size(\Filament\Infolists\Components\TextEntry\TextEntrySize::Large),
                                        \Filament\Infolists\Components\TextEntry::make('quantity')
                                            ->label('الكمية')
                                            ->weight('bold')
                                            ->size(\Filament\Infolists\Components\TextEntry\TextEntrySize::Large)
                                            ->color(fn($record) => match ($record->type) {
                                                'in', 'return' => 'success',
                                                'out' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn($state, $record) => match ($record->type) {
                                                'in', 'return' => '+' . $state,
                                                'out' => '-' . $state,
                                                default => $state,
                                            }),
                                    ]),
                                ]),
                        ])->from('md'),
                    ]),

                \Filament\Infolists\Components\Section::make('تفاصيل العملية')
                    ->schema([
                        \Filament\Infolists\Components\Grid::make(3)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('source_type')
                                    ->label('المصدر')
                                    ->icon('heroicon-m-arrow-right-circle')
                                    ->color('gray')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'supplier' => 'مورد',
                                        'warehouse' => 'المخزن الرئيسي',
                                        'client' => 'عميل',
                                        default => $state,
                                    }),
                                \Filament\Infolists\Components\IconEntry::make('direction')
                                    ->label('')
                                    ->icon('heroicon-m-arrow-long-left')
                                    ->size(\Filament\Infolists\Components\IconEntry\IconEntrySize::Large)
                                    ->color('primary'),
                                \Filament\Infolists\Components\TextEntry::make('destination_type')
                                    ->label('الوجهة')
                                    ->icon('heroicon-m-arrow-left-circle')
                                    ->color('gray')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'warehouse' => 'المخزن الرئيسي',
                                        'client' => 'عميل',
                                        'marketer' => 'مندوب',
                                        default => $state,
                                    }),
                            ]),

                        \Filament\Infolists\Components\Grid::make(2)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('creator.name')
                                    ->label('قام بالعملية')
                                    ->icon('heroicon-m-user'),
                                \Filament\Infolists\Components\TextEntry::make('created_at')
                                    ->label('التوقيت')
                                    ->dateTime('d/m/Y h:i A')
                                    ->icon('heroicon-m-calendar'),
                            ]),

                        \Filament\Infolists\Components\TextEntry::make('description')
                            ->label('ملاحظات')
                            ->columnSpanFull()
                            ->placeholder('لا توجد ملاحظات'),
                    ]),
            ]);
    }
}
