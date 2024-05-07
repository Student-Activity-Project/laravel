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
        Schema::create('jeniss', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        DB::table('jeniss')->insert([
            ['id' => '1', 'nama' => 'Toyota'],
            ['id' => '2', 'nama' => 'Daihatsu'],
            ['id' => '3', 'nama' => 'Mitsubishi'],
            ['id' => '4', 'nama' => 'Honda'],
            ['id' => '5', 'nama' => 'Nissan'],
            ['id' => '6', 'nama' => 'Suzuki'],
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jeniss');
    }
};
