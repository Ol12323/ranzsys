<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public $fillable = [
        'sender_id',
        'recipient_id',
        'subject',
        'content',
        'attached_file',
        'read',
    ];

    protected $casts = ['attached_file'=> 'array'];

}
