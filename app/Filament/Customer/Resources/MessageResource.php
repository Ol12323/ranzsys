<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\MessageResource\Pages;
use App\Filament\Customer\Resources\MessageResource\RelationManagers;
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
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as InfoListSection;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Facades\Auth;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $unreadCount = static::getModel()::query()
            ->where('read', false)
            ->whereHas('messageContent', function (Builder $query){
                $authId = Auth::id();
                
                $query->where('sender_id', $authId)
                      ->orWhere('recipient_id', $authId);
            })
            ->count();

        return $unreadCount >= 1 ? (string)$unreadCount : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->schema([
                Placeholder::make('sendTo')
                    ->content(function () {
                        return new HtmlString('<p class="font-bold">Ranz photography</p>');
                     }),
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
                TextEntry::make('subject')
                ->size(TextEntry\TextEntrySize::Large)
                ->weight(FontWeight::Bold)
                ->columnSpan(2),
                RepeatableEntry::make('messageContent')
                ->label('')
                ->schema([
                    TextEntry::make('me')
                    ->visible(function(Model $record){
                        return auth()->user()->id === $record->sender_id;
                    })
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                    TextEntry::make('sender.FullName')
                    ->label('From')
                    ->hidden(function(Model $record){
                        return auth()->user()->id === $record->sender_id;
                    })
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                    TextEntry::make('created_at')
                    ->label('')
                    ->since(),
                    TextEntry::make('body')
                    ->label('')
                    ->columnSpan(2),
                    ImageEntry::make('image_path')
                    ->label(''),
                    // ->visible(function(Model $record){
                    //     return $record->messageContent->image_path !== null;
                    // })
                ])->columnSpan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        $authId = Auth::id();

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($authId) {
                // Remove global scopes
                $query->withoutGlobalScopes();

                // Add additional conditions to the query
                $query->whereHas('messageContent', function ($query) {
                    $authId = Auth::id();
                    
                    $query->where('sender_id', $authId)
                          ->orWhere('recipient_id', $authId);
                });
    
            })
            ->columns([
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
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateActions([
                //Tables\Actions\CreateAction::make(),
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
