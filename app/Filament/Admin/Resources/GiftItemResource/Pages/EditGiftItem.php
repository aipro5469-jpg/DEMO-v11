<?php

namespace App\Filament\Admin\Resources\GiftItemResource\Pages;

use App\Filament\Admin\Resources\GiftItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGiftItem extends EditRecord
{
    protected static string $resource = GiftItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
