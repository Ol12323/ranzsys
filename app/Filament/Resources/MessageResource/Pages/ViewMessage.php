<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
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


                $previousMessageContent = MessageContent::where('messages_id', $messageID)
                    ->latest()
                    ->first();

                if ($previousMessageContent) {
                    if ($previousMessageContent->sender_id != auth()->user()->id) {
                        $recipientId = $previousMessageContent->sender_id;
                    } else {
                        $recipientId = $previousMessageContent->recipient_id;
                    }

                    $messageContent = new MessageContent([
                        'messages_id' => $messageID,
                        'body' => $data['body'],
                        'sender_id' => auth()->user()->id,
                        'recipient_id' => $recipientId,
                        'image_path' => $data['image_path'],
                    ]);
                    $messageContent->save();
                }
                
                Notification::make()
                ->title('Reply sent successfully.')
                ->success()
                ->send();
    
            }),
        ];
    }
}
