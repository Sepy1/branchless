@extends('layouts.app')

@section('content')
@if(session('success'))
    <div id="popup-success" style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        z-index: 9999;
        font-weight: bold;
        display: none;
    ">
        ✅ {{ session('success') }}
    </div>
@endif

<div style="max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    
    <!-- Judul -->
    <h2 style="background-color: #000000; color: white; padding: 12px 16px; border-radius: 5px; margin-bottom: 20px;">
        Registrasi Perangkat Branchless
    </h2>

    <form method="POST" action="{{ route('branchless.register.submit') }}">
        @csrf

        <div style="margin-bottom: 15px;">
            <label for="id_perangkat" style="display: block; font-weight: bold; margin-bottom: 5px;">ID Perangkat</label>
            <input type="text" name="id_perangkat" id="id_perangkat" required
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="merk_hp" style="display: block; font-weight: bold; margin-bottom: 5px;">Merk HP</label>
            <input type="text" name="merk_hp" id="merk_hp" required
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="tipe_hp" style="display: block; font-weight: bold; margin-bottom: 5px;">Tipe HP</label>
            <input type="text" name="tipe_hp" id="tipe_hp" required
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="username" style="display: block; font-weight: bold; margin-bottom: 5px;">Username</label>
            <input type="text" name="username" id="username" required
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="nama_lengkap" style="display: block; font-weight: bold; margin-bottom: 5px;">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" required
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 25px;">
            <label for="kode_kantor" style="display: block; font-weight: bold; margin-bottom: 5px;">Kode Kantor</label>
            <input type="text" name="kode_kantor" id="kode_kantor" required
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <!-- Tombol -->
        <div style="text-align: right;">
            <button type="reset"
                    style="background-color: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;">
                Reset
            </button>
            <button type="submit"
                    style="background-color: green; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                SIMPAN
            </button>
        </div>
    </form>
</div>

<!-- Notifikasi sukses otomatis -->
<script>
    window.addEventListener('DOMContentLoaded', function () {
        var popup = document.getElementById('popup-success');
        if (popup) {
            popup.style.display = 'block';
            setTimeout(function () {
                popup.style.display = 'none';
            }, 4000);
        }
    });
</script>
@endsection
