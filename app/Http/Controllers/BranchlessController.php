<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchlessDevice;
use App\Models\BranchlessLog;

class BranchlessController extends Controller
{
    public function create()
    {
        return view('branchless.register');
    }

   public function store(Request $request)
{
    // Validasi jika perlu
    $request->validate([
        'id_perangkat' => 'required',
        'merk_hp' => 'required',
        'tipe_hp' => 'required',
        'username' => 'required',
        'nama_lengkap' => 'required',
        'kode_kantor' => 'required',
    ]);

    // Simpan data
    BranchlessDevice::create([
        'id_perangkat' => $request->id_perangkat,
        'merk_hp' => $request->merk_hp,
        'tipe_hp' => $request->tipe_hp,
        'username' => $request->username,
        'nama_lengkap' => $request->nama_lengkap,
        'kode_kantor' => $request->kode_kantor,
    ]);

    return redirect()->back()->with('success', 'Perangkat berhasil ditambahkan!');
}

    public function index(Request $request)
{
    $search = $request->input('search');
    $kodeKantor = $request->input('filter_kantor');

    $devices = BranchlessDevice::when($search, function ($query, $search) {
        return $query->where('id_perangkat', 'like', "%$search%")
                     ->orWhere('username', 'like', "%$search%")
                     ->orWhere('nama_lengkap', 'like', "%$search%")
                     ->orWhere('kode_kantor', 'like', "%$search%");

         })
        ->when($kodeKantor, function ($query, $kodeKantor) {
            return $query->where('kode_kantor', $kodeKantor);

    })->orderBy('created_at', 'desc')->get();

    return view('branchless.pergantian', compact('devices', 'search'));
}

public function destroy($id)
{
    $device = BranchlessDevice::findOrFail($id);
    BranchlessLog::create([
        'id_perangkat' => $device->id_perangkat,
        'keterangan' => 'Data perangkat dihapus'
    ]);

    $device->delete();
   
    return redirect()->route('branchless.pergantian')->with('success', 'Data berhasil dihapus.');
}
public function update(Request $request, $id)
{
    $request->validate([
        'id_perangkat' => 'required',
        'merk_hp' => 'required',
        'tipe_hp' => 'required',
        'username' => 'required',
        'nama_lengkap' => 'required',
        'kode_kantor' => 'required',
    ]);

    $device = BranchlessDevice::findOrFail($id);
    $original = $device->getOriginal(); // Ambil data sebelum update

    $device->update($request->all());

    // Bandingkan dan catat perubahan
    $changes = [];
    foreach ($request->only(['merk_hp', 'tipe_hp', 'username', 'nama_lengkap', 'kode_kantor']) as $key => $newValue) {
        $oldValue = $original[$key] ?? null;
        if ($oldValue !== $newValue) {
            $changes[] = "$key: '$oldValue' → '$newValue'";
        }
    }

    if (!empty($changes)) {
        \App\Models\BranchlessLog::create([
            'id_perangkat' => $device->id_perangkat,
            'keterangan' => 'Perubahan: ' . implode(', ', $changes),
        ]);
    }

    return redirect()->route('branchless.pergantian')->with('success', 'Data berhasil diperbarui.');
}




public function export(Request $request)
{
    $search = $request->input('search');
    $filterKantor = $request->input('filter_kantor');

    $devices = BranchlessDevice::query()
        ->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('id_perangkat', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('nama_lengkap', 'like', "%$search%")
                  ->orWhere('kode_kantor', 'like', "%$search%");
            });
        })
        ->when($filterKantor, function ($query, $filterKantor) {
            return $query->where('kode_kantor', 'like', "%$filterKantor%");
        })
        ->orderBy('created_at', 'desc')
        ->get();

    $filename = "branchless_devices.csv";

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($devices) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['ID Perangkat', 'Merk HP', 'Tipe HP', 'Username', 'Nama Lengkap', 'Kode Kantor']);
        foreach ($devices as $d) {
            fputcsv($file, [
                $d->id_perangkat,
                $d->merk_hp,
                $d->tipe_hp,
                $d->username,
                $d->nama_lengkap,
                $d->kode_kantor
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}

