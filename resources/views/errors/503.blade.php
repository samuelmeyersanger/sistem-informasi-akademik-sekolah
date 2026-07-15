<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Pembaruan | ERP Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Animasi Latar Belakang Blob yang Menenangkan */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 8s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        /* Animasi Roda Gigi Presisi */
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 4s linear infinite;
        }
        .animate-spin-reverse {
            animation: spin-slow 5s linear infinite reverse;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased min-h-screen relative overflow-hidden flex items-center justify-center p-4 selection:bg-indigo-200 selection:text-indigo-900">

    {{-- Dekorasi Background Estetik (Warna Indigo & Biru Muda) --}}
    <div class="absolute top-10 left-10 w-96 h-96 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-10 right-10 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <div class="max-w-xl w-full relative z-10">
        {{-- Card Kaca Premium (Glassmorphism ringan) --}}
        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl shadow-indigo-900/10 sm:rounded-[2.5rem] border border-white p-12 text-center relative transform transition-all hover:-translate-y-1 hover:shadow-indigo-900/20">
            
            {{-- Aksen Garis Gradien di Atas --}}
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-indigo-500 via-blue-500 to-indigo-500"></div>

            {{-- Ilustrasi Animasi SVG Mekanik --}}
            <div class="mb-10 relative flex items-center justify-center h-32 w-32 mx-auto">
                <div class="absolute inset-0 bg-indigo-50 rounded-full border border-indigo-100 shadow-inner"></div>
                
                {{-- Roda Gigi Besar (Belakang) --}}
                <svg class="absolute w-20 h-20 text-indigo-200 animate-spin-slow" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.06-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.73,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.06,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.43-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.49-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                </svg>

                {{-- Roda Gigi Kecil (Depan) --}}
                <svg class="absolute w-12 h-12 text-indigo-600 animate-spin-reverse drop-shadow-md z-10 ml-12 mt-12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.06-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.73,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.06,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.43-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.49-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                </svg>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 text-xs font-bold uppercase tracking-widest mb-4">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                Sedang Maintenance
            </div>

            <h1 class="text-3xl sm:text-4xl font-black text-gray-900 mb-4 tracking-tight">Pembaruan Sistem</h1>
            
            <p class="text-gray-500 text-sm sm:text-base leading-relaxed max-w-sm mx-auto font-medium">
                Sistem ERP Sekolah saat ini sedang dinonaktifkan sementara untuk proses optimasi server dan pemeliharaan database rutin. 
                Kami akan segera kembali <em>online</em> dalam beberapa menit.
            </p>

            <div class="mt-10 pt-8 border-t border-gray-100">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">
                    Mohon tunggu sejenak dan *refresh* halaman ini nanti
                </p>
                <button onclick="window.location.reload()" class="mt-4 px-6 py-2.5 bg-white border-2 border-gray-200 hover:border-indigo-500 hover:text-indigo-600 text-gray-600 text-sm font-bold rounded-xl transition-colors shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 flex items-center justify-center gap-2 mx-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Refresh Sekarang
                </button>
            </div>
        </div>
    </div>

</body>
</html>