<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Service extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $fillable = [
        'service_name',
        'category_id',
        'category_name',
        'description',
        'price',
        'duration_in_days',
        'availability_status',
        'service_avatar',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
