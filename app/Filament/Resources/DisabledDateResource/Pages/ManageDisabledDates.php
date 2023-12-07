<?php

namespace App\Filament\Resources\DisabledDateResource\Pages;

use App\Filament\Resources\DisabledDateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDisabledDates extends ManageRecords
{
    protected static string $resource = DisabledDateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
