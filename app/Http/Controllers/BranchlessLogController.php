<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BranchlessLog;
use App\Models\BranchlessDevice;


class BranchlessLogController extends Controller
{



   
    public function index(Request $request)
{
    $search = $request->input('search');
      $filterKantor = $request->input('filter_kantor');


    $logs = \App\Models\BranchlessLog::when($search, function ($query, $search) {
        return $query->where('id_perangkat', 'like', "%$search%")
                     ->orWhere('keterangan', 'like', "%$search%");
     })
    ->when($filterKantor, function ($query, $filterKantor) {
            return $query->whereHas('device', function ($subQuery) use ($filterKantor) {
                $subQuery->where('kode_kantor', 'like', "%$filterKantor%");
            });

    
                    })
    ->orderBy('created_at', 'desc')->paginate(10); // ✅ hanya 10 data per halaman

     return view('branchless.log', compact('logs', 'search', 'filterKantor'));
}

public function export(Request $request)
{
    $search = $request->input('search');
     $filterKantor = $request->input('filter_kantor');

    $logs = BranchlessLog::when($search, function ($query, $search) {
        return $query->where('id_perangkat', 'like', "%$search%")
                     ->orWhere('keterangan', 'like', "%$search%");
 })
        ->when($filterKantor, function ($query, $filterKantor) {
            return $query->whereHas('device', function ($subQuery) use ($filterKantor) {
                $subQuery->where('kode_kantor', 'like', "%$filterKantor%");
            });


    })->orderBy('created_at', 'desc')->get();

    $filename = "log_perubahan_branchless.csv";

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($logs) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['ID Perangkat', 'Keterangan', 'Tanggal Perubahan']);

        foreach ($logs as $log) {
            fputcsv($file, [
                $log->id_perangkat,
                $log->keterangan,
                $log->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




}

