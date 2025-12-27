<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ClientEmployeeResource\Pages;
use App\Filament\Admin\Resources\ClientEmployeeResource\RelationManagers;
use App\Models\ClientEmployee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientEmployeeResource extends Resource
{
    protected static ?string $model = ClientEmployee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'موظفي العملاء';
    protected static ?string $modelLabel = 'موظف عميل';
    protected static ?string $pluralModelLabel = 'موظفي العملاء';
    protected static ?string $navigationGroup = 'إدارة العملاء';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label('العميل')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('role')
                    ->label('المسمى الوظيفي')
                    ->maxLength(255),
                Forms\Components\Repeater::make('phone')
                    ->label('أرقام الهاتف')
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->hiddenLabel()
                            ->required()
                            ->numeric()
                            ->length(9)
                            ->regex('/^[0-9]{9}$/')
                            ->placeholder('77xxxxxxx')
                            ->mask('999999999')
                    ])
                    ->addActionLabel('إضافة رقم آخر')
                    ->defaultItems(1)
                    ->grid(2)
                    ->columnSpanFull()
                    ->formatStateUsing(fn($state) => collect($state ?? [])->map(fn($item) => ['number' => $item])->toArray())
                    ->dehydrateStateUsing(fn($state) => collect($state ?? [])->pluck('number')->toArray()),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_contact_point')
                    ->label('نقطة تواصل')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('المسمى الوظيفي')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_contact_point')
                    ->label('نقطة تواصل')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('client')
                    ->label('العميل')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListClientEmployees::route('/'),
            'create' => Pages\CreateClientEmployee::route('/create'),
            'edit' => Pages\EditClientEmployee::route('/{record}/edit'),
        ];
    }
}
