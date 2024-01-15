<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource\RelationManagers;
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
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\Action;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoListSection;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\ImageEntry;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
{
    $unreadCount = static::getModel()::query()
        ->join('users', function ($join) {
            $join->on('messages.recipient_id', '=', 'users.id')
                ->whereNotIn('users.role_id', [3]);
        })
        ->where('messages.read', false)
        ->count();

        return $unreadCount >= 1 ? (string)$unreadCount : null;
}

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->schema([
                    Select::make('recipient_id')
                    ->label('Send to')
                    ->options(User::where('id', '!=', auth()->user()->id)
                    ->get()
                    ->pluck('full_name', 'id'))
                    ->required()
                    ->columnSpan('full')
                    ->searchable(),
                TextArea::make('content')
                    ->required()
                    ->columnSpan('full'),
                FileUpload::make('attached_file')
                ->label('Attached file(optional)')
                ->multiple(),
                ])
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('sender.FullName')
                ->label('From')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->hidden(function($record){
                    $true = $record->sender_id === auth()->id();

                    return $true;
                }),
                Infolists\Components\TextEntry::make('recipient.FullName')
                ->label('Send to')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->hidden(function($record){
                    $true = $record->recipient_id === auth()->id();

                    return $true;
                }),
                Infolists\Components\TextEntry::make('subject')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold),
                InfoListSection::make()
                ->schema([
                    TextEntry::make('content')
                    ->label('Content'),
                    ImageEntry::make('attached_file'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender.FullName')
                ->label('From'),
                TextColumn::make('recipient.FullName')
                ->label('To'),
                TextColumn::make('subject')
                ->weight(FontWeight::Bold),
                TextColumn::make('created_at')
                ->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('Mark as read')
                ->icon('heroicon-m-check-circle')
                ->action(fn(Message $record) => $record->update(['read' => true]))
                ->hidden(function ($record){
                    return $record->read === 1 || $record->sender_id === auth()->id();
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
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'view' => Pages\ViewMessage::route('/{record}'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }    
}
