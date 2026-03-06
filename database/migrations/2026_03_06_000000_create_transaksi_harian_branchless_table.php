<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi_harian_branchless', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kantor', 10);
            $table->date('tanggal');
            $table->integer('jumlah_transaksi')->default(0);
            $table->bigInteger('total_pokok')->default(0);
            $table->timestamps();

            $table->unique(['kode_kantor', 'tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_harian_branchless');
    }
};
