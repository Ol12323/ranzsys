<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\AppointmentResource\Pages;
use App\Filament\Customer\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\DisabledDate;
use App\Models\TimeSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Str;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DatePicker;
use Closure;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Component;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Pages\Catalogue;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        $unreadCount = static::getModel()::query()
            ->where('customer_id', auth()->id())
            ->where('status', 'Scheduled')
            ->count();

        return $unreadCount >= 1 ? (string)$unreadCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getGloballySearchableAttributes(): array
    {
    return ['status', 'name',];
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->id();

        return parent::getEloquentQuery()->where('customer_id', $userId);
        
    }
    
    public static function getGlobalSearchResultUrl(Model $record): string
    {
    return AppointmentResource::getUrl('view', ['record' => $record]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                ->label('Appointment name')
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
                    ->label('Total amount')
                    ->money('PHP', true)
                    ->weight(FontWeight::Bold),
                ])
                ->columnSpan('full')
                ->columns(5),
                Fieldset::make('Appointment services')
                ->schema([
                    RepeatableEntry::make('item')
                    ->label('')
                    ->schema([
                        Infolists\Components\ImageEntry::make('service.service_avatar')
                        ->label('')
                        ->height(50),
                        Infolists\Components\TextEntry::make('service.service_name')
                        ->label('Service Name'),
                        Infolists\Components\TextEntry::make('quantity'),
                        Infolists\Components\TextEntry::make('unit_price')
                        ->label('Sub total')
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
                TextColumn::make('sumOfItemValues')
                ->label('Total amount')
                ->money('PHP', TRUE),
                TextColumn::make('mode_of_payment')
                ->badge()
                ->sortable()
                ->color(fn (string $state): string => match ($state) {
                    'g-cash' => 'primary',
                    'cash' => 'success',
                    'g-cash-partial' => 'info',
                    'Missed' => 'warning',
                }),
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
                TextColumn::make('created_at')
                ->since(),
                
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
                Action::make('browseServices')
                ->icon('heroicon-m-magnifying-glass')
                ->url(Catalogue::getUrl())
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
