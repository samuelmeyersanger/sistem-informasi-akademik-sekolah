<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use SoftDeletes;

    protected $table = 'chat_messages';
    protected $guarded = ['id']; // Mengizinkan fillable massal kecuali ID

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    // Relasi self-relation untuk fitur Reply Pesan
    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }
}