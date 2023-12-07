<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
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
use Filament\Tables\Filters\SelectFilter;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 3;

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

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                ->label('Order name')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->copyable()
                ->copyMessage('Copied!')
                ->copyMessageDuration(1500),
                TextEntry::make('payment_due')
                ->label('Total amount due')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->money('PHP', TRUE)
                ->color(fn (Model $record) => match (true) {
                    $record->payment_due == 0.00 => 'success',
                    $record->payment_due > 0 => 'danger', 
                })
                ->hidden(function(Model $record){
                    return ($record->mode_of_payment === 'g-cash-partial');
                }),
                TextEntry::make('payment_due')
                ->label('Balance')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->money('PHP', TRUE)
                ->color(fn (Model $record) => match (true) {
                    $record->payment_due == 0.00 => 'success',
                    $record->payment_due > 0 => 'danger', 
                })
                ->visible(function(Model $record){
                    return ($record->mode_of_payment === 'g-cash-partial');
                }),
                TextEntry::make('customer.FullName')
                ->label('Customer')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->copyable()
                ->copyMessage('Copied!')
                ->copyMessageDuration(1500),
                Section::make([
                    Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Scheduled' => 'primary',
                        'Completed' => 'success',
                        'Payment due' => 'info',
                        'Paid' => 'success',
                        'Cancelled' => 'danger',
                        'Missed' => 'warning',
                    }),
                    Infolists\Components\TextEntry::make('mode_of_payment')
                    ->badge()
                    ->label('MOP')
                    ->color(fn (string $state): string => match ($state) {
                        'g-cash' => 'primary',
                        'cash' => 'success',
                        'g-cash-partial' => 'info',
                    }),
                    Infolists\Components\TextEntry::make('appointment_date'),
                    Infolists\Components\TextEntry::make('time_slot.time_slot'),
                    Infolists\Components\TextEntry::make('sumOfItemValues')
                    ->label('Total price')
                    ->money('PHP', true)
                    ->weight(FontWeight::Bold),
                ])
                ->columnSpan('full')
                ->columns(5),
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
                    ->columns(4)
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
                ->sortable()
                ->searchable(),
                ImageColumn::make('item.service.service_avatar')
                ->square()
                ->stacked(),
                TextColumn::make('customer.full_name')
                ->sortable(),
                TextColumn::make('mode_of_payment')
                ->badge()
                ->sortable()
                ->color(fn (string $state): string => match ($state) {
                    'g-cash' => 'primary',
                    'cash' => 'success',
                    'g-cash-partial' => 'info',
                }),
                TextColumn::make('sumOfItemValues')
                ->label('Total Amount')
                ->money('PHP', TRUE)
                ->sortable(),
                TextColumn::make('appointment_date')
                ->date()
                ->sortable(),
                TextColumn::make('time_slot.time_slot'),
                TextColumn::make('status')
                ->badge()
                ->sortable()
                ->color(fn (string $state): string => match ($state) {
                    'Scheduled' => 'primary',
                    'Completed' => 'success',
                    'Payment due' => 'info',
                    'Paid' => 'success',
                    'Cancelled' => 'danger',
                    'Missed' => 'warning',
                }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                ->options([
                    'Completed' => 'Completed',
                    'Cancelled' => 'Cancelled',
                ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
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
