<?php

namespace App\Filament\Admin\Resources\EvaluationCriterionResource\Pages;

use App\Filament\Admin\Resources\EvaluationCriterionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluationCriteria extends ListRecords
{
    protected static string $resource = EvaluationCriterionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
