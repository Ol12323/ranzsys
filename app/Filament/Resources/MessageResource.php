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
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Actions\Action as InfoAction;
use Illuminate\Support\Facades\Auth;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {    
        $authId = Auth::id();
    
        $unreadCount = Message::query()
        ->whereHas('messageContent', function ($query) use ($authId) {
            $query->where('recipient_id', $authId)
                ->whereColumn('messages_id', 'messages.id') // Assuming message_id is the foreign key linking messageContent to Message
                ->where('created_at', function ($subQuery) {
                    $subQuery->from('message_contents')
                             ->selectRaw('MAX(created_at)');
                });
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
                    ->label(function(Model $record){
                        return $record->sender->role->name;
                    })
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
                    ->label('Images')
                    ->height(40)
                    ->square()
                    ->stacked()
                    ->visible(function(Model $record){
                        $bool = $record->image_path;

                        if($bool == null){
                            return false;
                        }else{
                            return true;
                        }
                    })
                    ->columnSpan(2)
                    ->hintAction(
                        InfoAction::make('viewImages')
                            ->label('View Images')
                            ->modalSubmitAction(false)
                            ->icon('heroicon-m-eye')
                            ->form([
                                FileUpload::make('images')
                                ->label('')
                                ->downloadable()
                                ->multiple()
                                ->disabled()
                                ->openable()
                                ->default(function (Model $record) {
                                    $imagePaths = []; // Initialize an empty array to store image paths

                                    foreach ($record->messageContent as $contents) {
                                        // If image_path is cast as an array, you need to merge it with the existing array
                                        $imagePaths = array_merge($imagePaths, $contents->image_path);
                                    }

                                    return $imagePaths;
                                }),
                            ])
                    ),
                ])->columnSpan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Remove global scopes
                $query->withoutGlobalScopes();

                // Add additional conditions to the query
                $query->whereHas('messageContent', function ($query) {
                    $query->where('sender_id', 1)
                        ->orWhere('recipient_id', 1);
                });

            })
            ->columns([
                TextColumn::make('subject')
                ->weight(FontWeight::Bold)
                ->searchable(),
                TextColumn::make('created_at')
                ->date()
                ->sortable(),
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
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
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
