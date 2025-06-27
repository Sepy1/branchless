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
        Schema::create('branchless_devices', function (Blueprint $table) {
            $table->id();
            $table->string('id_perangkat');
            $table->string('merk_hp');
            $table->string('tipe_hp');
            $table->string('username');
            $table->string('nama_lengkap');
            $table->string('kode_kantor');
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
        Schema::dropIfExists('branchless_devices');
    }
};
