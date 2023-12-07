<?php

namespace App\Filament\Staff\Resources;

use App\Filament\Staff\Resources\SaleTransactionResource\Pages;
use App\Filament\Staff\Resources\SaleTransactionResource\RelationManagers;
use App\Models\SaleTransaction;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use App\Models\Service;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Support\Enums\FontWeight;

class SaleTransactionResource extends Resource
{
    protected static ?string $model = SaleTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
 
     $userId = auth()->id();
 
     return parent::getEloquentQuery()->where('processed_by', $userId);
 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Choose Services')
                        ->schema([
                            Repeater::make('item')
                            ->relationship()
                            ->label('Services')
                            ->schema([
                                Select::make('service_id')
                                ->label('Service Name')
                                ->options(Service::query()->pluck('service_name', 'id'))
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function($state, callable
                                $set){
                                    $service= Service::find($state);
                                     if ($service) {
                                        $set('price', number_format
                                        ($service->price));
                                        $set('unit_price', $service->price);
                                        $set('price', $service->price);
                                        $set('total_price', $service->price);
                                        $set('service_price', $service->price);
                                        $set('service_price_visible', $service->price);
                                        $set('total_price_visible', $service->price);
                                        $set('service_name', $service->service_name);
                                        $set('description', $service->description);
                                     }
                                }),
                                Hidden::make('service_name'),
                                TextArea::make('description')
                                ->disabled()
                                ->hidden(fn (Get $get) => $get('service_id') === null ),
                                Hidden::make('service_price'),
                                TextInput::make('service_price_visible')
                                ->disabled()
                                ->label('Price')
                                ->prefix('₱')
                                ->hidden(fn (Get $get) => $get('service_id') === null ),
                                TextInput::make('quantity')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->minValue(1)
                                                    ->reactive()
                                                    ->afterStateUpdated(function($state, callable $set, $get) {
                                                        $quantity = (int)$get('quantity');
                                                        $price = (float)$get('service_price');
                                                        
                                                        if ($quantity >= 0 && $price >= 0) {
                                                            $total = $quantity * $price;
                                                            $set('total_price_visible', number_format($total, 2));
                                                            $set('total_price', number_format($total, 2)); // Format total price with 2 decimal places
                                                        }
                                                    })
                                                    ->hidden(fn (Get $get) => $get('service_id') === null ),
                                Hidden::make('total_price'),
                                TextInput::make('total_price_visible')
                                                    ->disabled()
                                                    ->label('Sub total')
                                                    ->required()
                                                    ->prefix('₱')
                                                    ->dehydrated(false)
                                                    ->hidden(fn (Get $get) => $get('service_id') === null ),       
                            ])
                            ->addActionLabel('Add services')
                            ->collapsible()
                        ]),
                    Wizard\Step::make('Process Payment')
                        ->schema([
                            Hidden::make('sales_name')
                            ->label('Appointment Name')
                            ->default(Str::random(10))
                            ->unique(),
                            Hidden::make('process_type')
                            ->default('Walk-in'),
                            Hidden::make('processed_by')
                            ->default(auth()->id()),
                            Placeholder::make('total_amount')
                                ->label("Total Amount")
                                ->content(function ($get) {
                                    return '    '.'₱'.' '. collect($get('item'))
                                        ->pluck('total_price')
                                        ->sum();
                                }),
                            TextInput::make('amount_recieved')
                                ->prefix('₱')
                                ->placeholder('Enter customer cash...')
                                ->required()
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(function ($set, $get, $state, $record) {

                                    $total_amount = collect($get('item'))
                                        ->pluck('total_price')
                                        ->sum();

                                    $change = ($state) - $total_amount;
                                    if ($change < 0) {
                                        $set('change_visible', 'Insufficient cash');
                                        $set('change', 'Insufficient cash');
                                    } else {
                                        $set('change_visible', max(0, $change));
                                        $set('customer_cash_change', max(0, $change));
                                        $set('total_amount', max(0, $total_amount));
                                    }
                                }),
                            Hidden::make('total_amount'),
                            TextInput::make('change_visible')
                                ->prefix('₱')
                                ->disabled()
                                ->label('Change')
                                ->doesntStartWith(['Insufficient cash']),
                            Hidden::make('customer_cash_change')
                                ->doesntStartWith(['Insufficient cash']),
                        ]),
                ])
                ->columnSpan('full')
                ->submitAction(new HtmlString('<button type="submit" class="bg-primary-600 hover:bg-primary-600 text-white font-bold py-2 px-4 rounded">
                Create
            </button>
            '))
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
    return $infolist
        ->schema([
            Split::make([
                Section::make([
                 TextEntry::make('sales_name'),
                 TextEntry::make('total_amount')
                 ->money('PHP', true)
                 ->label('Total Price'),
                 TextEntry::make('customer_cash_change')
                 ->money('PHP', true)
                 ->label('Change'),
                ])
                ->grow(),
                Section::make([
                    TextEntry::make('process_type')
                        ->label('Processed Type')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Online Appointment' => 'p1',
                            'Online Order' => 'p2',
                            'Walk-in' => 'p3',
                        }),
                    TextEntry::make('customer.full_name')
                             ->default('Guest'),
                    TextEntry::make('staff.full_name')
                             ->label('Processed by'),
                ]),
            ])->columnSpan('full'),

            Fieldset::make('Services List')
            ->schema([
            RepeatableEntry::make('item')
                ->schema([
                    ImageEntry::make('service.service_avatar')
                    ->height(50),
                    TextEntry::make('service.service_name')
                    ->label('Name'),
                    TextEntry::make('service.description')
                    ->label('Description'),
                    TextEntry::make('service.price')
                    ->money('PHP', true)
                    ->label('Price'),
                    TextEntry::make('quantity'),
                    TextEntry::make('total_price')
                    ->money('PHP', true)
                    ->label('Sub Total'),
                ])
                ->columnSpan(2)
                ->columns('6')
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sales_name')
                ->searchable(),
                ImageColumn::make('item.service.service_avatar')
                ->square()
                ->stacked(),
                TextColumn::make('process_type')
                ->label('Processed type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Online Appointment' => 'p1',
                    'Online Order' => 'p2',
                    'Walk-in' => 'p3',
                })
                ->sortable(),
                TextColumn::make('customer.full_name')
                ->label('Customer')
                ->default('Customer: Guest'),
                TextColumn::make('total_amount')
                 ->money('PHP', true)
                 ->label('Total Amount')
                 ->sortable()
                 ->summarize([
                    Sum::make()
                    ->money('PHP', true),
                ]),
                 TextColumn::make('customer_cash_change')
                 ->money('PHP', true)
                 ->label('Change'),
                 TextColumn::make('updated_at')
                 ->label('Transaction date')
                 ->date(),
            ])
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
            'index' => Pages\ListSaleTransactions::route('/'),
            'create' => Pages\CreateSaleTransaction::route('/create'),
            'view' => Pages\ViewSaleTransaction::route('/{record}'),
            'edit' => Pages\EditSaleTransaction::route('/{record}/edit'),
        ];
    }    
}
