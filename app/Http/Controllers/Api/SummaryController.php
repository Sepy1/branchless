<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiHarianBranchless;

class SummaryController extends Controller
{
    /**
     * Return summary for a given month/year/kode_kantor
     */
    public function summaryBulanan(Request $request)
    {
        $bulan = (int) $request->query('bulan', 0);
        $tahun = (int) $request->query('tahun', 0);
        $kode = $request->query('kode_kantor', '');

        if (!$bulan || !$tahun || !$kode) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        // Aggregate from daily table (required)
        try {
            $harianExists = \Schema::hasTable('transaksi_harian_branchless');
        } catch (\Throwable $e) {
            $harianExists = false;
        }

        if (!$harianExists) {
            return response()->json(['error' => 'Daily transactions table not found'], 500);
        }

        $from = TransaksiHarianBranchless::where('kode_kantor', $kode)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->selectRaw('COALESCE(SUM(jumlah_transaksi),0) as jumlah, COALESCE(SUM(total_pokok),0) as pokok')
            ->first();

        $jumlah = $from->jumlah ?? 0;
        $total_pokok = $from->pokok ?? 0;

        return response()->json(['data' => [
            'jumlah_transaksi' => (int) $jumlah,
            'total_pokok' => (int) $total_pokok,
        ]]);
    }
}
