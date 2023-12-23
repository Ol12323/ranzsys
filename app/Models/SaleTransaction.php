<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleTransaction extends Model
{
    use HasFactory;

    public $fillable = [
        'sales_name',
        'process_type',
        'customer_id',
        'customer_cash_change',
        'total_amount',
        'processed_by',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get all of the item for the SaleTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function item()
    {
        return $this->hasMany(SaleItem::class);
    }
}
