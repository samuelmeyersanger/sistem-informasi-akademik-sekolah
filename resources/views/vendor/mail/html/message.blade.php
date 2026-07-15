<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
    {{-- Mengambil Logo Pemda dan Sekolah secara Dinamis (Tengah, Sejajar) --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto; background-color: transparent;">
        <tr>
            <td style="padding: 0 12px; background-color: transparent; border: none; vertical-align: middle;">
                <img src="{{ url('storage/logo-pemda.png') }}" style="height: 55px; width: auto; display: block; border: 0;" alt="Logo Pemda">
            </td>
            <td style="padding: 0 12px; background-color: transparent; border: none; vertical-align: middle;">
                <img src="{{ url('storage/logo-sekolah.png') }}" style="height: 55px; width: auto; display: block; border: 0;" alt="Logo Sekolah">
            </td>
        </tr>
    </table>
    
    {{-- Judul Header Mewah --}}
    <div style="margin-top: 18px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-weight: 900; color: #0f172a; font-size: 16px; letter-spacing: 2px; text-transform: uppercase;">
        SIAS <span style="color: #4f46e5; margin: 0 4px;">&bull;</span> SYSTEM
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
    {{-- Hak Cipta & Nama Instansi --}}
    <p style="margin: 0 0 12px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; color: #64748b; line-height: 1.6; text-align: center;">
        &copy; {{ date('Y') }} SIAS (Sistem Informasi Aktivitas Sekolah).<br>
        <span style="color: #0f172a; font-size: 13px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; display: inline-block; margin-top: 4px;">SMP NEGERI 4 CIBITUNG</span>
    </p>

    {{-- Keterangan Legalitas --}}
    <p style="margin: 0 0 20px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">
        Seluruh Hak Cipta Dilindungi Undang-Undang.
    </p>

    {{-- Garis Pemisah Kecil (Aksen) --}}
    <div style="height: 1px; width: 40px; background-color: #cbd5e1; margin: 0 auto 20px auto;"></div>

    {{-- Kredit Pengembang --}}
    <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 2px;">
        Developed by <br>
        <strong style="color: #4f46e5; font-weight: 800; font-size: 10px; display: inline-block; margin-top: 4px;">SAMUEL MEYER SANGER, MTCRE.</strong>
    </p>
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>