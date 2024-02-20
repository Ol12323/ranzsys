<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    use HasFactory;

    /**
     * Get the order that owns the OrderService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the service that owns the OrderService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    protected $casts = ['design_file_path'=> 'array'];

    public $fillable = [
        'order_id',
        'service_id',
        'price',
        'quantity',
        'subtotal',
        'design_type',
        'design_description',
        'design_file_path',
    ];
}
