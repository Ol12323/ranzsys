<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageContent extends Model
{
    use HasFactory;

    protected $casts = ['image_path'=> 'array'];

    public $fillable = [
        'messages_id',
        'body',
        'sender_id',
        'recipient_id',
        'image_path',
    ];

    /**
     * Get the message that owns the MessageContent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the sender that owns the MessageContent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient that owns the MessageContent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
