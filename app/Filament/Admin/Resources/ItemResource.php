<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ItemResource\Pages;
use App\Filament\Admin\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'إدارة المخزون';

    protected static ?string $navigationLabel = 'الأصناف';

    protected static ?string $modelLabel = 'صنف';

    protected static ?string $pluralModelLabel = 'الأصناف';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('اسم الصنف'),
                Forms\Components\TextInput::make('code')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('الرمز'),
                Forms\Components\Select::make('category_id')
                    ->label('الفئة')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('item_type')
                    ->label('نوع الصنف')
                    ->options([
                        'gift' => 'هدية',
                        'sale' => 'بيع',
                    ])
                    ->required()
                    ->default('gift'),
                Forms\Components\TextInput::make('unit')
                    ->label('الوحدة')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('الرمز')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('الفئة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('item_type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'gift' => 'info',
                        'sale' => 'success',
                    }),
                Tables\Columns\TextColumn::make('total_stock')
                    ->label('المخزون الكلي')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('الفئة'),
                Tables\Filters\SelectFilter::make('item_type')
                    ->options([
                        'gift' => 'هدية',
                        'sale' => 'بيع',
                    ])
                    ->label('النوع'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'view' => Pages\ViewItem::route('/{record}'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
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
                                        \Filament\Infolists\Components\TextEntry::make('name')
                                            ->label('اسم الصنف')
                                            ->weight('bold')
                                            ->size(\Filament\Infolists\Components\TextEntry\TextEntrySize::Large),
                                        \Filament\Infolists\Components\TextEntry::make('code')
                                            ->label('الرمز')
                                            ->copyable()
                                            ->icon('heroicon-m-qr-code'),
                                    ]),
                                    \Filament\Infolists\Components\Group::make([
                                        \Filament\Infolists\Components\TextEntry::make('status')
                                            ->label('الحالة')
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'active' => 'success',
                                                'inactive' => 'gray',
                                            }),
                                        \Filament\Infolists\Components\TextEntry::make('item_type')
                                            ->label('النوع')
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'gift' => 'info',
                                                'sale' => 'success',
                                            }),
                                    ]),
                                ]),
                        ])->from('md'),
                    ]),

                \Filament\Infolists\Components\Section::make('إحصائيات')
                    ->schema([
                        \Filament\Infolists\Components\Grid::make(4)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('total_stock')
                                    ->label('المخزون الحالي')
                                    ->size(\Filament\Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->color('primary'),
                                \Filament\Infolists\Components\TextEntry::make('assignments_count')
                                    ->label('العهد النشطة')
                                    ->state(fn($record) => $record->assignments()->where('status', 'active')->count())
                                    ->badge()
                                    ->color('warning'),
                                \Filament\Infolists\Components\TextEntry::make('category.name')
                                    ->label('الفئة'),
                                \Filament\Infolists\Components\TextEntry::make('created_at')
                                    ->label('تاريخ الإضافة')
                                    ->dateTime('d/m/Y'),
                            ]),
                    ]),

                \Filament\Infolists\Components\Tabs::make('Details')
                    ->tabs([
                        \Filament\Infolists\Components\Tabs\Tab::make('سجل الحركات')
                            ->icon('heroicon-m-clock')
                            ->schema([
                                \Filament\Infolists\Components\RepeatableEntry::make('stockMovements')
                                    ->label('')
                                    ->schema([
                                        \Filament\Infolists\Components\Grid::make(4)
                                            ->schema([
                                                \Filament\Infolists\Components\TextEntry::make('created_at')
                                                    ->label('التاريخ')
                                                    ->dateTime('d/m/Y h:i A'),
                                                \Filament\Infolists\Components\TextEntry::make('type')
                                                    ->label('النوع')
                                                    ->badge()
                                                    ->color(fn(string $state): string => match ($state) {
                                                        'in' => 'success',
                                                        'out' => 'danger',
                                                        'adjustment' => 'warning',
                                                        'return' => 'info',
                                                    })
                                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                                        'in' => 'إضافة',
                                                        'out' => 'صرف',
                                                        'adjustment' => 'تسويه',
                                                        'return' => 'مرتجع',
                                                        default => $state,
                                                    }),
                                                \Filament\Infolists\Components\TextEntry::make('quantity')
                                                    ->label('الكمية')
                                                    ->weight('bold')
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
                                                \Filament\Infolists\Components\TextEntry::make('creator.name')
                                                    ->label('بواسطة')
                                                    ->icon('heroicon-m-user'),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->contained(false),
                            ]),
                        \Filament\Infolists\Components\Tabs\Tab::make('العهد الحالية')
                            ->icon('heroicon-m-user-group')
                            ->schema([
                                \Filament\Infolists\Components\RepeatableEntry::make('assignments')
                                    ->label('')
                                    ->schema([
                                        \Filament\Infolists\Components\Grid::make(3)
                                            ->schema([
                                                \Filament\Infolists\Components\TextEntry::make('marketer.name')
                                                    ->label('المندوب')
                                                    ->icon('heroicon-m-user'),
                                                \Filament\Infolists\Components\TextEntry::make('quantity')
                                                    ->label('الكمية')
                                                    ->weight('bold'),
                                                \Filament\Infolists\Components\TextEntry::make('status')
                                                    ->label('الحالة')
                                                    ->badge()
                                                    ->color(fn(string $state): string => match ($state) {
                                                        'active' => 'success',
                                                        'returned' => 'gray',
                                                    }),
                                            ]),
                                    ])
                                    ->columns(1)
                                    ->contained(false),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
