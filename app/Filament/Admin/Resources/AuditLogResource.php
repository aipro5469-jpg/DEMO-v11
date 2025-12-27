<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuditLogResource\Pages;
use App\Filament\Admin\Resources\AuditLogResource\RelationManagers;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'سجل النشاطات';
    protected static ?string $pluralLabel = 'سجل النشاطات';
    protected static ?string $navigationGroup = 'النظام';
    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل النشاط')
                    ->schema([
                        Forms\Components\TextInput::make('event')
                            ->label('الحدث')
                            ->disabled(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('المستخدم')
                            ->disabled(),
                        Forms\Components\TextInput::make('auditable_type')
                            ->label('نوع السجل')
                            ->formatStateUsing(fn($state) => class_basename($state))
                            ->disabled(),
                        Forms\Components\TextInput::make('auditable_id')
                            ->label('رقم السجل')
                            ->disabled(),
                        Forms\Components\KeyValue::make('old_values')
                            ->label('القيم القديمة')
                            ->disabled(),
                        Forms\Components\KeyValue::make('new_values')
                            ->label('القيم الجديدة')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('الحدث')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'login' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'created' => 'إضافة',
                        'updated' => 'تعديل',
                        'deleted' => 'حذف',
                        'login' => 'تسجيل دخول',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('السجل')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التوقيت')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('نوع الحدث')
                    ->options([
                        'created' => 'إضافة',
                        'updated' => 'تعديل',
                        'deleted' => 'حذف',
                        'login' => 'تسجيل دخول',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
