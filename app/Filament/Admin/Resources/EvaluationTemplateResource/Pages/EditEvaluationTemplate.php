<?php

namespace App\Filament\Admin\Resources\EvaluationTemplateResource\Pages;

use App\Filament\Admin\Resources\EvaluationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluationTemplate extends EditRecord
{
    protected static string $resource = EvaluationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
