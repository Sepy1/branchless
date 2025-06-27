<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        // Distribusi perangkat
        $data = DB::table('branchless_devices')
            ->select('kode_kantor', DB::raw('COUNT(*) as total'))
            ->groupBy('kode_kantor')
            ->get();

        $labels = $data->pluck('kode_kantor')->toArray();
        $counts = $data->pluck('total')->toArray();

        $filterKode = $request->input('kode_kantor');

        // Ambil 6 bulan terakhir
        $bulanSekarang = now()->month;
        $tahunSekarang = now()->year;

        $bulanLabels = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->month;
        })->reverse()->values();

        $namaBulan = $bulanLabels->map(fn($b) => \Carbon\Carbon::create()->month($b)->translatedFormat('F'));

        $query = DB::table('transaksi_bulanan_branchless')
            ->select('bulan', DB::raw('SUM(jumlah_transaksi) as total'))
            ->where('tahun', $tahunSekarang)
            ->whereIn('bulan', $bulanLabels);

        if ($filterKode) {
            $query->where('kode_kantor', $filterKode);
        }

        $transaksi = $query->groupBy('bulan')->orderBy('bulan')->get();
        $transaksiMap = $transaksi->keyBy('bulan');

        $jumlahTransaksi = $bulanLabels->map(function ($bulan) use ($transaksiMap) {
            return $transaksiMap[$bulan]->total ?? 0;
        });

        return view('status', compact('data', 'labels', 'counts', 'namaBulan', 'jumlahTransaksi', 'filterKode'));
    }

    // Endpoint untuk AJAX
    public function chartData(Request $request)
    {
        $filterKode = $request->input('kode_kantor');
        $bulanSekarang = now()->month;
        $tahunSekarang = now()->year;

        $bulanLabels = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->month;
        })->reverse()->values();

        $namaBulan = $bulanLabels->map(fn($b) => \Carbon\Carbon::create()->month($b)->translatedFormat('F'));

        $query = DB::table('transaksi_bulanan_branchless')
            ->select('bulan', DB::raw('SUM(jumlah_transaksi) as total'))
            ->where('tahun', $tahunSekarang)
            ->whereIn('bulan', $bulanLabels);

        if ($filterKode) {
            $query->where('kode_kantor', $filterKode);
        }

        $transaksi = $query->groupBy('bulan')->orderBy('bulan')->get();
        $transaksiMap = $transaksi->keyBy('bulan');

        $jumlahTransaksi = $bulanLabels->map(function ($bulan) use ($transaksiMap) {
            return $transaksiMap[$bulan]->total ?? 0;
        });

        return response()->json([
            'labels' => $namaBulan,
            'data' => $jumlahTransaksi,
            'kode' => $filterKode
        ]);
    }
}
