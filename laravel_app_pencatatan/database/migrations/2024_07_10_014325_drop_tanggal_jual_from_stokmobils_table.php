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
        Schema::table('stokmobils', function (Blueprint $table) {
            if (Schema::hasColumn('stokmobils', 'tanggal_jual')) {
                $table->dropColumn('tanggal_jual');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stokmobils', function (Blueprint $table) {
            $table->date('tanggal_jual')->nullable()->after('tanggal_beli');
        });
    }
};
