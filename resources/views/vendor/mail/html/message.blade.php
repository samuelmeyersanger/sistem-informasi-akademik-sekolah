<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
    {{-- Mengambil Logo Pemda dan Sekolah secara Dinamis --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto; background-color: transparent;">
        <tr>
            <td style="padding: 0 8px; background-color: transparent; border: none;">
                <img src="{{ url('storage/logo-pemda.png') }}" style="height: 45px; width: auto; object-contain: contain;" alt="Logo Pemda">
            </td>
            <td style="padding: 0 8px; background-color: transparent; border: none;">
                <img src="{{ url('storage/logo-sekolah.png') }}" style="height: 45px; width: auto; object-contain: contain;" alt="Logo Sekolah">
            </td>
        </tr>
    </table>
    <div style="margin-top: 8px; font-weight: 800; color: #1e293b; font-size: 16px; tracking-content: tight;">
        SIAS - SYSTEM
    </div>
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy (Bagian Link Alternatif di Bawah Jika Tombol Error) --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} SIAS (Sistem Informasi Aktivitas Sekolah).<br>
<strong>SMP NEGERI 4 CIBITUNG</strong><br>
<strong>Created by SAMUEL MEYER SANGER, MTCRE.</strong><br>
<span style="font-size: 11px; color: #94a3b8;">Seluruh hak cipta dilindungi undang-undang.</span>
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>