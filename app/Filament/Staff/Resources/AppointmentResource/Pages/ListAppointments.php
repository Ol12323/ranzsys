<?php

namespace App\Filament\Staff\Resources\AppointmentResource\Pages;

use App\Filament\Staff\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

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
            'Scheduled' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Scheduled')),
            'Completed' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Completed')),
            'Cancelled' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Cancelled')),
        ];
    }
}
