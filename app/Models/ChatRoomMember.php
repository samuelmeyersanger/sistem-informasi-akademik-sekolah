<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoomMember extends Model
{
    protected $table = 'chat_room_members';
    
    // Tambahkan 'role_id' ke dalam fillable
    protected $fillable = ['chat_room_id', 'user_id', 'role_id', 'last_read_at', 'is_muted', 'is_left', 'left_at'];

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🟢 TAMBAHKAN RELASI DINAMIS INI: Berhubungan langsung dengan tabel roles Anda
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id'); // Sesuaikan nama model "Role" Anda jika berbeda
    }
}