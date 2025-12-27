<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EvaluationCriterionResource\Pages;
use App\Filament\Admin\Resources\EvaluationCriterionResource\RelationManagers;
use App\Models\EvaluationCriterion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvaluationCriterionResource extends Resource
{
    protected static ?string $model = EvaluationCriterion::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationLabel = 'معايير التقييم';
    protected static ?string $modelLabel = 'معيار تقييم';
    protected static ?string $pluralModelLabel = 'معايير التقييم';
    protected static ?string $navigationGroup = 'التقييمات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل المعيار')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('key')
                            ->label('المفتاح (Key)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('evaluation_template_id')
                            ->label('النموذج')
                            ->relationship('template', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('weight')
                            ->label('الوزن النسبي')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('min_value')
                            ->label('أقل قيمة')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('max_value')
                            ->label('أعلى قيمة')
                            ->required()
                            ->numeric()
                            ->default(5),
                        Forms\Components\TextInput::make('order')
                            ->label('الترتيب')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('template.name')
                    ->label('النموذج')
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->label('الوزن')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_value')
                    ->label('Min')
                    ->numeric(),
                Tables\Columns\TextColumn::make('max_value')
                    ->label('Max')
                    ->numeric(),
                Tables\Columns\TextColumn::make('order')
                    ->label('الترتيب')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('evaluation_template_id')
                    ->label('النموذج')
                    ->relationship('template', 'name'),
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
            'index' => Pages\ListEvaluationCriteria::route('/'),
            'create' => Pages\CreateEvaluationCriterion::route('/create'),
            'edit' => Pages\EditEvaluationCriterion::route('/{record}/edit'),
        ];
    }
}
