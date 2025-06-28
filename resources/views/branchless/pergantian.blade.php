@extends('layouts.app')


@section('content')



<div style="padding: 5px;">
    <h2 style="margin-bottom: 20px;">Update Data Branchless</h2>

{{-- 🔍 Form Pencarian Gabungan --}}
<form method="GET" action="{{ route('branchless.pergantian') }}" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
    {{-- Input Pencarian --}}
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID / Username / Nama "
           style="padding: 6px; width: 250px;">

    {{-- Filter Kode Kantor --}}
    <input type="text" name="filter_kantor" value="{{ request('filter_kantor') }}" placeholder="Filter Kode Kantor"
           style="padding: 6px; width: 180px;">

    {{-- Tombol Cari --}}
    <button type="submit"
            style="padding: 6px 12px; background-color: green; color: white; border: none; border-radius: 4px;">
        Cari
    </button>

    <button type="button" onclick="window.location.href='{{ route('branchless.pergantian') }}'"
        style="padding: 6px 12px; background-color: red; color: white; border: none; border-radius: 4px;">
    Reset
</button>

    {{-- Tombol Export CSV (gunakan URL dengan query) --}}
    <a href="{{ route('branchless.export', ['search' => request('search'), 'filter_kantor' => request('filter_kantor')]) }}"
       style="padding: 6px 12px; background-color: #0800ff; color: white; text-decoration: none; border-radius: 4px;">
        Export CSV
    </a>

    {{-- Tambah Perangkat --}}
    <button type="button" onclick="openAddModal()"
            style="padding: 6px 12px; background-color: rgb(56, 56, 231); color: white; border: none; border-radius: 4px;">
        Tambah Perangkat
    </button>
</form>



       
      

    @if($devices->isEmpty())
        <p>Tidak ada data ditemukan.</p>
    @else
        {{-- ✅ Tabel Scrollable dengan Header Sticky --}}
        <div style="max-height: 320px; overflow-y: auto; border: 1px solid #ccc;">
            <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #000000; color: white;">
                        <th style="position: sticky; top: 0; background-color: #000;">ID Perangkat</th>
                        <th style="position: sticky; top: 0; background-color: #000;">Merk HP</th>
                        <th style="position: sticky; top: 0; background-color: #000;">Tipe HP</th>
                        <th style="position: sticky; top: 0; background-color: #000;">Username</th>
                        <th style="position: sticky; top: 0; background-color: #000;">Nama Lengkap</th>
                        <th style="position: sticky; top: 0; background-color: #000;">Kode Kantor</th>
                        <th style="position: sticky; top: 0; background-color: #000;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devices as $index => $device)
                        <tr style="background-color: {{ $index % 2 == 0 ? '#f9f9f9' : '#ffffff' }};">
                            <td>{{ $device->id_perangkat }}</td>
                            <td>{{ $device->merk_hp }}</td>
                            <td>{{ $device->tipe_hp }}</td>
                            <td>{{ $device->username }}</td>
                            <td>{{ $device->nama_lengkap }}</td>
                            <td>{{ $device->kode_kantor }}</td>
                            <td style="display: flex; gap: 6px;">
                                <button onclick='openEditModal(@json($device))'
                                        style="background-color: yellow; color: black; border: none; padding: 5px 10px; cursor: pointer;">
                                    Edit
                                </button>
                                <form action="{{ route('branchless.delete', $device->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?');">
                                    @csrf
                                    <button type="submit"
                                            style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Modal Edit --}}
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background-color: rgba(0,0,0,0.5); z-index:10000; justify-content:center; align-items:center;">
    <div style="background:white; padding:20px; border-radius:8px; width:400px; position:relative;">
        <h3 style="margin-bottom: 15px;">Edit Perangkat</h3>

        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')

            <input type="hidden" name="id" id="edit_id">

            <label>ID Perangkat</label>
            <input type="text" name="id_perangkat" id="edit_id_perangkat" style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Merk HP</label>
            <input type="text" name="merk_hp" id="edit_merk_hp" style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Tipe HP</label>
            <input type="text" name="tipe_hp" id="edit_tipe_hp" style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Username</label>
            <input type="text" name="username" id="edit_username" style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="edit_nama_lengkap" style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Kode Kantor</label>
            <input type="text" name="kode_kantor" id="edit_kode_kantor" style="width:100%; padding:8px; margin-bottom:15px; border:1px solid #ccc;"><br>

            <div style="text-align:right;">
                <button type="button" onclick="closeEditModal()" style="padding: 6px 12px; background: gray; color: white; border: none; margin-right: 10px;">Batal</button>
                <button type="submit" style="padding: 6px 12px; background: green; color: white; border: none;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Perangkat --}}
<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background-color: rgba(0,0,0,0.5); z-index:10000; justify-content:center; align-items:center;">
    <div style="background:white; padding:20px; border-radius:8px; width:400px; position:relative;">
        <h3 style="margin-bottom: 15px;">Tambah Perangkat</h3>

        <form method="POST" action="{{ route('branchless.store') }}">
            @csrf

            <label>ID Perangkat</label>
            <input type="text" name="id_perangkat" required style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Merk HP</label>
            <input type="text" name="merk_hp" required style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Tipe HP</label>
            <input type="text" name="tipe_hp" required style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Username</label>
            <input type="text" name="username" required style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" required style="width:100%; padding:8px; margin-bottom:10px; border:1px solid #ccc;"><br>

            <label>Kode Kantor</label>
            <input type="text" name="kode_kantor" required style="width:100%; padding:8px; margin-bottom:15px; border:1px solid #ccc;"><br>

            <div style="text-align:right;">
                <button type="button" onclick="closeAddModal()" style="padding: 6px 12px; background: gray; color: white; border: none; margin-right: 10px;">Batal</button>
                <button type="submit" style="padding: 6px 12px; background: green; color: white; border: none;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT --}}
<script>
    function openEditModal(device) {
        document.getElementById('edit_id').value = device.id;
        document.getElementById('edit_id_perangkat').value = device.id_perangkat;
        document.getElementById('edit_merk_hp').value = device.merk_hp;
        document.getElementById('edit_tipe_hp').value = device.tipe_hp;
        document.getElementById('edit_username').value = device.username;
        document.getElementById('edit_nama_lengkap').value = device.nama_lengkap;
        document.getElementById('edit_kode_kantor').value = device.kode_kantor;
        document.getElementById('editForm').action = `/branchless/update/${device.id}`;
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }
</script>

@if(session('success'))
    <div id="success-toast"
         style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
                background-color: #28a745; color: white; padding: 15px 25px;
                border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.3); z-index: 9999;
                font-size: 16px; text-align: center;">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(function () {
            const toast = document.getElementById('success-toast');
            if (toast) {
                toast.style.transition = 'opacity 0.5s ease';
                toast.style.opacity = 0;
                setTimeout(() => toast.remove(), 500); // hapus dari DOM
            }
        }, 3000);
    </script>
@endif

@endsection
