<?php

namespace App\Http\Controllers\Skm;

use App\Http\Controllers\Controller;
use App\Models\SkmUnsur;
use Illuminate\Http\Request;

class SkmUnsurController extends Controller
{
    public function index()
    {
        $unsurs = SkmUnsur::orderBy('kode_unsur')->paginate(15);
        return view('skm.unsur.index', compact('unsurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_unsur' => 'required|string|max:10|unique:skm_unsur,kode_unsur',
            'pertanyaan' => 'required|string'
        ]);
        SkmUnsur::create($request->all());
        return redirect()->route('skm.unsur.index')->with('success', 'Unsur Pertanyaan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_unsur' => 'required|string|max:10|unique:skm_unsur,kode_unsur,'.$id,
            'pertanyaan' => 'required|string',
            'status' => 'required|boolean'
        ]);
        SkmUnsur::findOrFail($id)->update($request->all());
        return redirect()->route('skm.unsur.index')->with('success', 'Unsur Pertanyaan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        SkmUnsur::findOrFail($id)->delete();
        return redirect()->route('skm.unsur.index')->with('success', 'Unsur Pertanyaan berhasil dihapus!');
    }
}