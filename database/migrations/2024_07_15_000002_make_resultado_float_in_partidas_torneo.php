<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('partidas_torneo', function (Blueprint $table) {
            $table->float('resultado', 3, 1)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('partidas_torneo', function (Blueprint $table) {
            $table->integer('resultado')->nullable()->change();
        });
    }
}; 