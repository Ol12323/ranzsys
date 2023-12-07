<?php

namespace App\Filament\Customer\Resources\CartResource\Pages;

use App\Filament\Customer\Resources\CartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Appointment;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;

class EditCart extends EditRecord
{
    protected static string $resource = CartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
