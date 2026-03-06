<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('transaksi_bulanan_branchless')) {
            Schema::dropIfExists('transaksi_bulanan_branchless');
        }
    }

    public function down()
    {
        Schema::create('transaksi_bulanan_branchless', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kantor', 10);
            $table->year('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->integer('jumlah_transaksi');
            $table->timestamps();
            $table->unique(['kode_kantor', 'tahun', 'bulan']);
        });
    }
};
