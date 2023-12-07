<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Get all of the service for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service()
    {
        return $this->hasMany(OrderService::class);
    }

    /**
     * Get the user that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function time_slot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function getSumOfItemValuesAttribute()
    {
        return $this->service->sum('subtotal');
    }
    public $fillable = [
        'user_id',
        'order_name',
        'service_type',
        'status',
        'total_amount',
        'payment_due',
        'mode_of_payment',
        'receipt_screenshot',
        'service_date',
        'time_slot_id',
    ];
}
