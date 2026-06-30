<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatRoom extends Model
{
    use SoftDeletes;

    protected $table = 'chat_room';
    protected $fillable = ['nama_room', 'tipe', 'pembuat_id', 'deskripsi', 'avatar', 'is_aktif'];

    // Relasi mendapatkan seluruh pesan di room ini
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_room_id');
    }

    // Relasi mendapatkan data anggota room ini
    public function members()
    {
        return $this->hasMany(ChatRoomMember::class, 'chat_room_id');
    }

    // Relasi ke pembuat grup (Pegawai)
    public function pembuat()
    {
        return $this->belongsTo(Pegawai::class, 'pembuat_id');
    }
}