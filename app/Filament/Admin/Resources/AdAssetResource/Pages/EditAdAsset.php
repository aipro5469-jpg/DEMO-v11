<?php

namespace App\Filament\Admin\Resources\AdAssetResource\Pages;

use App\Filament\Admin\Resources\AdAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdAsset extends EditRecord
{
    protected static string $resource = AdAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
