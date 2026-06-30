<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatRoomMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * 1. Menampilkan halaman dashboard chat & daftar room
     */
    public function index()
    {
        $userId = auth()->id();

        // Ambil semua room tempat user ini terdaftar sebagai anggota aktif
        $rooms = ChatRoom::whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('is_left', false);
        })
        ->with(['messages' => function ($query) {
            // Ambil pesan terakhir untuk preview di sidebar chat
            $query->orderBy('created_at', 'desc')->first();
        }, 'members.user'])
        ->get()
        ->map(function ($room) use ($userId) {
            // Jika tipenya Pribadi, ubah nama_room menjadi nama lawan bicaranya
            if ($room->tipe === 'Pribadi') {
                $lawanBicara = $room->members->first(function ($member) use ($userId) {
                    return $member->user_id !== $userId;
                });
                $room->nama_room = $lawanBicara ? $lawanBicara->user->name : 'Pengguna Tidak Dikenal';
            }
            return $room;
        });

        // Ambil daftar user lain (misal untuk memulai chat baru)
        $users = User::where('id', '!=', $userId)->get();

        return view('chat.index', compact('rooms', 'users'));
    }

    /**
     * 2. Mengambil semua pesan dari suatu room (Dipanggil berkala oleh Polling Alpine.js)
     */
    public function getMessages($roomId)
    {
        $userId = auth()->id();

        // Proteksi keamanan: pastikan user yang me-request memang anggota room tersebut
        $isMember = ChatRoomMember::where('chat_room_id', $roomId)
            ->where('user_id', $userId)
            ->where('is_left', false)
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ambil histori pesan beserta data pengirimnya
        $messages = ChatMessage::with('pengirim')
            ->where('chat_room_id', $roomId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Update last_read_at sebagai penanda user sudah membaca chat di room ini
        ChatRoomMember::where('chat_room_id', $roomId)
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $roomId)
    {
        $request->validate([
            'pesan' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // Maksimal 10MB
        ]);

        if (!$request->pesan && !$request->hasFile('file')) {
            return response()->json(['error' => 'Pesan atau file tidak boleh kosong.'], 422);
        }

        $fileAttachment = null;
        $namaFile = null;
        $ukuranFile = null;
        $tipePesan = 'Teks';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $namaFile = $file->getClientOriginalName();
            $ukuranFile = $file->getSize(); // Mengambil ukuran dalam bytes sesuai kolom Anda
            $extension = strtolower($file->getClientOriginalExtension());
            
            // Sesuaikan dengan ENUM Anda: ['Teks', 'Gambar', 'File', 'Video', 'Audio']
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $tipePesan = 'Gambar';
            } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'mkv'])) {
                $tipePesan = 'Video';
            } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a'])) {
                $tipePesan = 'Audio';
            } else {
                $tipePesan = 'File'; // Untuk PDF, Docx, Zip, dll.
            }

            // Simpan ke storage public
            $fileAttachment = $file->store('chat_attachments', 'public');
        }

        $message = ChatMessage::create([
            'chat_room_id'    => $roomId,
            'pengirim_id'     => auth()->id(),
            'pesan'           => $request->pesan ?? '',
            'file_attachment' => $fileAttachment,
            'nama_file'       => $namaFile,
            'ukuran_file'     => $ukuranFile,
            'tipe_pesan'      => $tipePesan,
        ]);

        $message->load('pengirim');

        return response()->json([
            'success' => true,
            'data' => $message
        ]);
    }

    /**
     * 4. Membuat atau Menginisialisasi Chat Pembuka Baru (Dinamis Role)
     */
    public function initiatePrivateChat($targetUserId)
    {
        $myId = auth()->id();

        // 1. Cek dulu apakah room pribadi sudah ada
        $existingRoom = ChatRoom::where('tipe', 'Pribadi')
            ->whereHas('members', function ($q) use ($myId) {
                $q->where('user_id', $myId);
            })
            ->whereHas('members', function ($q) use ($targetUserId) {
                $q->where('user_id', $targetUserId);
            })
            ->first();

        if ($existingRoom) {
            return redirect()->route('chat.index', ['room_id' => $existingRoom->id]);
        }

        // 2. Jika belum ada, ambil data role asli mereka dari tabel user_role Anda
        // Kita ambil role pertama (atau utama) yang dimiliki masing-masing user
        $myRole = DB::table('user_role')->where('user_id', $myId)->first();
        $targetRole = DB::table('user_role')->where('user_id', $targetUserId)->first();

        // Jalankan transaction penyimpanan
        DB::transaction(function () use ($myId, $targetUserId, $myRole, $targetRole, &$newRoom) {
            // Buat Room Baru
            $newRoom = ChatRoom::create([
                'tipe'     => 'Pribadi',
                'is_aktif' => true,
            ]);

            // Daftarkan SAYA ke chat_room_members lengkap dengan role_id asli saya
            ChatRoomMember::create([
                'chat_room_id' => $newRoom->id,
                'user_id'      => $myId,
                'role_id'      => $myRole ? $myRole->role_id : null, // Menggunakan role_id dinamis Anda
            ]);

            // Daftarkan LAWAN CHAT ke chat_room_members lengkap dengan role_id aslinya
            ChatRoomMember::create([
                'chat_room_id' => $newRoom->id,
                'user_id'      => $targetUserId,
                'role_id'      => $targetRole ? $targetRole->role_id : null, // Menggunakan role_id dinamis Anda
            ]);
        });

        return redirect()->route('chat.index', ['room_id' => $newRoom->id]);
    }

    /**
     * 5. Membuat Grup Chat Baru Dinamis dengan Anggota Terpilih
     */
    public function createGroupChat(Request $request)
    {
        $request->validate([
            'nama_grup' => 'required|string|max:255',
            'anggota'   => 'required|array|min:1', // Minimal mengundang 1 anggota lain
        ]);

        $myId = auth()->id();
        
        // Ambil role_id saya dari tabel user_role Anda
        $myRole = DB::table('user_role')->where('user_id', $myId)->first();

        // Bungkus dengan database transaction agar aman
        $newRoom = DB::transaction(function () use ($request, $myId, $myRole) {
            
            // 1. Buat Room Chat bertipe Grup
            $room = ChatRoom::create([
                'nama_room' => $request->nama_grup,
                'tipe'      => 'Grup',
                'is_aktif'  => true,
            ]);

            // 2. Masukkan SAYA sebagai pembuat grup (Admin)
            ChatRoomMember::create([
                'chat_room_id' => $room->id,
                'user_id'      => $myId,
                'role_id'      => $myRole ? $myRole->role_id : null,
            ]);

            // 3. Masukkan Anggota Lain yang dipilih ke grup ini secara dinamis beserta role mereka
            foreach ($request->anggota as $userId) {
                $userRole = DB::table('user_role')->where('user_id', $userId)->first();
                
                ChatRoomMember::create([
                    'chat_room_id' => $room->id,
                    'user_id'      => $userId,
                    'role_id'      => $userRole ? $userRole->role_id : null,
                ]);
            }

            return $room;
        });

        return redirect()->route('chat.index', ['room_id' => $newRoom->id])
                         ->with('success', 'Grup ' . $newRoom->nama_room . ' berhasil dibuat!');
    }
}