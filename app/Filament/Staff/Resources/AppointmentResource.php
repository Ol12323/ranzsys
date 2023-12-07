<?php

namespace App\Filament\Staff\Resources;

use App\Filament\Staff\Resources\AppointmentResource\Pages;
use App\Filament\Staff\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Shop';

    public static function getGloballySearchableAttributes(): array
    {
    return ['status', 'name',];
    }
    
    public static function getGlobalSearchResultUrl(Model $record): string
    {
    return AppointmentResource::getUrl('view', ['record' => $record]);
    }

    public static function getNavigationBadge(): ?string
    {

        return (string) static::getModel()::where('status', 'Scheduled')
            ->count();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Section::make([
                        Infolists\Components\TextEntry::make('name')
                        ->label('Appointment name'),
                        Infolists\Components\TextEntry::make('sumOfItemValues')
                    ->label('Total price')
                    ->money('PHP', true),
                    Infolists\Components\TextEntry::make('customer.full_name'),
                    ])->grow(),
                    Section::make([
                    Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Scheduled' => 'scheduled',
                        'Completed' => 'completed',
                        'Payment due' => 'due',
                        'Paid'  => 'paid',
                        'Cancelled' => 'cancelled',
                    }),
                    Infolists\Components\TextEntry::make('appointment_date'),
                    Infolists\Components\TextEntry::make('time_slot.time_slot'),
                    ]),
                ])->columnSpan('full'),
                Fieldset::make('Selected Services')
                ->schema([
                    RepeatableEntry::make('item')
                    ->schema([
                        Infolists\Components\ImageEntry::make('service.service_avatar')
                        ->label('')
                        ->height(50),
                        Infolists\Components\TextEntry::make('service.service_name')
                        ->label('Service Name'),
                        Infolists\Components\TextEntry::make('quantity'),
                        Infolists\Components\TextEntry::make('unit_price')
                        ->label('Sub Total')
                        ->money('PHP', true),
                        Infolists\Components\TextEntry::make('service.description')
                        ->label('Description'),
                    ])
                    ->columnSpan('full')
                    ->columns(5)
                ])->columnSpan('full')
            ]);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Appointment Name')
                ->searchable(),
                ImageColumn::make('item.service.service_avatar')
                ->square()
                ->stacked(),
                TextColumn::make('customer.full_name'),
                TextColumn::make('sumOfItemValues')
                ->label('Total Amount')
                ->money('PHP', TRUE),
                TextColumn::make('appointment_date')
                ->date(),
                TextColumn::make('time_slot.time_slot'),
                TextColumn::make('status')
                ->badge()
                ->sortable()
                ->color(fn (string $state): string => match ($state) {
                    'Scheduled' => 'scheduled',
                    'Completed' => 'completed',
                    'Payment due' => 'due',
                    'Paid'  => 'paid',
                    'Cancelled' => 'cancelled',
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            // ->bulkActions([
            //     // Tables\Actions\BulkActionGroup::make([
            //     //     Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }    
}
