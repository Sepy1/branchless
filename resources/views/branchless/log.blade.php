@extends('layouts.app')

@section('content')




<h2 style="margin-bottom: 20px;">Log Perubahan Perangkat</h2>

<form method="GET" action="{{ route('branchless.log') }}" style="margin-bottom: 20px;">
    {{-- Input pencarian umum --}}
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID / keterangan"
           style="padding: 6px; width: 300px; border: 1px solid #ccc;">

    
    {{-- Tombol cari --}}
    <button type="submit"
            style="padding: 6px 12px; background-color: green; color: white; border: none; border-radius: 4px; margin-left: 10px;">
        Cari
    </button>

    {{-- Tombol reset --}}
    <button type="button"
            onclick="window.location.href='{{ route('branchless.log') }}'"
            style="padding: 6px 12px; background-color: red; color: white; border: none; border-radius: 4px; margin-left: 10px;">
        Reset
    </button>

    {{-- Export CSV --}}
    <a href="{{ route('branchless.log.export', ['search' => request('search'), 'filter_kantor' => request('filter_kantor')]) }}"
       style="padding: 6px 12px; background-color: #0800ff; color: white; text-decoration: none; margin-left: 10px;">
        Export CSV
    </a>
</form>



{{-- ✅ Scroll isi tabel, header tetap --}}
<div style="max-height: 320px; overflow-y: auto; border: 1px solid #ccc;">
    <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #000000; color: white;">
                <th style="position: sticky; top: 0; background-color: #000000;">ID Perangkat</th>
                <th style="position: sticky; top: 0; background-color: #000000;">Keterangan</th>
                <th style="position: sticky; top: 0; background-color: #000000;">Tanggal Perubahan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $index => $log)
                <tr style="background-color: {{ $index % 2 == 0 ? '#f9f9f9' : '#ffffff' }};">
                    <td>{{ $log->id_perangkat }}</td>
                    <td>{{ $log->keterangan }}</td>
                    <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Tidak ada log ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
