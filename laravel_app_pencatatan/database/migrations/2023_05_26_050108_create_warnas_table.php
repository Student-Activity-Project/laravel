<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warnas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        DB::table('warnas')->insert([
            ['id' => '1', 'nama' => 'Hitam'],
            ['id' => '2', 'nama' => 'Biru'],
            ['id' => '3', 'nama' => 'Merah'],
            ['id' => '4', 'nama' => 'Kuning'],
            ['id' => '5', 'nama' => 'Hijau'],
            ['id' => '6', 'nama' => 'Putih'],
            ['id' => '7', 'nama' => 'Coklat'],
            ['id' => '8', 'nama' => 'Biru Muda'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warnas');
    }
};
