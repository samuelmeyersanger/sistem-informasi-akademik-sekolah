@php
    $emailLogo = DB::table('pengaturan_logo')->first();
    $emailSchool = DB::table('profil_sekolah')->first();
    
    $namaSekolah = $emailSchool->nama_sekolah ?? 'SMP NEGERI 4 CIBITUNG';
    $imgPemda = $emailLogo->logo_pemda ?? 'logo_pemda.png';
    $imgSekolah = $emailLogo->logo_sekolah ?? 'logo_sekolah.png';
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto; background-color: transparent;">
        <tr>
            <td style="padding: 0 8px; background-color: transparent; border: none; vertical-align: middle;">
                <img src="{{ config('app.url') }}/storage/{{ $imgPemda }}" style="height: 45px; width: auto; display: block;" alt="Logo Pemda">
            </td>
            <td style="padding: 0 8px; background-color: transparent; border: none; vertical-align: middle;">
                <img src="{{ config('app.url') }}/storage/{{ $imgSekolah }}" style="height: 45px; width: auto; display: block;" alt="Logo Sekolah">
            </td>
        </tr>
    </table>
    <div style="margin-top: 10px; font-weight: 800; color: #1e293b; font-size: 14px; letter-spacing: 0.5px; text-transform: uppercase;">
        SIAS - {{ $namaSekolah }}
    </div>
</a>
</td>
</tr>