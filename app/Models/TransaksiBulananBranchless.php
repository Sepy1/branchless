<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiBulananBranchless extends Model
{
    protected $table = 'transaksi_bulanan_branchless';

    protected $fillable = [
        'kode_kantor',
        'tahun',
        'bulan',
        'jumlah_transaksi',
    ];
}