<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiBulananBranchlessTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi_bulanan_branchless', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kantor', 10);
            $table->year('tahun');
            $table->unsignedTinyInteger('bulan'); // 1 - 12
            $table->integer('jumlah_transaksi');
            $table->timestamps();

            // Optional: Cegah duplikat data per kantor-bulan
            $table->unique(['kode_kantor', 'tahun', 'bulan']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_bulanan_branchless');
    }
}
