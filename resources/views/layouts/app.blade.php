<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Branchless App</title>

    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        /* Top Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #003366;
            color: white;
            padding: 10px 20px;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar img {
            height: 40px;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .layout {
            display: flex;
            margin-top: 60px; /* tinggi navbar */
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 60px; /* setelah navbar */
            left: 0;
            width: 240px;
            height: calc(100vh - 60px);
            background-color: #003366;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
            z-index: 999;
        }

        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 15px;
        }

        .sidebar nav a {
            display: block;
            color: white;
            padding: 10px;
            border-bottom: 1px solid #005599;
            text-decoration: none;
        }

        .sidebar nav a:hover {
            background-color: #005599;
        }

        .sidebar.hidden {
            display: none;
        }

        /* Content */
        .main {
            flex: 1;
            margin-left: 240px;
            padding: 20px;
            background-color: #fff;
            min-height: calc(100vh - 60px);
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .sidebar.hidden {
                display: block;
            }

            .main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <div class="navbar">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Laravel">
        <button onclick="toggleSidebar()" class="toggle-btn">☰</button>
    </div>

    <div class="layout">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            <h2>Dashboard Branchless</h2>
            <nav>
                <a href="{{ route('status') }}">Informasi</a>
                <a href="{{ route('branchless.pergantian') }}">Nominatif Branchless</a>
                <a href="{{ route('branchless.log') }}">Log Perubahan Perangkat</a>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   style="display: block; margin-top: 20px; color: rgb(255, 255, 255); font-weight: bold;">
                    🚪 Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        }
    </script>
</body>
</html>
