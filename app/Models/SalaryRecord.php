<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    protected $fillable = [
        'period_id', 'nik', 'nama', 'nik_ktp', 'department', 'jabatan',
        'tanggal_masuk_kontrak', 'tanggal_akhir_kontrak', 'no_rekening', 'nama_bank',
        'gaji_pokok', 'gaji_kurangi_potongan', 'rapel_gaji_lembur', 'kompensasi_pkwt',
        'lembur', 'total_pendapatan', 'bpjs_kesehatan_potongan', 'bpjs_tk_potongan',
        'pph21', 'pembuatan_rekening', 'meterai', 'pinjaman_pribadi', 'sumbangan',
        'total_potongan', 'total_ditransfer', 'tgl_lahir', 'potongan_absen',
        'masuk_kerja_hari', 'lama_lembur_jam', 'no_kpj', 'no_jkn',
        'jht_kary', 'jkm', 'jkk', 'pensiun_kary', 'tot_iuran_kary',
        'hadir', 'cuti', 'travel', 'sakit', 'ijin', 'alpa', 'off', 'total_hk', 'ot_jam'
    ];

    protected $casts = [
        'tanggal_masuk_kontrak' => 'date',
        'tanggal_akhir_kontrak' => 'date',
        'tgl_lahir' => 'date',
    ];

    public function period()
    {
        return $this->belongsTo(SalaryPeriod::class, 'period_id');
    }

    public function getLemburPerJamAttribute()
    {
        if ($this->gaji_pokok > 0) {
            return $this->gaji_pokok / 173.333333;
        }
        return 0;
    }
}
