@extends('layouts.app')


@section('content')



<div style="padding: 5px;">
    <h2 style="margin-bottom: 20px;">Update Data Branchless</h2>

{{-- 🔍 Form Pencarian Gabungan --}}
<form method="GET" action="{{ route('branchless.pergantian') }}" style="margin-bottom: 12px; display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
    {{-- Input Pencarian --}}
        <input id="global-search" type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID / Username / Nama "
            style="padding: 6px; width: 250px;">

    {{-- Filter Kode Kantor --}}
        <input id="filter-kode" type="text" name="filter_kantor" value="{{ request('filter_kantor') }}" placeholder="Filter Kode Kantor"
            style="padding: 6px; width: 180px;">

    {{-- Search is automatic on input (no submit/reset buttons) --}}
    {{-- Tambah Perangkat (letakkan tombol lain di luar form untuk menghindari nested forms) --}}
    <button type="button" onclick="openAddModal()"
            style="padding: 6px 12px; background-color: rgb(56, 56, 231); color: white; border: none; border-radius: 4px;">
        Tambah Perangkat
    </button>
    <a href="{{ route('branchless.export', ['search' => request('search'), 'filter_kantor' => request('filter_kantor')]) }}"
       style="padding: 6px 12px; background-color: #0800ff; color: white; text-decoration: none; border-radius: 4px; margin-left:6px;">
        Export (.xlsx)
    </a>
</form>

{{-- Flash & Errors --}}
@if(session('success'))
    <div id="success-toast" style="margin:8px 0; padding:10px; background:#28a745; color:white; border-radius:6px;">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div style="margin:8px 0; padding:10px; background:#dc3545; color:white; border-radius:6px;">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div style="margin:8px 0; padding:10px; background:#ffc107; color:#000; border-radius:6px;">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Actions: Export / Download Template / Upload --}}
<div style="margin-bottom:12px; display:flex; gap:8px; align-items:center;">
    <a href="{{ route('branchless.template') }}" download
       style="padding:6px 12px; background:#6c757d; color:#fff; text-decoration:none; border-radius:4px;">
        Unduh Template (.xlsx)
    </a>

    <form action="{{ route('branchless.import') }}" method="POST" enctype="multipart/form-data" style="display:inline-block; margin:0;">
        @csrf
        <input type="file" name="file" accept=".xlsx,.xls" style="display:inline-block;">
        <button type="submit" style="padding:6px 10px; background:#0a7; color:#fff; border:none; border-radius:4px; margin-left:6px;">Upload</button>
    </form>
</div>



       
      

    @if($devices->isEmpty())
        <p>Tidak ada data ditemukan.</p>
    @else
        {{-- ✅ Tabel tinggi dinamis hingga batas bawah layar, dengan sorting dan filter --}}
        <div id="devices-table-container" style="height: calc(100vh - 300px); overflow-y: auto; border: 1px solid #ccc;">
            <table id="devices-table" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #000000; color: white;">
                        <th style="position: sticky; top: 0; background-color: #000;">ID Perangkat
                            <button class="sort-btn" onclick="sortTable(0, true)">▲</button>
                            <button class="sort-btn" onclick="sortTable(0, false)">▼</button>
                        </th>
                        <th style="position: sticky; top: 0; background-color: #000;">Merk HP
                            <button class="sort-btn" onclick="sortTable(1, true)">▲</button>
                            <button class="sort-btn" onclick="sortTable(1, false)">▼</button>
                        </th>
                        <th style="position: sticky; top: 0; background-color: #000;">Tipe HP
                            <button class="sort-btn" onclick="sortTable(2, true)">▲</button>
                            <button class="sort-btn" onclick="sortTable(2, false)">▼</button>
                        </th>
                        <th style="position: sticky; top: 0; background-color: #000;">Username
                            <button class="sort-btn" onclick="sortTable(3, true)">▲</button>
                            <button class="sort-btn" onclick="sortTable(3, false)">▼</button>
                        </th>
                        <th style="position: sticky; top: 0; background-color: #000;">Nama Lengkap
                            <button class="sort-btn" onclick="sortTable(4, true)">▲</button>
                            <button class="sort-btn" onclick="sortTable(4, false)">▼</button>
                        </th>
                        <th style="position: sticky; top: 0; background-color: #000;">Kode Kantor
                            <button class="sort-btn" onclick="sortTable(5, true)">▲</button>
                            <button class="sort-btn" onclick="sortTable(5, false)">▼</button>
                        </th>
                        <th style="position: sticky; top: 0; background-color: #000;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devices as $index => $device)
                        <tr class="devices-row" style="background-color: {{ $index % 2 == 0 ? '#f9f9f9' : '#ffffff' }};">
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

<script>
    // Sorting and filtering for the devices table
    function sortTable(colIndex, asc = true) {
        const table = document.getElementById('devices-table');
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const A = a.cells[colIndex].innerText.trim().toLowerCase();
            const B = b.cells[colIndex].innerText.trim().toLowerCase();
            if (!isNaN(parseFloat(A)) && !isNaN(parseFloat(B))) {
                return asc ? (parseFloat(A) - parseFloat(B)) : (parseFloat(B) - parseFloat(A));
            }
            return asc ? A.localeCompare(B) : B.localeCompare(A);
        });

        // re-append rows
        rows.forEach(r => tbody.appendChild(r));
    }

    function filterTable() {
        const table = document.getElementById('devices-table');
        const tbody = table.tBodies[0];
        const q = (document.getElementById('global-search')?.value || '').trim().toLowerCase();
        const kode = (document.getElementById('filter-kode')?.value || '').trim().toLowerCase();

        Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
            const cells = Array.from(tr.cells).map(c => c.innerText.trim().toLowerCase());
            // if kode filter provided, require match on kode_kantor column (index 5)
            let visible = true;
            if (kode) {
                const kodeCell = (cells[5] || '');
                if (!kodeCell.includes(kode)) visible = false;
            }
            if (q) {
                // match q across id_perangkat, username, nama_lengkap, kode_kantor
                const hay = [cells[0] || '', cells[3] || '', cells[4] || '', cells[5] || ''].join(' ');
                if (!hay.includes(q)) visible = false;
            }
            tr.style.display = visible ? '' : 'none';
        });
    }

    // hook inputs to auto-filter
    document.addEventListener('DOMContentLoaded', function () {
        const s = document.getElementById('global-search');
        const k = document.getElementById('filter-kode');
        if (s) s.addEventListener('input', filterTable);
        if (k) k.addEventListener('input', filterTable);
        // apply initial filter if inputs already have values
        filterTable();
    });
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
