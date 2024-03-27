<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listdatas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_nama_mobil')->constrained('jeniss');
            $table->enum('transmisi', ['manual', 'matic']);
            $table->date('tanggal_beli');
            $table->string('tahun_mobil');
            $table->foreignId('id_warna_mobil')->constrained('warnas');
            $table->string('nomor_polisi');
            $table->integer('harga_jual');
            $table->string('catatan_perbaikan');
            $table->string('foto');
            $table->enum('status', ['available', 'sold']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listdatas');
    }
};
