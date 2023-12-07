<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $table = 'appointments';

    public $fillable = [
        'name',
        'customer_id',
        'appointment_date',
        'time_slot_id',
        'status',
        'total_amount',
        'payment_due',
        'mode_of_payment',
        'receipt_screenshot',
        'processed_by'
    ];

    public function time_slot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function item()
    {
        return $this->hasMany(AppointmentItem::class);
    }

    public function getSumOfItemValuesAttribute()
    {
        return $this->item->sum('unit_price');
    }

    /**
     * Get the customer that owns the Appointment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
