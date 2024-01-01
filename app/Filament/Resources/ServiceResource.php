<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Models\ServiceCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationLabel = 'Services';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['service_name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('')
                ->schema([
                    FileUpload::make('service_avatar')
                    ->required()
                    ->image()
                    ->columnSpan('full')
                    ->maxSize(1024),
                         ])->columnSpan(1),
                    Fieldset::make('')
                    ->schema([
                        TextInput::make('service_name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->columnSpan('full')
                        ->autocapitalize('words')
                        ->minLength(2)
                        ->maxLength(255),
                        Select::make('category_id')
                        ->label('Category')
                        ->options(ServiceCategory::all()->pluck('category_name', 'id'))
                        ->searchable()
                        ->columnSpan('full')
                        ->required()
                        ->live(),
                        TextArea::make('description')
                        ->required()
                        ->columnSpan('full')
                        ->minLength(2),
                        TextInput::make('duration_in_days')
                        ->required(fn (Get $get) => $get('category_id') === '1')
                        ->numeric()
                        ->columnSpan('full')
                        ->visible(fn (Get $get) => $get('category_id') === '1'),
                        TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->inputMode('decimal')
                        ->columnSpan('full')
                        ->prefix('₱'),
                        Select::make('availability_status')
                        ->options([
                            'Available' => 'Available',
                            'Not Available' => 'Not Available',
                        ])
                        ->required()
                        ->columnSpan('full'),
                        ])->columnSpan(1)
                     ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('service_avatar')
                ->label('')
                ->square(),
                TextColumn::make('service_name')
                ->searchable(),
                TextColumn::make('service_categories.category_name')
                ->badge()
                ->color('gray')
                ->icon('heroicon-m-tag'),
                TextColumn::make('price')
                ->money('PHP', True),
                TextColumn::make('availability_status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Available' => 'primary',
                    'Not Available' => 'warning',
                }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                ->relationship('service_categories', 'category_name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }    
}
