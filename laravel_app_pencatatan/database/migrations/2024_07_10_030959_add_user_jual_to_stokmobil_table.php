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
            $table->string('user_jual')->nullable()->after('user_jual_id');
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
            $table->dropColumn('user_jual');
        });
    }
};
