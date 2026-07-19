<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berhasil | Survey Kepuasan Masyarakat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-50 to-emerald-50 min-h-screen flex flex-col items-center justify-center font-sans antialiased p-4">

    <div class="bg-white/80 backdrop-blur-xl p-10 md:p-16 rounded-[3rem] shadow-2xl shadow-emerald-500/10 border border-white/60 text-center max-w-xl w-full transform transition-all duration-500 hover:scale-105">
        
        <!-- Ikon Sukses -->
        <div class="mx-auto w-24 h-24 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mb-8 shadow-inner">
            <i class="fa-solid fa-heart text-5xl animate-bounce"></i>
        </div>

        <h1 class="text-3xl font-black text-slate-800 tracking-tight mb-4">Terima Kasih! 🎉</h1>
        <p class="text-slate-500 font-medium leading-relaxed mb-8">
            Penilaian dan masukan Anda telah berhasil kami rekam. Suara Anda sangat berharga bagi kami untuk terus meningkatkan kualitas pelayanan di masa yang akan datang.
        </p>

        <a href="{{ route('publik.survey.index') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-500/30 transition-all">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Awal
        </a>
    </div>

</body>
</html>