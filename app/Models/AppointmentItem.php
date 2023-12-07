<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentItem extends Model
{
    use HasFactory;

    public $fillable = [
        'appointment_id',
        'service_id',
        'item_name',
        'quantity',
        'unit_price',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
