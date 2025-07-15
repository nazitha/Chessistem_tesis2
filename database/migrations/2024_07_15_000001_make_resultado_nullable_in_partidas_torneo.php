<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('partidas_torneo', function (Blueprint $table) {
            $table->integer('resultado')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('partidas_torneo', function (Blueprint $table) {
            $table->integer('resultado')->nullable(false)->change();
        });
    }
}; 