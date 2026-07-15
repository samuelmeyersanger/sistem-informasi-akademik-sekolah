@php
    $emailSchool = DB::table('profil_sekolah')->first();
    $namaSekolah = $emailSchool->nama_sekolah ?? 'SMP NEGERI 4 CIBITUNG';
@endphp
<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; width: 100%; margin: 0 auto;">
<tr>
<td class="content-cell" align="center" style="padding: 35px 20px;">
    
    {{-- Hak Cipta & Nama Instansi --}}
    <p style="margin: 0 0 12px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 11px; color: #64748b; line-height: 1.6; text-align: center;">
        &copy; {{ date('Y') }} SIAS (Sistem Informasi Aktivitas Sekolah).<br>
        <span style="color: #0f172a; font-size: 13px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; display: inline-block; margin-top: 4px;">{{ $namaSekolah }}</span>
    </p>

    {{-- Keterangan Legalitas --}}
    <p style="margin: 0 0 20px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px;">
        Seluruh Hak Cipta Dilindungi Undang-Undang.
    </p>

    {{-- Garis Pemisah Kecil --}}
    <div style="height: 1px; width: 40px; background-color: #cbd5e1; margin: 0 auto 20px auto;"></div>

    {{-- Kredit Pengembang --}}
    <p style="margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 2px;">
        Developed by <br>
        <strong style="color: #4f46e5; font-weight: 800; font-size: 10px; display: inline-block; margin-top: 4px;">SAMUEL MEYER SANGER, MTCRE.</strong>
    </p>

</td>
</tr>
</table>
</td>
</tr>