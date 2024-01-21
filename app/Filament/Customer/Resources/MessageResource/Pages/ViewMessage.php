<?php

namespace App\Filament\Customer\Resources\MessageResource\Pages;

use App\Filament\Customer\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use App\Models\Message;
use App\Models\MessageContent;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class ViewMessage extends ViewRecord
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Reply')
            ->form([
                TextArea::make('body')
                ->required(),
                FileUpload::make('image_path')
                ->label('Upload image (Optional)')
                ->multiple()
                ->minSize(10)
                ->maxSize(1024),
            ])
            ->action(function (array $data): void {
                $subject = $this->record->subject;
                $messageID = $this->record->id;


                $messageContent = new MessageContent([
                    'messages_id' => $messageID,
                    'body' => $data['body'],
                    'sender_id' => auth()->user()->id,
                    'recipient_id' => 1,
                    'image_path' => $data['image_path'],
                ]);
                $messageContent->save();

                Notification::make()
                ->title('Reply sent successfully.')
                ->success()
                ->send();
    
            }),
        ];
    }
}
