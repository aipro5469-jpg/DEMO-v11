<?php

namespace App\Filament\Admin\Resources\GiftItemResource\Pages;

use App\Filament\Admin\Resources\GiftItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGiftItem extends ViewRecord
{
    protected static string $resource = GiftItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
