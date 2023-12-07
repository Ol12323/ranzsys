<?php

namespace App\Filament\Customer\Resources\AppointmentResource\Pages;

use App\Filament\Customer\Resources\AppointmentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Appointment;
use App\Filament\Pages\Catalogue;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('browseServices')
            ->icon('heroicon-m-magnifying-glass')
            ->url(Catalogue::getUrl())
        ];
    }

    public function getTabs(): array
    {
        $userId = auth()->id();

        return [
            'all' => Tab::make(),
            'Today' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_id', $userId)->whereDate('appointment_date', today()))
                ->badge(Appointment::query()->where('customer_id', $userId)->whereDate('appointment_date', today())->count() >= 1 ? Appointment::query()->where('customer_id', $userId)->where('appointment_date', today())->count() : false),
            'Completed' => Tab::make() 
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_id', $userId)->where('status', 'Completed'))
                ->badge(Appointment::query()->where('customer_id', $userId)->where('status', 'Completed')->count() >= 1 ? Appointment::query()->where('customer_id', $userId)->where('status', 'Completed')->count() : false),
            'Cancelled' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_id', $userId)->where('status', 'Cancelled'))
                ->badge(Appointment::query()->where('customer_id', $userId)->where('status', 'Cancelled')->count() >= 1 ? Appointment::query()->where('customer_id', $userId)->where('status', 'Cancelled')->count() : false),
        ];
    }
}
