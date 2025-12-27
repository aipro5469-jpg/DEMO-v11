<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GiftItemResource\Pages;
use App\Filament\Admin\Resources\GiftItemResource\RelationManagers;
use App\Models\GiftItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GiftItemResource extends Resource
{
    protected static ?string $model = GiftItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    
    protected static ?string $navigationLabel = 'الهدايا';
    
    protected static ?string $modelLabel = 'هدية';
    
    protected static ?string $pluralModelLabel = 'الهدايا';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الهدية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('category')
                            ->label('الفئة')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('unit')
                            ->label('الوحدة')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('المخزون والتكلفة')
                    ->schema([
                        Forms\Components\TextInput::make('total_stock')
                            ->label('المخزون الكلي')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('cost_per_unit')
                            ->label('التكلفة للوحدة')
                            ->numeric()
                            ->prefix('$'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('الرمز')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('الفئة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_stock')
                    ->label('المخزون')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_per_unit')
                    ->label('التكلفة')
                    ->money('USD')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('الفئة')
                    ->options(fn () => GiftItem::query()->pluck('category', 'category')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListGiftItems::route('/'),
            'create' => Pages\CreateGiftItem::route('/create'),
            'view' => Pages\ViewGiftItem::route('/{record}'),
            'edit' => Pages\EditGiftItem::route('/{record}/edit'),
        ];
    }
}
