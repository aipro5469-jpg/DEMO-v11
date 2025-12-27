<?php

namespace App\Filament\Admin\Resources\InventoryAssignmentResource\Pages;

use App\Filament\Admin\Resources\InventoryAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventoryAssignments extends ListRecords
{
    protected static string $resource = InventoryAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
