<?php

namespace App\Filament\Admin\Resources\InventoryAssignmentResource\Pages;

use App\Filament\Admin\Resources\InventoryAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryAssignment extends EditRecord
{
    protected static string $resource = InventoryAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
