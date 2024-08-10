<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id('id_status');
            $table->integer('profil_tes_id');
            $table->integer('id_siswa');
            $table->integer('soal_bank_paket_id');
            $table->string('nama');
            $table->string('nisn');
            $table->char('jk');
            $table->float('skor');
            $table->integer('soal_benar');
            $table->string('nama_pelajaran');
            $table->integer('pelajaran_id');
            $table->integer('materi_uji_id');
            $table->integer('sesi');
            $table->integer('id_pelaksanaan');
            $table->string('nama_sekolah');
            $table->integer('total_soal');
            $table->integer('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
