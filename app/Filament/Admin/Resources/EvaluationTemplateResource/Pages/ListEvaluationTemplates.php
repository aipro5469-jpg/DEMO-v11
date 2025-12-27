<?php

namespace App\Filament\Admin\Resources\EvaluationTemplateResource\Pages;

use App\Filament\Admin\Resources\EvaluationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluationTemplates extends ListRecords
{
    protected static string $resource = EvaluationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
