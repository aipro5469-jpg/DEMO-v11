<?php

namespace App\Filament\Admin\Resources\ClientEmployeeResource\Pages;

use App\Filament\Admin\Resources\ClientEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientEmployee extends EditRecord
{
    protected static string $resource = ClientEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
