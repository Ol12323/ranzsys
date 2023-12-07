<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SaleItem extends Model
{
    use HasFactory;

    public $fillable = [
        'sale_transaction_id',
        'service_id',
        'service_name',
        'service_price',
        'quantity',
        'total_price',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function sale_transaction()
    {
        return $this->belongsTo(SaleTransaction::class);
    }
}
