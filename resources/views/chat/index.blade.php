<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ruang Diskusi & Chat Sekolah') }}
        </h2>
    </x-slot>

    <div x-data="Object.assign(chatSystem({{ request('room_id', 'null') }}, {{ json_encode($rooms) }}), { openCreateGroup: false })" 
         x-init="init()" 
         class="py-6 bg-slate-900/10 min-h-[calc(100vh-64px)] flex flex-col justify-center">
        
        <div class="max-w-6xl w-full mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 flex h-[75vh]">
                
                <div class="w-80 border-r border-gray-100 flex flex-col shrink-0 bg-white">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center shrink-0">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Obrolan</h3>
                            <p class="text-[10px] text-gray-500">Internal Sekolah</p>
                        </div>
                        <div class="flex gap-1.5">
                            <button @click="openCreateGroup = true" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                                👥 +Grup
                            </button>
                            <button @click="openKontak = true" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                                ➕ Kontak
                            </button>
                        </div>
                    </div>

                    <div class="overflow-y-auto flex-1 divide-y divide-gray-50">
                        <template x-for="room in roomList" :key="room.id">
                            <a :href="'/chat?room_id=' + room.id" 
                               class="flex items-center gap-3 p-3 hover:bg-gray-50/80 transition-colors"
                               :class="activeRoomId == room.id ? 'bg-indigo-50/70 border-l-4 border-indigo-600' : ''">
                                
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold uppercase shrink-0"
                                     :class="room.tipe === 'Grup' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-600'"
                                     x-text="room.nama_room.substring(0,2)">
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-bold text-gray-900 truncate" x-text="room.nama_room"></h4>
                                        <span x-show="room.tipe === 'Grup'" class="text-[9px] px-1 bg-emerald-100 text-emerald-700 font-bold rounded">Grup</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 truncate mt-0.5">
                                        <span x-text="room.pesan_terakhir || 'Belum ada pesan'" :class="!room.pesan_terakhir ? 'italic text-gray-400' : ''"></span>
                                    </p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                <div class="flex-1 flex flex-col bg-gray-50/30">
                    
                    <div x-show="activeRoomId === null" class="m-auto text-center space-y-2 p-6 flex flex-col items-center justify-center h-full w-full">
                        <div class="text-4xl text-gray-300 mb-2">💬</div>
                        <h4 class="text-sm font-bold text-gray-700">Belum Ada Obrolan Terpilih</h4>
                        <p class="text-xs text-gray-400 max-w-xs">Silakan klik salah satu obrolan atau buat ruang diskusi baru.</p>
                    </div>

                    <div x-show="activeRoomId !== null" class="flex flex-col h-full w-full" style="display: none;">
                        <div class="p-4 bg-white border-b border-gray-100 flex items-center gap-3 shadow-sm shrink-0">
                            <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold uppercase" x-text="roomName.substring(0,2)"></div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900" x-text="roomName"></h4>
                                <span class="text-[9px] px-1.5 py-0.5 bg-gray-100 text-gray-600 font-medium rounded">ID Ruang: <span x-text="activeRoomId"></span></span>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/50" id="chat-box">
                            <template x-for="msg in messages" :key="msg.id">
                                <div :class="msg.pengirim_id == {{ auth()->id() }} ? 'text-right' : 'text-left'">
                                    <div :class="msg.pengirim_id == {{ auth()->id() }} ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none'"
                                         class="inline-block p-3 rounded-2xl max-w-md text-xs shadow-sm text-left">
                                        
                                        <span class="block font-bold text-[10px] mb-1 opacity-75" 
                                              :class="msg.pengirim_id == {{ auth()->id() }} ? 'text-indigo-200' : 'text-indigo-600'"
                                              x-text="msg.pengirim ? msg.pengirim.name : 'Sistem'"></span>
                                        
                                        <template x-if="msg.file_attachment && msg.tipe_pesan === 'Gambar'">
                                            <div class="mb-2 max-w-xs overflow-hidden rounded-lg border border-gray-100/20 shadow-sm bg-black/5">
                                                <img :src="'/storage/' + msg.file_attachment" class="w-full h-auto object-cover max-h-48 cursor-zoom-in" @click="window.open('/storage/' + msg.file_attachment, '_blank')">
                                            </div>
                                        </template>

                                        <template x-if="msg.file_attachment && msg.tipe_pesan !== 'Gambar'">
                                            <div class="mb-2 p-2 rounded-xl border flex items-center gap-2 bg-slate-900/5 backdrop-blur-sm"
                                                 :class="msg.pengirim_id == {{ auth()->id() }} ? 'border-white/20 bg-white/10' : 'border-gray-100 bg-gray-50'">
                                                <span class="text-xl" x-text="msg.tipe_pesan === 'Video' ? '🎬' : (msg.tipe_pesan === 'Audio' ? '🎵' : '📄')"></span>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[11px] font-bold truncate" :class="msg.pengirim_id == {{ auth()->id() }} ? 'text-white' : 'text-gray-900'" x-text="msg.nama_file"></p>
                                                    <span class="text-[9px] opacity-75 uppercase" x-text="msg.tipe_pesan + ' (' + (msg.ukuran_file ? Math.round(msg.ukuran_file/1024) + ' KB' : '') + ')'"></span>
                                                </div>
                                                <a :href="'/storage/' + msg.file_attachment" target="_blank" download class="p-1.5 bg-indigo-500 hover:bg-indigo-400 text-white rounded-lg text-[10px] font-bold shrink-0">
                                                    ⬇️ Unduh
                                                </a>
                                            </div>
                                        </template>

                                        <p x-show="msg.pesan" class="whitespace-pre-line leading-relaxed text-sm select-text" x-text="msg.pesan"></p>
                                        <span class="block text-[9px] mt-1 text-right opacity-60" x-text="formatTime(msg.created_at)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="attachedFile" class="p-2.5 bg-amber-50/80 border-t border-amber-100 flex items-center justify-between px-4 shrink-0" style="display: none;">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-sm">📎</span>
                                <span class="text-xs font-semibold text-amber-900 truncate" x-text="attachedFile ? attachedFile.name : ''"></span>
                            </div>
                            <button type="button" @click="clearFileAttachment()" class="text-amber-700 hover:text-amber-900 font-bold text-sm bg-amber-100 hover:bg-amber-200 px-2 py-0.5 rounded-md">&times; Batal</button>
                        </div>

                        <div class="p-3 bg-white border-t border-gray-100 shrink-0">
                            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                                <input type="file" id="file-input" @change="handleFileSelect($event)" class="hidden">
                                
                                <button type="button" @click="document.getElementById('file-input').click()" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors cursor-pointer" title="Lampirkan File">
                                    📎
                                </button>

                                <input type="text" x-model="inputMessage" placeholder="Ketik pesan atau lampirkan file berkas..." class="w-full text-xs rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm px-4 py-2.5">
                                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-colors cursor-pointer shrink-0">
                                    🚀 Kirim
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div x-show="openKontak" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openKontak = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Mulai Chat Baru</h3>
                    <button type="button" @click="openKontak = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <div class="overflow-y-auto max-h-60 divide-y divide-gray-100 pr-1">
                    @foreach($users as $user)
                        <a href="{{ route('chat.initiate', $user->id) }}" class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-xl transition-colors group">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-xs font-bold text-gray-500 uppercase">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <span class="text-xs font-semibold text-gray-700 group-hover:text-indigo-600">{{ $user->name }}</span>
                            </div>
                            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">Pilih</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div x-show="openCreateGroup" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreateGroup = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Buat Grup Diskusi</h3>
                    <button type="button" @click="openCreateGroup = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('chat.group.create') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Grup Chat *</label>
                        <input type="text" name="nama_grup" required placeholder="Contoh: Tim Kurikulum Merdeka" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Undang Anggota *</label>
                        <div class="overflow-y-auto max-h-40 divide-y divide-gray-50 border border-gray-100 rounded-lg p-2 bg-gray-50/50">
                            @foreach($users as $user)
                                <label class="flex items-center gap-2.5 py-1.5 cursor-pointer select-none">
                                    <input type="checkbox" name="anggota[]" value="{{ $user->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-700 font-medium">{{ $user->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreateGroup = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Buat Grup</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

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
                    let pesanAwal = 'Belum ada pesan';
                    if (room.messages && room.messages.length > 0) {
                        pesanAwal = room.messages[0].file_attachment 
                            ? `📁 [${room.messages[0].tipe_pesan}]` 
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

                            // UPDATE SISI KIRI: Jika ada pesan baru masuk dari polling, ubah teks pratinjau di sidebar kiri
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
                        ? `📁 [${lastMsg.tipe_pesan}]` 
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

                // Simpan teks sementara untuk update sidebar instan
                let temporaryText = this.inputMessage;
                let temporaryHasFile = this.attachedFile;

                this.inputMessage = '';
                this.clearFileAttachment();

                axios.post(`/chat/room/${this.activeRoomId}/send`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                .then(res => {
                    const savedMsg = res.data.data;
                    this.messages.push(savedMsg);
                    this.scrollToBottom();
                    
                    // UPDATE SISI KIRI INSTAN: Begitu sukses klik kirim, pratinjau kiri langsung berubah saat itu juga
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