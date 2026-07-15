@php
    $emailLogo = DB::table('pengaturan_logo')->first();
    $emailSchool = DB::table('profil_sekolah')->first();
    
    $namaSekolah = $emailSchool->nama_sekolah ?? 'SMP NEGERI 4 CIBITUNG';
    $imgPemda = $emailLogo->logo_pemda ?? 'logo_pemda.png';
    $imgSekolah = $emailLogo->logo_sekolah ?? 'logo_sekolah.png';
@endphp
<tr>
<td class="header" style="padding: 30px 0; text-align: center; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto; background-color: transparent;">
        <tr>
            <td style="padding: 0 12px; background-color: transparent; border: none; vertical-align: middle;">
                <img src="{{ config('app.url') }}/storage/{{ $imgPemda }}" style="height: 55px; width: auto; display: block; border: 0;" alt="Logo Pemda">
            </td>
            <td style="padding: 0 12px; background-color: transparent; border: none; vertical-align: middle;">
                <img src="{{ config('app.url') }}/storage/{{ $imgSekolah }}" style="height: 55px; width: auto; display: block; border: 0;" alt="Logo Sekolah">
            </td>
        </tr>
    </table>
    
    {{-- Tipografi Utama (Judul) --}}
    <div style="margin-top: 18px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-weight: 900; color: #0f172a; font-size: 16px; letter-spacing: 2px; text-transform: uppercase;">
        SIAS <span style="color: #4f46e5; margin: 0 4px;">&bull;</span> {{ $namaSekolah }}
    </div>
    
    {{-- Tipografi Sekunder (Sub-judul) --}}
    <div style="margin-top: 6px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-weight: 700; color: #64748b; font-size: 10px; letter-spacing: 3px; text-transform: uppercase;">
        Sistem Informasi Akademik Terpadu
    </div>
</a>
</td>
</tr>