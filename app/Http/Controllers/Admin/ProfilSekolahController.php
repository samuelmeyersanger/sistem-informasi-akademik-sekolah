<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilSekolah;
use Illuminate\Http\Request;

class ProfilSekolahController extends Controller
{
    public function index()
    {
        $profil = ProfilSekolah::first();
        return view('admin.profil-sekolah.index', compact('profil'));
    }

    public function storeOrUpdate(Request $request)
    {
        $profil = ProfilSekolah::first();

        $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'jenjang'      => ['required', 'string', 'max:50'],
            'fase'         => ['nullable', 'string', 'max:10'],
            'npsn'         => ['required', 'string', 'max:50'],
            'nss'          => ['nullable', 'string', 'max:50'],
            'provinsi'     => ['required', 'string', 'max:100'],
            'kota'         => ['required', 'string', 'max:100'],
            'kecamatan'    => ['required', 'string', 'max:100'],
            'kelurahan'    => ['required', 'string', 'max:100'],
            'alamat'       => ['required', 'string'],
            'kode_pos'     => ['required', 'string', 'max:10'],
            'latitude'     => ['nullable', 'numeric'],
            'longitude'    => ['nullable', 'numeric'],
            'website'      => ['nullable', 'url', 'max:255'],
            'email'        => ['required', 'email', 'max:255'],
        ]);

        ProfilSekolah::updateOrCreate(
            ['id' => $profil ? $profil->id : null],
            [
                'nama_sekolah' => $request->nama_sekolah,
                'jenjang'      => $request->jenjang,
                'fase'         => $request->fase,
                'npsn'         => $request->npsn,
                'nss'          => $request->nss,
                'provinsi'     => $request->provinsi,
                'kota'         => $request->kota,
                'kecamatan'    => $request->kecamatan,
                'kelurahan'    => $request->kelurahan,
                'alamat'       => $request->alamat,
                'kode_pos'     => $request->kode_pos,
                'latitude'     => $request->latitude,
                'longitude'    => $request->longitude,
                'website'      => $request->website,
                'email'        => $request->email,
            ]
        );

        return redirect()->route('admin.profil-sekolah.index')
            ->with('success', 'Data identitas resmi dan koordinat peta berhasil diperbarui.');
    }
}