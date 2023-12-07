<?php

namespace App\Filament\Customer\Resources\AppointmentResource\Pages;

use App\Filament\Customer\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Appointment;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $not_unique = Appointment::where([
					['appointment_date','=', $data['appointment_date']], 
					['time_slot_id', '=', $data['time_slot_id']]
		])->exists();

        if ($not_unique) {
            Notification::make()
                ->title('Appointment date and timeslot are unavailable. Please choose another.')
                ->danger()
                ->send();
                
                throw ValidationException::withMessages(['Apologies, the chosen timeslot is no longer available. Please pick an alternate time. Thank you.']);
        }
        
        return $data;

    }

}
