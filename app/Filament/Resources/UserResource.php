<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
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
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('')
                        ->schema([
                            FileUpload::make('avatar')
                            ->avatar()
                            ->columnSpanFull()
                            ->required()
                            ->maxSize(512),
                        ])->columnSpanFull(),
                Fieldset::make('Personal Information')
                        ->schema([
                            Select::make('role_id')
                            ->relationship(name: 'role', titleAttribute: 'name')
                            ->required()
                            ->columnSpanFull(),
                            TextInput::make('last_name')
                            ->columnSpanFull()
                            ->required()
                            ->maxLength(255),
                            TextInput::make('first_name')
                            ->columnSpanFull()
                            ->required()
                            ->maxLength(255),
                            TextInput::make('phone_number')
                            ->columnSpanFull()
                            ->required()
                            ->tel()
                            ->regex('/^(09)\\d{9}/')
                            ->maxLength(255),
                            DatePicker::make('date_of_birth')
                            ->columnSpanFull()
                            ->required(),
                            TextInput::make('address')
                            ->columnSpanFull(),
                            TextInput::make('email')
                             ->email()
                             ->required()
                            ->columnSpanFull(),
                            TextInput::make('password')
                            ->password()
                            ->required()
                            ->columnSpan(2)
                            ->dehydrateStateUsing(static fn (null|string $state): null|string =>
                                filled($state) ? Hash::make($state) : null,
                            )->required(static fn($livewire): bool =>
                                $livewire instanceof CreateUser,
                            )->dehydrated(static fn (null|string $state): bool =>
                                filled($state),
                            )->label(static fn ($livewire): string =>
                            ($livewire instanceof EditUser) ? 'New Password' : 'Password',
                        ),
                        ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                ->circular(),
                TextColumn::make('last_name')
                ->searchable()
                ->sortable(),
                TextColumn::make('first_name')
                ->searchable()
                ->sortable(),
                TextColumn::make('email')
                ->searchable()
                ->sortable(),
                TextColumn::make('role.name')
                ->badge()
                ->sortable()
                ->color(fn (string $state): string => match ($state) {
                    'Owner' => 'primary',
                    'Staff' => 'success',
                    'Customer' => 'gray',
                })
                ->searchable(),
                TextColumn::make('created_at')
                ->searchable()
                ->sortable()
                ->date(),
            ])
            ->filters([
                SelectFilter::make('role_id')
                ->label('Role')
                ->options([
                    '1' => 'Owner',
                    '2' => 'Staff',
                    '3' => 'Customer',
                ]),
            ])
            ->actions([
                Action::make('ban')
                ->color('info')
                ->icon('heroicon-m-lock-closed')
                ->visible(
                    function (Model $record) {
                        return  (!$record->is_banned);
                    }
                )
                ->hidden(
                    function (Model $record) {
                        return  ($record->role->name === 'Owner');
                    }
                )
                ->action(function(User $record){
                    $record->is_banned = true;
                    $record->save();

                    Notification::make()
                    ->title('User was banned successfully.')
                    ->success()
                    ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Ban user')
                ->modalDescription('Are you sure you\'d like to ban this user? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, ban user'),
                Action::make('unban')
                ->color('success')
                ->icon('heroicon-m-lock-open')
                ->visible(
                    function (Model $record) {
                        return  ($record->is_banned);
                    }
                )
                ->hidden(
                    function (Model $record) {
                        return  ($record->role->name === 'Owner');
                    }
                )
                ->action(function(User $record){
                    $record->is_banned = false;
                    $record->save();

                    Notification::make()
                    ->title('User was unbanned successfully.')
                    ->success()
                    ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Unban user')
                ->modalDescription('Are you sure you\'d like to unban this user? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, unban user'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}
