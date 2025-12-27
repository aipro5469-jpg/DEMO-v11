<?php

namespace App\Filament\Admin\Resources\ClientEmployeeResource\Pages;

use App\Filament\Admin\Resources\ClientEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientEmployees extends ListRecords
{
    protected static string $resource = ClientEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
