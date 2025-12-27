<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'الفروع';
    protected static ?string $modelLabel = 'فرع';
    protected static ?string $pluralModelLabel = 'الفروع';
    protected static ?string $navigationGroup = 'إدارة العملاء';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الأساسية')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('الفرع الرئيسي')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الفرع')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city_id')
                            ->label('المدينة (ID)')
                            ->numeric(),
                        Forms\Components\Textarea::make('address')
                            ->label('العنوان')
                            ->rows(2),
                        Forms\Components\TextInput::make('gps_location')
                            ->label('الموقع GPS')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('الإدارة')
                    ->schema([
                        Forms\Components\Select::make('manager_id')
                            ->label('مدير الفرع (مستخدم النظام)')
                            ->relationship('manager', 'name')
                            ->searchable(),
                        Forms\Components\TextInput::make('manager_name')
                            ->label('اسم المدير (نص)')
                            ->maxLength(255),
                        Forms\Components\TagsInput::make('manager_phone')
                            ->label('أرقام هواتف المدير')
                            ->placeholder('اضغط Enter لإضافة رقم'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الفرع')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('تابع لـ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('العنوان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager.name')
                    ->label('المدير (مستخدم)'),
                Tables\Columns\TextColumn::make('manager_name')
                    ->label('المدير (اسم)'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
