<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\SaleTransactionResource\Pages;
use App\Filament\Customer\Resources\SaleTransactionResource\RelationManagers;
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

    return parent::getEloquentQuery()->where('customer_id', $userId);

   }

   public static function infolist(Infolist $infolist): Infolist
   {
   return $infolist
       ->schema([
           TextEntry::make('sales_name')
               ->label('Sales name')
               ->size(TextEntry\TextEntrySize::Large)
               ->weight(FontWeight::Bold)
               ->copyable()
               ->copyMessage('Copied!')
               ->copyMessageDuration(1500),
           TextEntry::make('staff.full_name')
               ->label('Processed by')
               ->size(TextEntry\TextEntrySize::Large)
               ->weight(FontWeight::Bold),
           Section::make([
               TextEntry::make('process_type')
                       ->label('Processed type')
                       ->badge()
                       ->color(fn (string $state): string => match ($state) {
                           'Online Appointment' => 'primary',
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
                       ->label('Selected services')
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
                           TextEntry::make('service.description')
                           ->label('Description'),
                       ])
                       ->columnSpan(2)
                       ->columns('5')
                   ]),
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
                TextColumn::make('sales_name')
                ->searchable(),
                ImageColumn::make('item.service.service_avatar')
                ->square()
                ->stacked(),
                TextColumn::make('process_type')
                ->label('Processed type')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Online Order' => 'primary',
                })
                ->sortable(),
                TextColumn::make('staff.full_name')
                ->label('Processed by'),
                TextColumn::make('total_amount')
                 ->money('PHP', true)
                 ->label('Total Amount')
                 ->sortable(),
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
            ->defaultSort('created_at', 'desc')
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
