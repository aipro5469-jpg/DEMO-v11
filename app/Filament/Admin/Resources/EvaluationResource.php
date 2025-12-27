<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EvaluationResource\Pages;
use App\Models\Evaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'التقييمات';
    protected static ?string $modelLabel = 'تقييم';
    protected static ?string $pluralModelLabel = 'التقييمات';
    protected static ?string $navigationGroup = 'التقييمات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات التقييم')
                    ->schema([
                        Forms\Components\Select::make('evaluation_template_id')
                            ->label('نموذج التقييم')
                            ->relationship('template', 'name')
                            ->required(),
                        Forms\Components\Select::make('evaluator_id')
                            ->label('المقيم')
                            ->relationship('evaluator', 'name')
                            ->required(),
                        Forms\Components\DateTimePicker::make('evaluated_at')
                            ->label('تاريخ التقييم')
                            ->default(now()),
                        Forms\Components\MorphToSelect::make('evaluable')
                            ->label('العنصر المقيم')
                            ->types([
                                Forms\Components\MorphToSelect\Type::make(\App\Models\Client::class)
                                    ->label('عميل')
                                    ->titleAttribute('name'),
                                Forms\Components\MorphToSelect\Type::make(\App\Models\Agent::class)
                                    ->label('وكيل')
                                    ->titleAttribute('name'),
                                Forms\Components\MorphToSelect\Type::make(\App\Models\Task::class)
                                    ->label('مهمة')
                                    ->titleAttribute('title'),
                            ])
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('النتائج')
                    ->schema([
                        Forms\Components\TextInput::make('total_score')
                            ->label('الدرجة الكلية')
                            ->numeric()
                            ->disabled(), // Usually calculated
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('template.name')
                    ->label('النموذج'),
                Tables\Columns\TextColumn::make('evaluator.name')
                    ->label('المقيم'),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('الدرجة'),
                Tables\Columns\TextColumn::make('evaluated_at')
                    ->label('التاريخ')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}
