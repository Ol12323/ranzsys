<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    public $fillable = [
        'start_time',
        'end_time',
        'period',
    ]; 

    public function getTimeSlotAttribute()
    {
    return $this->start_time.'-'.$this->end_time. ' ' .$this->period;
    }
}
