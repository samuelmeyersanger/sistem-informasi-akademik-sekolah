<?php

namespace App\Http\Controllers\Skm;

use App\Http\Controllers\Controller;
use App\Models\SkmLayanan;
use Illuminate\Http\Request;

class SkmLayananController extends Controller
{
    public function index()
    {
        $layanans = SkmLayanan::latest()->paginate(10);
        return view('skm.layanan.index', compact('layanans'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_layanan' => 'required|string|max:255']);
        SkmLayanan::create($request->all());
        return redirect()->route('skm.layanan.index')->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255', 
            'status' => 'required|boolean'
        ]);
        SkmLayanan::findOrFail($id)->update($request->all());
        return redirect()->route('skm.layanan.index')->with('success', 'Layanan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        SkmLayanan::findOrFail($id)->delete();
        return redirect()->route('skm.layanan.index')->with('success', 'Layanan berhasil dihapus!');
    }
}