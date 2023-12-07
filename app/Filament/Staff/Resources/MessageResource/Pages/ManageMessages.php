<?php

namespace App\Filament\Staff\Resources\MessageResource\Pages;

use App\Filament\Staff\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMessages extends ManageRecords
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
