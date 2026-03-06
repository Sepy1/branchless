<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiHarianBranchless extends Model
{
    use HasFactory;

    protected $table = 'transaksi_harian_branchless';

    protected $fillable = [
        'kode_kantor',
        'tanggal',
        'jumlah_transaksi',
        'total_pokok',
    ];
}
