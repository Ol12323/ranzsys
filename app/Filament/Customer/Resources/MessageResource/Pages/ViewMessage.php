<?php

namespace App\Filament\Customer\Resources\MessageResource\Pages;

use App\Filament\Customer\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use App\Models\Message;
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
                Textarea::make('content')
                ->required(),
            ])
            ->visible(function(Model $record){
                $true = $this->record->recipient_id === auth()->id();

                return $true;
            })
            ->action(function (array $data): void {
                $message = new Message([
                    'sender_id' => auth()->user()->id,
                    'recipient_id' => $this->record->sender_id,
                    'subject' => $this->record->subject,
                    'content' => $data['content'],
                    'read' => false,
                ]);
                $message->save();
    
                Notification::make()
                ->title('Reply sent successfully.')
                ->success()
                ->send();
    
            })
        ];
    }
}
