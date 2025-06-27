<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchlessDevice;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = BranchlessDevice::select('kode_kantor', DB::raw('COUNT(*) as total'))
                    ->groupBy('kode_kantor')
                    ->get();

        // Kirim data sebagai array
        return view('dashboard', ['data' => $data]);
    }
}
