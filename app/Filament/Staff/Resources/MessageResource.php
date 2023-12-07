<?php

namespace App\Filament\Staff\Resources;

use App\Filament\Staff\Resources\MessageResource\Pages;
use App\Filament\Staff\Resources\MessageResource\RelationManagers;
use App\Models\Message;
use App\Models\User;
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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    protected static ?string $navigationLabel = 'Inbox';

    public static function getNavigationBadge(): ?string
    {
        $userId = auth()->id();

        return (string) static::getModel()::where('read', false)
            ->where('recipient_id', $userId)
            ->count();
    }

    public static function getEloquentQuery(): Builder
        {
            $userId = auth()->id();

            return parent::getEloquentQuery()->where('recipient_id', $userId);
            
        }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('sender_id')
                ->default(auth()->id()),
                Select::make('recipient_id')
                ->label('Send to')
                ->options(User::all()->pluck('full_name', 'id'))
                ->required()
                ->columnSpan('full')
                ->searchable(),
                TextArea::make('content')
                ->required()
                ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender.role.name')
                ->label('From')
                ->badge()
                ->sortable()
                ->color(fn (string $state): string => match ($state) {
                    'Owner' => 'owner',
                    'Staff' => 'staff',
                    'Customer' => 'customer',
                }),
                ImageColumn::make('sender.avatar')
                ->label('')
                ->circular(),
                TextColumn::make('sender.last_name')
                ->label(''),
                TextColumn::make('sender.first_name')
                ->label(''),
                TextColumn::make('content')
                ->limit(50),
                TextColumn::make('created_at')
                ->searchable()
                ->sortable()
                ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Mark as read')
                ->icon('heroicon-m-check-circle')
                ->action(fn(Message $record) => $record->update(['read' => true]))
                ->hidden(function ($record){
                    return $record->read === 1;
                }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMessages::route('/'),
        ];
    }    
}
