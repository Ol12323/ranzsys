<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\OrderResource\Pages;
use App\Filament\Customer\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Actions\Action as InfoAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\FontWeight;
use App\Filament\Pages\Catalogue;
use Filament\Tables\Actions\Action;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 5;

    public static function getGloballySearchableAttributes(): array
    {
        return ['order_name'];
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user()->id;

        $count = static::getModel()::query()
            ->where('user_id', $user)
            ->where(function($query) {
                $query->where('status', '!=', 'Completed')
                    ->where('status', '!=', 'Canceled');
            })
            ->count();

        return $count >= 1 ? (string)$count : null;
    }
  
    public static function getEloquentQuery(): Builder
        {
            $userId = auth()->user()->id;

            return parent::getEloquentQuery()->where('user_id', $userId);
            
        }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('order_name')
                ->label('Order name')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->copyable()
                ->copyMessage('Copied!')
                ->copyMessageDuration(1500),
                Fieldset::make('Order information')
                ->label('')
                ->schema([
                    TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Declined' => 'danger',
                        'Pending' => 'gray',
                        'Payment method confirmed' => 'info',
                        'Payment received' => 'primary',
                        'Confirmed' => 'primary',
                        'In progress' => 'warning',
                        'Ready for pickup' => 'success',
                        'Completed' => 'success',
                        'Select payment method' => 'warning',
                        'Picked up' => 'primary',
                        'Missed' => 'danger',
                        'Cancelled' => 'gray'
                    }),
                    TextEntry::make('service_date')
                    ->icon('heroicon-m-clock')
                    ->date(),
                    TextEntry::make('time_slot.time_slot')
                    ->hidden(function(Model $record){
                        return ($record->time_slot_id === NULL);
                    }),
                    TextEntry::make('sumOfItemValues')
                    ->label('Total amount')
                    ->money('PHP', TRUE)
                    ->weight(FontWeight::Bold),
                    TextEntry::make('mode_of_payment')
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'g-cash' => 'primary',
                        'g-cash-partial' => 'info',
                        'Not yet applicable' => 'gray'
                    }),
                    TextEntry::make('sumOfItemValues')
                ->label('Total amount due')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->money('PHP', TRUE)
                ->color(fn (Model $record) => match (true) {
                    $record->sumOfItemValues == 0.00 => 'success',
                    $record->sumOfItemValues > 0 => 'danger', 
                })
                ->visible(function(Model $record){
                    return $record->status === 'Pending';
                }),
                TextEntry::make('payment_due')
                ->label('Total amount due')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->money('PHP', TRUE)
                ->color(fn (Model $record) => match (true) {
                    $record->payment_due == 0.00 => 'success',
                    $record->payment_due > 0 => 'danger', 
                })
                ->visible(function(Model $record){
                    return $record->status != 'Pending';
                })
                ->hidden(function(Model $record){
                    return ($record->status === 'In progress' || 'Ready for pickup' || 'Picked up' || 'Completed') AND ($record->mode_of_payment === 'g-cash-partial');
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
                    return ($record->mode_of_payment === 'g-cash-partial' AND $record->status === 'In progress' || 'Ready for pickup' || 'Picked up' || 'Completed');
                }), 
                ]) ->columns(5),
                Fieldset::make('Order services')
                ->schema([
                    RepeatableEntry::make('service')
                    ->label('')
                    ->schema([
                        ImageEntry::make('service.service_avatar')
                        ->label(''),
                        TextEntry::make('service.service_name'),
                        TextEntry::make('price')
                        ->money('PHP', TRUE),
                        TextEntry::make('quantity'),
                        TextEntry::make('subtotal')
                        ->money('PHP', TRUE),
                        TextEntry::make('design_file_path')
                        ->visible(function(Model $record){
                            return $record->design_type === 'have_design';
                        })
                        ->limit(5)
                        ->label('Design file')
                        ->suffixAction(
                            InfoAction::make('viewImage')
                                ->label('View design file')
                                ->modalSubmitAction(false)
                                ->icon('heroicon-m-folder-open')
                                ->form([
                                    FileUpload::make('fileImage')
                                    ->label('File')
                                    ->downloadable()
                                    ->disabled()
                                    ->openable()
                                    ->default(function (Model $record) {
                                        foreach ($record->service as $services) {
                                            $image = $services->design_file_path;
                                            return $image;
                                        }
                                   }),
                                ])
                        ),
                        TextEntry::make('design_description')
                        ->visible(function(Model $record){
                            return $record->design_type === 'describe_design';
                        })
                        ->limit(5)
                        ->label('Design description')
                        ->suffixAction(
                            InfoAction::make('viewImage')
                                ->label('View design description')
                                ->modalSubmitAction(false)
                                ->icon('heroicon-m-folder-open')
                                ->form([
                                    TextArea::make('design_description')
                                    ->disabled()
                                    ->default(function (Model $record) {
                                        foreach ($record->service as $services) {
                                            $description = $services->design_description;
                                            return $description;
                                        }
                                   })
                                ])
                            ),
                        TextEntry::make('service.description')
                        ->label('Description')
                        ->markdown()
                        ->html(),
                    ])
                    ->columnSpan('full')
                    ->columns(5)
                ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_name')
                ->searchable()
                ->sortable(),
                ImageColumn::make('service.service.service_avatar')
                ->square()
                ->stacked()
                ->label('Orders'),
                TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Declined' => 'danger',
                    'Pending' => 'gray',
                    'Payment method confirmed' => 'info',
                    'Payment received' => 'primary',
                    'Confirmed' => 'primary',
                    'In progress' => 'warning',
                    'Ready for pickup' => 'success',
                    'Completed' => 'success',
                    'Select payment method' => 'warning',
                    'Picked up' => 'primary',
                    'Missed' => 'danger',
                    'Cancelled' => 'gray'
                }),
                TextColumn::make('service_date')
                ->icon('heroicon-m-clock')
                ->date()
                ->sortable(),
                TextColumn::make('sumOfItemValues')
                ->label('Total Amount')
                ->money('PHP', TRUE),
                TextColumn::make('mode_of_payment')
                ->color(fn (string $state): string => match ($state) {
                    'cash' => 'success',
                    'g-cash' => 'primary',
                    'g-cash-partial' => 'info',
                    'Not yet applicable' => 'gray'
                })
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }    
}
