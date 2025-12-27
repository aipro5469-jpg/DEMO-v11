<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InventoryAssignmentResource\Pages;
use App\Models\InventoryAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryAssignmentResource extends Resource
{
    protected static ?string $model = InventoryAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'عهد المسوقين';

    protected static ?string $modelLabel = 'عهدة مسوق';

    protected static ?string $pluralModelLabel = 'عهد المسوقين';

    protected static ?string $navigationGroup = 'إدارة المخزون';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('marketer_id')
                    ->label('المسوق')
                    ->relationship('marketer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('item_id')
                    ->label('الصنف')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('available_stock', \App\Models\Item::find($state)?->total_stock ?? 0)),
                Forms\Components\TextInput::make('available_stock')
                    ->label('المخزون المتوفر')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn(Forms\Get $get) => $get('item_id') !== null),
                Forms\Components\TextInput::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(fn(Forms\Get $get) => $get('available_stock') ?? 999999)
                    ->validationMessages([
                        'max' => 'الكمية المطلوبة أكبر من المخزون المتوفر.',
                    ]),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'returned' => 'مسترجع',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('marketer.name')
                    ->label('المسوق')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('item.name')
                    ->label('الصنف')
                    ->searchable()
                    ->sortable()
                    ->description(fn(InventoryAssignment $record): string => $record->item->code ?? '-'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية الحالية')
                    ->numeric()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        'active' => 'heroicon-m-check-circle',
                        'returned' => 'heroicon-m-arrow-uturn-left',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'returned' => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'نشط',
                        'returned' => 'مسترجع',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->icon('heroicon-m-clock')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('marketer')
                    ->relationship('marketer', 'name')
                    ->label('المسوق'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'نشط',
                        'returned' => 'مسترجع',
                    ])
                    ->label('الحالة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryAssignments::route('/'),
            'create' => Pages\CreateInventoryAssignment::route('/create'),
            'edit' => Pages\EditInventoryAssignment::route('/{record}/edit'),
        ];
    }
}
