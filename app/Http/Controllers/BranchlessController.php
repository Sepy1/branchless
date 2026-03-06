<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchlessDevice;
use App\Models\BranchlessLog;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        // merk_hp and tipe_hp are optional now
        'username' => 'required',
        'nama_lengkap' => 'required',
        'kode_kantor' => 'required',
    ]);

    // Simpan data
    BranchlessDevice::create([
        'id_perangkat' => $request->input('id_perangkat'),
        'merk_hp' => $request->input('merk_hp', ''),
        'tipe_hp' => $request->input('tipe_hp', ''),
        'username' => $request->input('username'),
        'nama_lengkap' => $request->input('nama_lengkap'),
        'kode_kantor' => $request->input('kode_kantor'),
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
    \Log::info('Branchless update request', ['id' => $id, 'input' => $request->all()]);

    $request->validate([
        'id_perangkat' => 'required',
        // merk_hp and tipe_hp optional now
        'username' => 'required',
        'nama_lengkap' => 'required',
        'kode_kantor' => 'required',
    ]);

    $device = BranchlessDevice::findOrFail($id);
    $original = $device->getOriginal(); // Ambil data sebelum update

    // prepare data with defaults (allow empty merk/tipe)
    $data = [
        'id_perangkat' => trim((string)$request->input('id_perangkat')),
        'merk_hp' => trim((string)$request->input('merk_hp', '')),
        'tipe_hp' => trim((string)$request->input('tipe_hp', '')),
        'username' => trim((string)$request->input('username')),
        'nama_lengkap' => trim((string)$request->input('nama_lengkap')),
        'kode_kantor' => trim((string)$request->input('kode_kantor')),
    ];
    \Log::info('Branchless update data prepared', $data);
    $device->update($data);

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

        // Generate XLSX using PhpSpreadsheet
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            return redirect()->back()->with('error', 'PhpSpreadsheet not found. Run: composer require phpoffice/phpspreadsheet');
        }

        $rows = [];
        // header
        $rows[] = ['ID Perangkat', 'Merk HP', 'Tipe HP', 'Username', 'Nama Lengkap', 'Kode Kantor'];
        foreach ($devices as $d) {
            $rows[] = [
                $d->id_perangkat,
                $d->merk_hp,
                $d->tipe_hp,
                $d->username,
                $d->nama_lengkap,
                $d->kode_kantor,
            ];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($rows, null, 'A1');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'branchless_devices.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
}

    /**
     * Import devices from uploaded CSV or Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            return redirect()->back()->with('error', "PhpSpreadsheet not found. Run: composer require phpoffice/phpspreadsheet");
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        $rows = [];
        $header = null;
        $skipped = 0;
        foreach ($data as $rowIndex => $row) {
            // skip completely empty rows
            $values = array_values($row);
            $allEmpty = true;
            foreach ($values as $v) {
                if (trim((string)$v) !== '') { $allEmpty = false; break; }
            }
            if ($allEmpty) {
                continue;
            }

            if (!$header) {
                // normalize header: lowercase, replace non-alnum with underscore, trim duplicates
                $header = array_map(function ($h) {
                    $normalized = strtolower(trim((string)$h));
                    $normalized = preg_replace('/[^a-z0-9]+/i', '_', $normalized);
                    $normalized = preg_replace('/_+/', '_', $normalized);
                    $normalized = trim($normalized, '_');
                    // map common aliases
                    $map = [
                        'id' => 'id_perangkat',
                        'idperangkat' => 'id_perangkat',
                        'id_perangkat' => 'id_perangkat',
                        'id_perangkat' => 'id_perangkat',
                        'merk' => 'merk_hp',
                        'merk_hp' => 'merk_hp',
                        'tipe' => 'tipe_hp',
                        'tipe_hp' => 'tipe_hp',
                        'username' => 'username',
                        'nama' => 'nama_lengkap',
                        'nama_lengkap' => 'nama_lengkap',
                        'kode' => 'kode_kantor',
                        'kode_kantor' => 'kode_kantor',
                    ];
                    $key = str_replace('_', '', $normalized);
                    return $map[$key] ?? ($map[$normalized] ?? $normalized);
                }, array_values($row));
                continue;
            }

            $combined = @array_combine($header, array_values($row));
            if ($combined === false) {
                $skipped++;
                continue;
            }

            $rows[] = $combined;
        }

        $created = 0;
        $updated = 0;
        foreach ($rows as $r) {
            // normalize keys we expect
            $record = collect($r)->mapWithKeys(function ($v, $k) {
                return [strtolower($k) => trim($v)];
            })->all();

            // require id_perangkat at minimum
            if (empty($record['id_perangkat'])) {
                continue;
            }

            $data = [
                'id_perangkat' => $record['id_perangkat'] ?? '',
                'merk_hp' => $record['merk_hp'] ?? ($record['merk'] ?? ''),
                'tipe_hp' => $record['tipe_hp'] ?? ($record['tipe'] ?? ''),
                'username' => $record['username'] ?? '',
                'nama_lengkap' => $record['nama_lengkap'] ?? ($record['nama'] ?? ''),
                'kode_kantor' => $record['kode_kantor'] ?? ($record['kode'] ?? ''),
            ];

            $device = BranchlessDevice::where('id_perangkat', $data['id_perangkat'])->first();
            if ($device) {
                $device->update($data);
                $updated++;
                BranchlessLog::create([
                    'id_perangkat' => $device->id_perangkat,
                    'keterangan' => 'Diupdate lewat import file',
                ]);
            } else {
                BranchlessDevice::create($data);
                $created++;
            }
        }

        $msg = "Import selesai. Created: $created, Updated: $updated";
        if (!empty($skipped)) {
            $msg .= ", Skipped: $skipped";
        }

        return redirect()->route('branchless.pergantian')->with('success', $msg);
    }

    /**
     * Stream an XLSX template for download.
     */
    public function downloadTemplate()
    {
        if (!class_exists(Spreadsheet::class)) {
            return redirect()->back()->with('error', "PhpSpreadsheet not found. Run: composer require phpoffice/phpspreadsheet");
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['ID Perangkat', 'Merk HP', 'Tipe HP', 'Username', 'Nama Lengkap', 'Kode Kantor'],
            ['12345', 'Samsung', 'Galaxy A53', 'jdoe', 'John Doe', 'KB001'],
        ], null, 'A1');

        $writer = new Xlsx($spreadsheet);
        $filename = 'branchless_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

}

