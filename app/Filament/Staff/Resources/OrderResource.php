<?php

namespace App\Filament\Staff\Resources;

use App\Filament\Staff\Resources\OrderResource\Pages;
use App\Filament\Staff\Resources\OrderResource\RelationManagers;
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
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Shop';

    public static function getNavigationBadge(): ?string
    {

        return (string) static::getModel()::where('status', 'Pending')
            ->count();
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
                TextColumn::make('order_name'),
                ImageColumn::make('user.avatar')
                ->circular()
                ->label('Customer'),
                TextColumn::make('user.FullName')
                ->label(''),
                TextColumn::make('sumOfItemValues')
                ->label('Total Amount')
                ->money('PHP', TRUE),
                TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Declined'=> 'danger',
                    'Pending' => 'Pending', 
                    'Payment received' => 'Payment received', 
                    'In progress' => 'In progress',       
                    'Ready for pickup' => 'Ready for pickup',  
                    'Completed' => 'Completed',  
                    'To be paid' => 'To be paid', 
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')
                ->requiresConfirmation()
                ->modalHeading('Approve pending order')
                ->modalDescription('Are you sure you\'d like to approve this pending order? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, approve it')
                ->icon('heroicon-m-clipboard-document-check')
                ->visible(
                    function (Model $record) {
                        return $record->status === 'Pending';
                    }
                )
                ->action(
                    function (Order $record): void {
                        $record->status = 'To be paid';

                        $record->save();
                    }
                ),
                Action::make('decline')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Approve pending order')
                ->modalDescription('Are you sure you\'d like to approve this pending order? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, approve it')
                ->icon('heroicon-m-x-circle')
                ->visible(
                    function (Model $record) {
                        return $record->status === 'Pending';
                    }
                )
                ->action(
                    function (Order $record): void {
                        $record->status = 'To be paid';

                        $record->save();
                    }
                ),
                Tables\Actions\ViewAction::make(),
            ])
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }    
}
