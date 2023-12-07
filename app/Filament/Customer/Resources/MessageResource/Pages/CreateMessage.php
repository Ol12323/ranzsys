<?php

namespace App\Filament\Customer\Resources\MessageResource\Pages;

use App\Filament\Customer\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMessage extends CreateRecord
{
    protected static string $resource = MessageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['subject'] = 'Personal message';
        $data['sender_id'] = auth()->id();
        $data['recipient_id'] = 1;
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Message sent.';
    }
}
