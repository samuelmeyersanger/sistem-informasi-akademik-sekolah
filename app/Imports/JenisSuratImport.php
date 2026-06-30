<?php

namespace App\Imports;

use App\Models\JenisSurat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JenisSuratImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Jika kode klasifikasi kosong, lewati baris ini
        if (empty($row['kode_klasifikasi'])) {
            return null;
        }

        return new JenisSurat([
            'kode_klasifikasi' => trim($row['kode_klasifikasi']),
            'nama_jenis'       => $row['nama_jenis_surat'],
            'format_nomor'     => trim($row['format_susunan_nomor']),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.kode_klasifikasi'     => 'required|string|unique:jenis_surat,kode_klasifikasi',
            '*.nama_jenis_surat'     => 'required|string|max:255',
            '*.format_susunan_nomor' => 'required|string',
        ];
    }
    
    public function customValidationMessages()
    {
        return [
            '*.kode_klasifikasi.unique' => 'Kode Klasifikasi sudah terdaftar di sistem.',
            '*.nama_jenis_surat.required' => 'Nama jenis surat wajib diisi.',
        ];
    }
}