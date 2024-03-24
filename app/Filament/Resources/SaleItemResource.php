<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleItemResource\Pages;
use App\Filament\Resources\SaleItemResource\RelationManagers;
use App\Models\SaleItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;


class SaleItemResource extends Resource
{
    protected static ?string $model = SaleItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationLabel = 'Sales';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
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
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('sale_transaction.sales_name')
                ->searchable(),
                ImageColumn::make('service.service_avatar')
                ->label('Service avatar'),
                TextColumn::make('service.service_name')
                ->label('Service name')
                ->searchable()
                ->sortable(),
                TextColumn::make('service.price')
                ->money('PHP', true)
                ->label('Price')
                ->sortable(),
                TextColumn::make('quantity')
                ->sortable(),
                TextColumn::make('total_price')
                ->money('PHP', true)
                ->label('Total price')
                ->sortable()
                ->summarize([
                    Sum::make()
                    ->money('PHP', true),
                ]),
            ])
            ->filters([
                Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from')
                    ->label('From')
                    ->native(false)
                    ->closeOnDateSelection(),
                    DatePicker::make('created_until')
                    ->label('Until')
                    ->native(false)
                    ->closeOnDateSelection(),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSaleItems::route('/'),
            'create' => Pages\CreateSaleItem::route('/create'),
            'edit' => Pages\EditSaleItem::route('/{record}/edit'),
        ];
    }    
}
