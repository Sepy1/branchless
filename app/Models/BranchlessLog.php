<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BranchlessDevice;

class BranchlessLog extends Model
{
       use HasFactory;

    protected $fillable = ['id_perangkat', 'keterangan'];

 public function device()
    {
        return $this->belongsTo(BranchlessDevice::class, 'id_perangkat', 'id_perangkat');
    }

}

