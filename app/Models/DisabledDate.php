<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisabledDate extends Model
{
    use HasFactory;

    public $fillable = [
        'disabled_date',
        'reason',
    ];
}
