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
            if (!Schema::hasColumn('stokmobils', 'user_jual_id')) {
                $table->foreignId('user_jual_id')->nullable()->constrained('users')->after('tanggal_jual');
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
            if (Schema::hasColumn('stokmobils', 'user_jual_id')) {
                $table->dropColumn('user_jual_id');
            }
        });
    }
};
