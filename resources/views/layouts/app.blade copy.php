<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard Branchless</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #2c3e50; /* biru tua */
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100vh;
            background-color: #2c3e50;
            padding-top: 60px; /* space for navbar */
            border-right: 1px solid #444;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid #444; /* garis antar menu */
        }

        .sidebar a:hover {
            background-color: #3d566e;
        }

        .sidebar a.active {
            background-color: #1abc9c;
            color: white;
        }

        .content {
            margin-left: 220px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <div><strong>Dashboard Branchless</strong></div>
        <div>{{ Auth::user()->name ?? 'Guest' }}</div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="{{ route('branchless.register') }}" class="{{ Request::is('branchless/register') ? 'active' : '' }}">Registrasi Branchless</a>
        <a href="{{ route('branchless.pergantian') }}" class="{{ Request::is('branchless/pergantian') ? 'active' : '' }}">Pergantian Data Branchless</a>
        <a href="{{ route('branchless.log') }}" class="{{ Request::is('branchless/log') ? 'active' : '' }}">Log Perubahan Perangkat</a>
        <a href="{{ route('logout') }}">Log Out</a>
        
    </div>

    <!-- Konten Utama -->
    <div class="content">
        @yield('content')
    </div>

</body>
</html>
