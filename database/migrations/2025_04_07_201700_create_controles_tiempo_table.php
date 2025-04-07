<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('controles_tiempo', function (Blueprint $table) {
            $table->unsignedInteger('id_control_tiempo')->primary();
            $table->string('formato', 12);
            $table->string('control_tiempo', 15);
            $table->string('descrip_control_tiempo', 100)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('controles_tiempo');
    }
}; 