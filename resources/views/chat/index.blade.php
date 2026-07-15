<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">💬</span> {{ __('Ruang Diskusi & Chat') }}
        </h2>
    </x-slot>

    <div x-data="Object.assign(chatSystem({{ request('room_id', 'null') }}, {{ json_encode($rooms) }}), { openCreateGroup: false })" 
         x-init="init()" 
         class="py-8 bg-slate-50 min-h-[calc(100vh-64px)] flex flex-col justify-center">
        
        <div class="max-w-7xl w-full mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 flex h-[78vh] relative">
                
                {{-- SIDEBAR KIRI: Daftar Ruang Chat --}}
                <div class="w-80 border-r border-gray-100 flex flex-col shrink-0 bg-white z-10 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
                    <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center shrink-0">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 leading-tight">Pesan</h3>
                            <p class="text-xs font-semibold text-gray-500 mt-0.5">Internal Sekolah</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="openKontak = true" class="w-9 h-9 flex items-center justify-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl font-bold transition-colors shadow-sm" title="Mulai Chat Baru">
                                ➕
                            </button>
                            <button @click="openCreateGroup = true" class="w-9 h-9 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-xl font-bold transition-colors shadow-sm" title="Buat Grup Baru">
                                👥
                            </button>
                        </div>
                    </div>

                    <div class="overflow-y-auto flex-1 divide-y divide-gray-50/80 custom-scrollbar">
                        <template x-for="room in roomList" :key="room.id">
                            <a :href="'/chat?room_id=' + room.id" 
                               class="flex items-center gap-4 p-4 hover:bg-indigo-50/50 transition-colors cursor-pointer relative overflow-hidden group"
                               :class="activeRoomId == room.id ? 'bg-indigo-50/80' : ''">
                                
                                <!-- Indikator Aktif (Garis Kiri) -->
                                <div x-show="activeRoomId == room.id" class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600 shadow-[2px_0_8px_rgba(79,70,229,0.5)]"></div>
                                
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-lg font-black uppercase shrink-0 shadow-sm border border-transparent group-hover:border-indigo-100 transition-colors"
                                     :class="room.tipe === 'Grup' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'"
                                     x-text="room.nama_room.substring(0,2)">
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <h4 class="text-sm font-bold text-gray-900 truncate" x-text="room.nama_room"></h4>
                                        <span x-show="room.tipe === 'Grup'" class="text-[9px] px-1.5 py-0.5 bg-emerald-50 text-emerald-600 font-bold uppercase tracking-wider rounded border border-emerald-100">Grup</span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate" :class="!room.pesan_terakhir ? 'italic text-gray-400 font-medium' : ''">
                                        <span x-text="room.pesan_terakhir || 'Belum ada pesan...'"></span>
                                    </p>
                                </div>
                            </a>
                        </template>
                        
                        <div x-show="roomList.length === 0" class="p-8 text-center text-gray-400">
                            <span class="text-4xl block mb-2 opacity-50">📭</span>
                            <p class="text-sm font-bold">Belum ada obrolan.</p>
                        </div>
                    </div>
                </div>

                {{-- AREA KANAN: Ruang Obrolan Aktif --}}
                <div class="flex-1 flex flex-col bg-[#F8FAFC] relative">
                    
                    {{-- State: Belum ada room terpilih --}}
                    <div x-show="activeRoomId === null" class="m-auto text-center space-y-4 p-8 flex flex-col items-center justify-center h-full w-full">
                        <div class="w-24 h-24 bg-white rounded-full shadow-sm border border-gray-100 flex items-center justify-center mb-2">
                            <span class="text-5xl">💬</span>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-gray-800">Mulai Percakapan</h4>
                            <p class="text-sm font-medium text-gray-500 mt-1 max-w-xs mx-auto">Pilih obrolan dari panel di sebelah kiri atau mulai diskusi baru dengan kolega Anda.</p>
                        </div>
                    </div>

                    {{-- State: Ada room terpilih --}}
                    <div x-show="activeRoomId !== null" class="flex flex-col h-full w-full" style="display: none;">
                        
                        {{-- Header Chat --}}
                        <div class="px-6 py-4 bg-white border-b border-gray-100 flex justify-between items-center shadow-sm shrink-0 z-10">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-sm font-black uppercase shadow-md shadow-indigo-200" x-text="roomName.substring(0,2)"></div>
                                <div>
                                    <h4 class="text-base font-black text-gray-900 leading-tight" x-text="roomName"></h4>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
                                        <span class="text-xs font-semibold text-emerald-600">Terhubung</span>
                                    </div>
                                </div>
                            </div>
                            <div class="px-3 py-1 bg-gray-50 border border-gray-200 rounded-lg text-xs font-bold text-gray-500 tracking-wider">
                                ID: <span x-text="activeRoomId"></span>
                            </div>
                        </div>

                        {{-- Kotak Pesan (Chat Box) --}}
                        <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-fixed" id="chat-box" style="background-color: #F8FAFC;">
                            <template x-for="msg in messages" :key="msg.id">
                                <div class="flex flex-col w-full" :class="msg.pengirim_id == {{ auth()->id() }} ? 'items-end' : 'items-start'">
                                    
                                    {{-- Nama Pengirim (Muncul jika bukan pesan dari diri sendiri) --}}
                                    <span x-show="msg.pengirim_id != {{ auth()->id() }}" class="text-[11px] font-bold text-gray-500 mb-1 ml-2" x-text="msg.pengirim ? msg.pengirim.name : 'Sistem'"></span>
                                    
                                    <div :class="msg.pengirim_id == {{ auth()->id() }} ? 'bg-indigo-600 text-white rounded-2xl rounded-br-sm shadow-md shadow-indigo-200' : 'bg-white text-gray-800 rounded-2xl rounded-bl-sm shadow-sm border border-gray-100'"
                                         class="relative p-3.5 max-w-[75%] sm:max-w-md text-sm">
                                        
                                        {{-- Lampiran Gambar --}}
                                        <template x-if="msg.file_attachment && msg.tipe_pesan === 'Gambar'">
                                            <div class="mb-2 overflow-hidden rounded-xl bg-black/5">
                                                <img :src="'/storage/' + msg.file_attachment" class="w-full h-auto object-cover max-h-64 cursor-zoom-in hover:opacity-90 transition-opacity" @click="window.open('/storage/' + msg.file_attachment, '_blank')">
                                            </div>
                                        </template>

                                        {{-- Lampiran Dokumen/Video/Audio --}}
                                        <template x-if="msg.file_attachment && msg.tipe_pesan !== 'Gambar'">
                                            <div class="mb-2 p-3 rounded-xl border flex items-center gap-3 backdrop-blur-sm"
                                                 :class="msg.pengirim_id == {{ auth()->id() }} ? 'border-white/20 bg-white/10' : 'border-gray-100 bg-gray-50'">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0" :class="msg.pengirim_id == {{ auth()->id() }} ? 'bg-indigo-500 text-white' : 'bg-white shadow-sm border border-gray-100'">
                                                    <span x-text="msg.tipe_pesan === 'Video' ? '🎬' : (msg.tipe_pesan === 'Audio' ? '🎵' : '📄')"></span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold truncate" :class="msg.pengirim_id == {{ auth()->id() }} ? 'text-white' : 'text-gray-900'" x-text="msg.nama_file"></p>
                                                    <span class="text-[10px] font-semibold opacity-75 uppercase mt-0.5 block" x-text="msg.tipe_pesan + ' (' + (msg.ukuran_file ? Math.round(msg.ukuran_file/1024) + ' KB' : '') + ')'"></span>
                                                </div>
                                                <a :href="'/storage/' + msg.file_attachment" target="_blank" download class="w-8 h-8 flex items-center justify-center bg-indigo-500 hover:bg-indigo-400 text-white rounded-lg shadow-sm shrink-0 transition-colors" title="Unduh File">
                                                    ⬇️
                                                </a>
                                            </div>
                                        </template>

                                        {{-- Teks Pesan --}}
                                        <p x-show="msg.pesan" class="whitespace-pre-line leading-relaxed font-medium" x-text="msg.pesan"></p>
                                        
                                        {{-- Waktu Pesan --}}
                                        <span class="block text-[10px] mt-1.5 font-semibold opacity-60" 
                                              :class="msg.pengirim_id == {{ auth()->id() }} ? 'text-right text-indigo-100' : 'text-right text-gray-400'"
                                              x-text="formatTime(msg.created_at)"></span>
                                    </div>
                                </div>
                            </template>
                            <!-- Spacer for bottom padding -->
                            <div class="h-2"></div>
                        </div>

                        {{-- Preview File Akan Dikirim --}}
                        <div x-show="attachedFile" class="p-3 bg-amber-50/90 backdrop-blur border-t border-amber-100 flex items-center justify-between px-6 shrink-0 shadow-[0_-4px_10px_rgba(0,0,0,0.02)] z-10" style="display: none;" x-transition>
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-sm border border-amber-200">📎</div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-amber-900 truncate" x-text="attachedFile ? attachedFile.name : ''"></p>
                                    <p class="text-[10px] text-amber-700 font-semibold uppercase">File Siap Dikirim</p>
                                </div>
                            </div>
                            <button type="button" @click="clearFileAttachment()" class="text-amber-800 hover:text-white font-bold text-xs bg-amber-200 hover:bg-rose-500 px-3 py-1.5 rounded-lg transition-colors shadow-sm">&times; Batal</button>
                        </div>

                        {{-- Input Form Area --}}
                        <div class="p-4 bg-white border-t border-gray-100 shrink-0 z-10">
                            <form @submit.prevent="sendMessage()" class="flex items-center gap-3">
                                <input type="file" id="file-input" @change="handleFileSelect($event)" class="hidden">
                                
                                <button type="button" @click="document.getElementById('file-input').click()" class="w-12 h-12 flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-2xl transition-colors cursor-pointer border border-gray-200 shadow-sm shrink-0" title="Lampirkan File">
                                    <span class="text-xl">📎</span>
                                </button>

                                <input type="text" x-model="inputMessage" placeholder="Ketik pesan Anda di sini..." class="w-full text-sm font-medium rounded-2xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner px-5 py-3.5 transition-colors">
                                
                                <button type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-2xl shadow-md shadow-indigo-200 transition-colors cursor-pointer shrink-0 flex items-center gap-2">
                                    Kirim <span class="text-lg">🚀</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MODAL: MULAI CHAT BARU (KONTAK) ================= --}}
        <div x-show="openKontak" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl p-6 overflow-hidden flex flex-col max-h-[80vh]" @click.away="openKontak = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4 shrink-0">
                    <h3 class="text-base font-black text-gray-900 flex items-center gap-2"><span>👤</span> Kontak Personal</h3>
                    <button type="button" @click="openKontak = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-rose-100 text-gray-500 hover:text-rose-600 font-bold transition-colors cursor-pointer">&times;</button>
                </div>
                
                <div class="overflow-y-auto flex-1 custom-scrollbar pr-2 space-y-1">
                    @forelse($users as $user)
                        <a href="{{ route('chat.initiate', $user->id) }}" class="flex items-center justify-between p-3 hover:bg-indigo-50 rounded-2xl border border-transparent hover:border-indigo-100 transition-colors group cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 group-hover:bg-indigo-100 rounded-xl flex items-center justify-center text-sm font-black text-gray-500 group-hover:text-indigo-600 uppercase transition-colors">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <span class="text-sm font-bold text-gray-700 group-hover:text-indigo-800 transition-colors">{{ $user->name }}</span>
                            </div>
                            <span class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-xs font-bold text-gray-400 group-hover:bg-indigo-600 group-hover:border-indigo-600 group-hover:text-white transition-all shadow-sm">
                                💬
                            </span>
                        </a>
                    @empty
                        <div class="text-center p-6 text-gray-400">
                            <span class="text-3xl block mb-2">📭</span>
                            <p class="text-sm font-bold">Tidak ada kontak tersedia.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ================= MODAL: BUAT GRUP DISKUSI ================= --}}
        <div x-show="openCreateGroup" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl p-8 overflow-hidden" @click.away="openCreateGroup = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-5 mb-5">
                    <h3 class="text-lg font-black text-gray-900 flex items-center gap-2"><span>👥</span> Buat Grup Diskusi</h3>
                    <button type="button" @click="openCreateGroup = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-rose-100 text-gray-500 hover:text-rose-600 font-bold transition-colors cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('chat.group.create') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Grup Chat <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_grup" required placeholder="Contoh: Tim Kurikulum Merdeka" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Undang Anggota <span class="text-rose-500">*</span></label>
                        <div class="overflow-y-auto max-h-48 border border-gray-200 rounded-xl p-2 bg-gray-50 custom-scrollbar space-y-1">
                            @foreach($users as $user)
                                <label class="flex items-center gap-3 p-2 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-gray-200 hover:shadow-sm">
                                    <input type="checkbox" name="anggota[]" value="{{ $user->id }}" class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 bg-white">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-[10px] font-bold text-gray-600 uppercase">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <span class="text-sm text-gray-800 font-bold">{{ $user->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="pt-2 flex justify-end gap-3">
                        <button type="button" @click="openCreateGroup = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-md transition-colors cursor-pointer">➕ Buat Grup</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <style>
        /* CSS Custom Scrollbar untuk memberikan feel premium */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>

    <script>
    function chatSystem(initialRoomId, serverRooms) {
        return {
            activeRoomId: initialRoomId,
            openKontak: false,
            inputMessage: '',
            messages: [],
            roomName: '',
            pollingInterval: null,
            attachedFile: null,

            // Simpan daftar room ke state lokal agar reaktif
            roomList: [],

            init() {
                // Map data awal dari Laravel ke format reaktif Alpine
                this.roomList = serverRooms.map(room => {
                    let pesanAwal = 'Belum ada pesan...';
                    if (room.messages && room.messages.length > 0) {
                        pesanAwal = room.messages[0].file_attachment 
                            ? `📁 [${room.messages[0].tipe_pesan}] File Terlampir` 
                            : room.messages[0].pesan;
                    }
                    return {
                        id: room.id,
                        nama_room: room.nama_room,
                        tipe: room.tipe,
                        pesan_terakhir: pesanAwal
                    };
                });

                if (this.activeRoomId) {
                    this.setRoomName();
                    this.startPolling();
                }
            },

            setRoomName() {
                const currentRoom = this.roomList.find(r => r.id == this.activeRoomId);
                if (currentRoom) {
                    this.roomName = currentRoom.nama_room;
                }
            },

            startPolling() {
                this.fetchMessages();
                this.pollingInterval = setInterval(() => {
                    this.fetchMessages();
                }, 3000);
            },

            fetchMessages() {
                if (!this.activeRoomId) return;

                axios.get(`/chat/room/${this.activeRoomId}/messages`)
                    .then(res => {
                        if (res.data.length !== this.messages.length) {
                            this.messages = res.data;
                            this.scrollToBottom();

                            // UPDATE SISI KIRI
                            if (res.data.length > 0) {
                                const lastMsg = res.data[res.data.length - 1];
                                this.updateLeftSidebar(this.activeRoomId, lastMsg);
                            }
                        }
                    })
                    .catch(err => {
                        console.error("Gagal sinkronisasi pesan:", err);
                    });
            },

            updateLeftSidebar(roomId, lastMsg) {
                const targetRoom = this.roomList.find(r => r.id == roomId);
                if (targetRoom) {
                    targetRoom.pesan_terakhir = lastMsg.file_attachment 
                        ? `📁 [${lastMsg.tipe_pesan}] File Terlampir` 
                        : lastMsg.pesan;
                }
            },

            sendMessage() {
                if (this.inputMessage.trim() === '' && !this.attachedFile) return;

                let formData = new FormData();
                formData.append('pesan', this.inputMessage);
                if (this.attachedFile) {
                    formData.append('file', this.attachedFile);
                }

                this.inputMessage = '';
                this.clearFileAttachment();

                axios.post(`/chat/room/${this.activeRoomId}/send`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                .then(res => {
                    const savedMsg = res.data.data;
                    this.messages.push(savedMsg);
                    this.scrollToBottom();
                    
                    // UPDATE SISI KIRI INSTAN
                    this.updateLeftSidebar(this.activeRoomId, savedMsg);
                })
                .catch(err => {
                    alert("Gagal mengirimkan pesan.");
                });
            },

            handleFileSelect(event) {
                const files = event.target.files;
                if (files.length > 0) {
                    this.attachedFile = files[0];
                }
            },

            clearFileAttachment() {
                this.attachedFile = null;
                document.getElementById('file-input').value = '';
            },

            scrollToBottom() {
                setTimeout(() => {
                    const box = document.getElementById('chat-box');
                    if (box) box.scrollTop = box.scrollHeight;
                }, 60);
            },

            formatTime(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
        }
    }
    </script>
</x-app-layout>