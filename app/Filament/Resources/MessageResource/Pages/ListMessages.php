<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Message;

class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $userId = auth()->id();
        $unreadMessageCount = Message::query()
            ->where('read', false)
            ->count();

        return [
            'inbox' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('recipient_id', 1 ))
                ->badge($unreadMessageCount >= 1 ? (string)$unreadMessageCount : null),

            'sent' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('sender_id', $userId))
                ->badge(Message::query()->where('sender_id', $userId)->count() >= 1 ? Message::query()->where('sender_id', $userId)->count() : false), // No badge for sent messages
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'inbox';
    }
}
