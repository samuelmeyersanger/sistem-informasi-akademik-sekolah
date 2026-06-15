<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }} - {{ $schoolProfile->nama_sekolah ?? 'SIAS' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-20 items-center">
                <a href="/" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 flex items-center gap-1">
                    &larr; Kembali ke Beranda
                </a>
                <span class="text-xs text-gray-400 font-medium">Informasi Sekolah</span>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 flex-grow w-full">
        <article class="bg-white rounded-2xl p-6 sm:p-10 border border-gray-100 shadow-sm space-y-6">
            <header class="pb-4 border-b border-gray-100">
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-gray-900 leading-tight">
                    {{ $page->title }}
                </h1>
                <p class="text-xs text-gray-400 mt-2">Terakhir diperbarui: {{ $page->updated_at->translatedFormat('d F Y') }}</p>
            </header>

            <div class="prose max-w-none text-gray-700 text-sm sm:text-base leading-relaxed space-y-4">
                {!! $page->content !!} 
            </div>
        </article>
    </main>

    @include('layouts.footer')

</body>
</html>