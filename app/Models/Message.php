<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * Get all of the messageContent for the Message
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messageContent()
    {
        return $this->hasMany(MessageContent::class, 'messages_id')->orderBy('created_at', 'desc');
    }

    public $fillable = [
        'subject',
        'read',
    ];

    protected $casts = ['attached_file'=> 'array'];

}
