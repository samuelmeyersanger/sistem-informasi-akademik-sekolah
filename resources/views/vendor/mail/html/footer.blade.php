@php
    $emailSchool = DB::table('profil_sekolah')->first();
    $namaSekolah = $emailSchool->nama_sekolah ?? 'SMP NEGERI 4 CIBITUNG';
@endphp
<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
    <p style="font-size: 12px; color: #94a3b8; line-height: 1.5;">
        © {{ date('Y') }} SIAS (Sistem Informasi Aktivitas Sekolah).<br>
        <strong>{{ $namaSekolah }}</strong><br>
        <span style="font-size: 11px; color: #cbd5e1;">Seluruh hak cipta dilindungi undang-undang.</span><br>
        <strong>Created by SAMUEL MEYER SANGER, MTCRE</strong>
    </p>
</td>
</tr>
</table>
</td>
</tr>