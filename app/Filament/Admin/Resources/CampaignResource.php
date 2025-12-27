<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CampaignResource\Pages;
use App\Filament\Admin\Resources\CampaignResource\RelationManagers;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Basic Info')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('type')
                                ->options([
                                    'field' => 'Field Marketing',
                                    'phone' => 'Phone Campaign',
                                    'digital' => 'Digital Marketing',
                                    'sales' => 'Sales',
                                    'ads' => 'Ads',
                                ])
                                ->required(),
                            Forms\Components\DatePicker::make('start_date'),
                            Forms\Components\DatePicker::make('end_date'),
                            Forms\Components\Textarea::make('description')
                                ->columnSpanFull(),
                            Forms\Components\Hidden::make('creator_id')
                                ->default(auth()->id()),
                        ]),
                    Forms\Components\Wizard\Step::make('Objectives')
                        ->schema([
                            Forms\Components\Repeater::make('metadata.objectives')
                                ->label('Campaign Objectives')
                                ->schema([
                                    Forms\Components\TextInput::make('title')->required(),
                                    Forms\Components\TextInput::make('target_value')->numeric(),
                                    Forms\Components\Select::make('metric')
                                        ->options(['visits', 'sales', 'leads', 'conversions'])
                                        ->required(),
                                ])
                                ->columns(3),
                        ]),
                    Forms\Components\Wizard\Step::make('Tasks')
                        ->schema([
                            Forms\Components\Repeater::make('tasks')
                                ->relationship()
                                ->schema([
                                    Forms\Components\TextInput::make('title')->required(),
                                    Forms\Components\Textarea::make('description'),
                                    Forms\Components\Select::make('priority')
                                        ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                                        ->default('medium'),
                                    Forms\Components\DateTimePicker::make('due_date'),
                                ])
                                ->columns(2)
                                ->defaultItems(0),
                        ]),
                    Forms\Components\Wizard\Step::make('Review')
                        ->schema([
                            Forms\Components\Placeholder::make('review_text')
                                ->content('Please review the campaign details before creating.'),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}
