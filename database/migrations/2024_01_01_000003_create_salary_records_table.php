<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('period_id');
            $table->string('nik', 20);
            $table->string('nama', 100);
            $table->string('nik_ktp', 20)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('jabatan', 100)->nullable();
            $table->date('tanggal_masuk_kontrak')->nullable();
            $table->date('tanggal_akhir_kontrak')->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->string('nama_bank', 100)->nullable();
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('gaji_kurangi_potongan', 15, 2)->default(0);
            $table->decimal('rapel_gaji_lembur', 15, 2)->default(0);
            $table->decimal('kompensasi_pkwt', 15, 2)->default(0);
            $table->decimal('lembur', 15, 2)->default(0);
            $table->decimal('total_pendapatan', 15, 2)->default(0);
            $table->decimal('bpjs_kesehatan_potongan', 15, 2)->default(0); // 1%
            $table->decimal('bpjs_tk_potongan', 15, 2)->default(0); // 3%
            $table->decimal('pph21', 15, 2)->default(0);
            $table->decimal('pembuatan_rekening', 15, 2)->default(0);
            $table->decimal('meterai', 15, 2)->default(0);
            $table->decimal('pinjaman_pribadi', 15, 2)->default(0);
            $table->decimal('sumbangan', 15, 2)->default(0);
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->decimal('total_ditransfer', 15, 2)->default(0);
            $table->date('tgl_lahir')->nullable();
            $table->decimal('potongan_absen', 15, 2)->default(0);
            $table->integer('masuk_kerja_hari')->default(0);
            $table->decimal('lama_lembur_jam', 10, 2)->default(0);
            // BPJS Ketenagakerjaan (Perusahaan)
            $table->string('no_kpj', 30)->nullable();
            $table->string('no_jkn', 30)->nullable();
            $table->decimal('jht_kary', 15, 2)->default(0);
            $table->decimal('jkm', 15, 2)->default(0);
            $table->decimal('jkk', 15, 2)->default(0);
            $table->decimal('pensiun_kary', 15, 2)->default(0);
            $table->decimal('tot_iuran_kary', 15, 2)->default(0);
            // Kehadiran
            $table->integer('hadir')->default(0);
            $table->integer('cuti')->default(0);
            $table->integer('travel')->default(0);
            $table->integer('sakit')->default(0);
            $table->integer('ijin')->default(0);
            $table->integer('alpa')->default(0);
            $table->integer('off')->default(0);
            $table->integer('total_hk')->default(0);
            $table->decimal('ot_jam', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('period_id')->references('id')->on('salary_periods')->onDelete('cascade');
            $table->index(['period_id', 'nik']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_records');
    }
};
