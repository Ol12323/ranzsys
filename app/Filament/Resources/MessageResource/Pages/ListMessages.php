<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Message;
use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use App\Models\MessageContent;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
            Actions\Action::make('New message')
            ->form([
                Select::make('recipient_id')
                    ->label('Send to')
                    ->options(User::where('id', '!=', auth()->user()->id)
                    ->get()
                    ->pluck('full_name', 'id'))
                    ->required()
                    ->columnSpan('full')
                    ->searchable(),
                TextInput::make('subject')
                ->required()
                ->maxLength(255),
                TextArea::make('body')
                ->required(),
                FileUpload::make('image_path')
                ->label('Upload image (Optional)')
                ->multiple()
                ->minSize(10)
                ->maxSize(1024),
            ])
            ->action(function (array $data): void {
                $message = new Message([
                    'subject' => $data['subject'],
                    'read' => false,
                ]);
                $message->save();

                $messageContent = new MessageContent([
                    'messages_id' => $message->id,
                    'body' => $data['body'],
                    'sender_id' => auth()->user()->id,
                    'recipient_id' => $data['recipient_id'],
                    'image_path' => $data['image_path'],
                ]);
                $messageContent->save();

                Notification::make()
                ->title('Message sent successfully.')
                ->success()
                ->send();
    
            }),
        ];
    }

}
