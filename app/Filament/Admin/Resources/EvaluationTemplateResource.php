<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EvaluationTemplateResource\Pages;
use App\Filament\Admin\Resources\EvaluationTemplateResource\RelationManagers;
use App\Models\EvaluationTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvaluationTemplateResource extends Resource
{
    protected static ?string $model = EvaluationTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'نماذج التقييم';
    protected static ?string $modelLabel = 'نموذج تقييم';
    protected static ?string $pluralModelLabel = 'نماذج التقييم';
    protected static ?string $navigationGroup = 'التقييمات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات النموذج')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم النموذج')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('evaluable_type')
                            ->label('نوع التقييم')
                            ->options([
                                'App\Models\Client' => 'عميل',
                                'App\Models\Agent' => 'وكيل',
                                'App\Models\Branch' => 'فرع',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->required(),
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
                Tables\Columns\TextColumn::make('evaluable_type')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\Models\Client' => 'عميل',
                        'App\Models\Agent' => 'وكيل',
                        'App\Models\Branch' => 'فرع',
                        default => $state,
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
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
            'index' => Pages\ListEvaluationTemplates::route('/'),
            'create' => Pages\CreateEvaluationTemplate::route('/create'),
            'edit' => Pages\EditEvaluationTemplate::route('/{record}/edit'),
        ];
    }
}
