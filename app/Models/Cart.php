<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    public $fillable = [
        'service_id',
        'user_id',
        'price',
        'quantity',
        'sub_total',
        'mode_of_payment',
        'appointment_date',
        'payment_receipt',
        'time_slot_id',
        'design_type',
        'design_description',
        'design_file_path',
    ];

    /**
     * Get the service that owns the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
