<?php

namespace App\Filament\Admin\Resources\InventoryBalanceResource\Pages;

use App\Filament\Admin\Resources\InventoryBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageInventoryBalances extends ManageRecords
{
    protected static string $resource = InventoryBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
