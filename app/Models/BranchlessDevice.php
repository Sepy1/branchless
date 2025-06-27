<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchlessDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_perangkat',
        'merk_hp',
        'tipe_hp',
        'username',
        'nama_lengkap',
        'kode_kantor',
    ];
}
