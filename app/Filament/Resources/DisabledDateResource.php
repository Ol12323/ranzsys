<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisabledDateResource\Pages;
use App\Filament\Resources\DisabledDateResource\RelationManagers;
use App\Models\DisabledDate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class DisabledDateResource extends Resource
{
    protected static ?string $model = DisabledDate::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('disabled_date')
                ->required()
                ->unique()
                ->columnSpan(2)
                ->native(false)
                ->minDate(now())
                ->closeOnDateSelection(),
                TextInput::make('reason')
                ->columnSpan('2')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('disabled_date')
                ->date(),
                TextColumn::make('reason')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDisabledDates::route('/'),
        ];
    }    
}
