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

        /* Top Navigation (customized to avoid Bootstrap conflict) */
        .custom-navbar {
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

        .custom-navbar img {
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
            top: 60px;
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

        /* Main Content */
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

    <!-- Top Navigation Bar -->
    <div class="custom-navbar">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Laravel">

    <!-- Profile Dropdown -->
    @auth
         <div class="dropdown">
        <a class="dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fas fa-user-circle" style="font-size: 24px;"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            
         
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
    @endauth
    </div>

    <div class="layout">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            <h2>Dashboard Branchless</h2>
            <nav>
                <a href="{{ route('status') }}">Dashboard</a>
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

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybBogGzY1yd1zR2nCqD/8STf4fhI1ChfP1TVM3Wfda19z5aKv" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeo5KkNykvS0dD5RlmI3Aatn3zF/1b8H0EdEfJ+vF4pYxE1+" crossorigin="anonymous"></script>
</body>
</html>
