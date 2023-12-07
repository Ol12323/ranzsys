<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Appointment;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Today' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('appointment_date', today()))
                ->badge(Appointment::query()->whereDate('appointment_date', today())->count() >= 1 ? Appointment::query()->where('appointment_date', today())->count() : false),
            'Completed' => Tab::make() 
                ->modifyQueryUsing(fn (Builder $query) =>$query->where('status', 'Completed'))
                ->badge(Appointment::query()->where('status', 'Completed')->count() >= 1 ? Appointment::query()->where('status', 'Completed')->count() : false),
            'Cancelled' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) =>$query->where('status', 'Cancelled'))
                ->badge(Appointment::query()->where('status', 'Cancelled')->count() >= 1 ? Appointment::query()->where('status', 'Cancelled')->count() : false),
        ];
    }
}
