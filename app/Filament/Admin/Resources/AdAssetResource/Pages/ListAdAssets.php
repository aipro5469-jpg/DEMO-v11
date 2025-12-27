<?php

namespace App\Filament\Admin\Resources\AdAssetResource\Pages;

use App\Filament\Admin\Resources\AdAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdAssets extends ListRecords
{
    protected static string $resource = AdAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
