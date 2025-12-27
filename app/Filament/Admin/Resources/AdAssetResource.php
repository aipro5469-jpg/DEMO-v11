<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdAssetResource\Pages;
use App\Filament\Admin\Resources\AdAssetResource\RelationManagers;
use App\Models\AdAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdAssetResource extends Resource
{
    protected static ?string $model = AdAsset::class;

    public static function getModelLabel(): string
    {
        return 'أصل إعلاني';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الأصول الإعلانية';
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الأصل')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الأصل')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('asset_code')
                            ->label('كود الأصل')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('سيتم إنشاؤه تلقائياً'),
                        Forms\Components\Select::make('type')
                            ->label('النوع')
                            ->options([
                                'electronic' => 'إلكتروني',
                                'furniture' => 'أثاث',
                                'vehicle' => 'مركبة',
                                'other' => 'أخرى',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'active' => 'نشط',
                                'maintenance' => 'صيانة',
                                'retired' => 'تالف/خارج الخدمة',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('الصور')
                    ->schema([
                        Forms\Components\FileUpload::make('photos')
                            ->label('صور الأصل')
                            ->image()
                            ->multiple()
                            ->directory(function (Forms\Get $get) {
                                $date = now()->format('Y-m-d');
                                $name = $get('name') ?? 'new-asset';
                                $name = str_replace(' ', '-', $name);
                                $name = preg_replace('/[^\p{L}\p{N}\-_]/u', '', $name);
                                return "assets/{$date}_{$name}";
                            })
                            ->getUploadedFileNameForStorageUsing(function (Forms\Get $get, $file) {
                                $date = now()->format('Y-m-d');
                                $name = $get('name') ?? 'new-asset';
                                $name = str_replace(' ', '-', $name);
                                $name = preg_replace('/[^\p{L}\p{N}\-_]/u', '', $name);
                                $extension = $file->getClientOriginalExtension();
                                $random = \Illuminate\Support\Str::random(5);
                                return "{$name}_{$date}_{$random}.{$extension}";
                            })
                            ->maxSize(2048)
                            ->panelLayout('grid'),
                    ]),
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
            'index' => Pages\ListAdAssets::route('/'),
            'create' => Pages\CreateAdAsset::route('/create'),
            'edit' => Pages\EditAdAsset::route('/{record}/edit'),
        ];
    }
}
