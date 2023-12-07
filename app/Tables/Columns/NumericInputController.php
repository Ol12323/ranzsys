<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\CanBeValidated;
use Filament\Tables\Columns\Concerns\CanUpdateState;
use Filament\Tables\Columns\Contracts\Editable;

class NumericInputController extends Column implements Editable
{
    use CanBeValidated;
    use CanUpdateState;

    protected string $view = 'tables.columns.numeric-input-controller';
}
