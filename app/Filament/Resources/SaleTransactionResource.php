<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleTransactionResource\Pages;
use App\Filament\Resources\SaleTransactionResource\RelationManagers;
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
use Illuminate\Support\Facades\Blade;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Filters\SelectFilter;

class SaleTransactionResource extends Resource
{
    protected static ?string $model = SaleTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Shop';

    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->id();

        if(auth()->user()->role->name === 'Staff')
        {

        return parent::getEloquentQuery()->where('processed_by', $userId);
        
        }

        return parent::getEloquentQuery();
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
            TextEntry::make('sales_name')
                ->label('Invoice No.')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->copyable()
                ->copyMessage('Copied!')
                ->copyMessageDuration(1500),
            TextEntry::make('customer.full_name')
                ->label('Customer')
                ->default('Guest')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold),
            TextEntry::make('staff.full_name')
                ->label('Processed By')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold),
            Fieldset::make('Order')
                ->label('')
                ->schema([   
            Section::make([
                TextEntry::make('process_type')
                        ->label('Processed type')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Online Order' => 'primary',
                            'Walk-in' => 'success',
                        }),
                TextEntry::make('total_amount')
                             ->money('PHP', true)
                             ->label('Total amount')
                             ->weight(FontWeight::Bold),
                TextEntry::make('customer_cash_change')
                             ->money('PHP', true)
                             ->label('Change')
                             ->weight(FontWeight::Bold),
                    ])->columns(3),
                Fieldset::make('Services List')
                    ->schema([
                    RepeatableEntry::make('item')
                    ->label('')
                        ->schema([
                            ImageEntry::make('service.service_avatar')
                            ->height(50),
                            TextEntry::make('service.service_name')
                            ->label('Name'),
                            TextEntry::make('service.price')
                            ->money('PHP', true)
                            ->label('Price'),
                            TextEntry::make('quantity'),
                            TextEntry::make('total_price')
                            ->label('Subtotal')
                            ->money('PHP', true),
                        ])
                        ->columnSpan(2)
                        ->columns('5')
                    ])
                ]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing( function ($query){
                return $query->with(['customer', 'staff']);
            })
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('sales_name')
                ->label('Invoice No.')
                ->searchable()
                ->sortable(),
                TextColumn::make('process_type')
                ->label('Processed Type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Online Order' => 'primary',
                    'Walk-in' => 'success',
                })
                ->sortable(),
                TextColumn::make('customer.full_name')
                ->default('Customer: Guest'),
                TextColumn::make('staff.full_name')
                ->label('Processed By')
                ->sortable(),
                TextColumn::make('total_amount')
                 ->money('PHP', true)
                 ->sortable()
                 ->summarize([
                    Sum::make()
                    ->money('PHP', true),
                ]),
                 TextColumn::make('customer_cash_change')
                 ->money('PHP', true)
                 ->label('Change')
                 ->sortable(),
                 TextColumn::make('created_at')
                 ->label('Transaction Date')
                 ->date()
                 ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('process_type')
                ->label('Process type')
                ->options([
                    'Walk-in' => 'Walk-in',
                    'Online Order' => 'Online Order',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ->emptyStateActions([
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
