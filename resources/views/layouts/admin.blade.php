<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel Admin - {{ \App\Models\Setting::getValue('website_name', 'Ecommerce Seragam') }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #dc3545; /* Merah */
            --primary-hover: #b02a37;
            --dark-sidebar: #0f172a;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            --transition-smooth: all 0.2s ease-in-out;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: var(--dark-sidebar);
            min-height: 100vh;
            color: #94a3b8;
            flex-shrink: 0;
            z-index: 100;
            transition: var(--transition-smooth);
        }

        .sidebar-brand {
            padding: 24px;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 20px;
            color: #ffffff;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu {
            padding: 20px 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            border-left: 4px solid transparent;
            transition: var(--transition-smooth);
            gap: 12px;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: var(--primary-color);
        }

        .sidebar-link i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar-header {
            padding: 12px 24px 6px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #475569;
        }

        /* Content Area */
        .content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-y: auto;
        }

        .admin-header {
            background-color: #ffffff;
            padding: 16px 32px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-content {
            padding: 32px;
            flex-grow: 1;
        }

        /* Widgets */
        .stat-widget {
            background: #ffffff;
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: 1px solid #f1f5f9;
            transition: var(--transition-smooth);
        }

        .stat-widget:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* DataTables Custom Styling */
        .table-premium {
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .table-premium th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-premium td {
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-gauge-high text-danger"></i>
            <span>ADMIN SERAGAM</span>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-header">Manajemen Produk</li>
            <li>
                <a href="{{ route('admin.categories') }}" class="sidebar-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i>
                    <span>Kategori Produk</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.products') }}" class="sidebar-link {{ request()->routeIs('admin.products') ? 'active' : '' }}">
                    <i class="fa-solid fa-shirt"></i>
                    <span>Produk & Variasi</span>
                </a>
            </li>

            <li class="sidebar-header">Penjualan & User</li>
            <li>
                <a href="{{ route('admin.orders') }}" class="sidebar-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Daftar Pesanan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Laporan Penjualan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.customers') }}" class="sidebar-link {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Daftar Customer</span>
                </a>
            </li>

            <li class="sidebar-header">CMS & Pengaturan</li>
            <li>
                <a href="{{ route('admin.sliders') }}" class="sidebar-link {{ request()->routeIs('admin.sliders') ? 'active' : '' }}">
                    <i class="fa-solid fa-images"></i>
                    <span>Slider Promo</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.website') }}" class="sidebar-link {{ request()->routeIs('admin.website') ? 'active' : '' }}">
                    <i class="fa-solid fa-gears"></i>
                    <span>Setting Website & RajaOngkir</span>
                </a>
            </li>
            
            <li class="mt-4 pt-4 border-top border-secondary border-opacity-10">
                <a href="{{ route('home') }}" class="sidebar-link text-warning">
                    <i class="fa-solid fa-globe"></i>
                    <span>Lihat Website</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        
        <!-- Header -->
        <header class="admin-header">
            <div>
                <h4 class="mb-0 fw-semibold font-outfit">@yield('page_title', 'Dashboard')</h4>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-muted">Selamat datang, <strong>{{ Auth::user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-1.5 rounded-pill">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Body -->
        <main class="admin-content">
            @yield('content')
        </main>
    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables Bootstrap 5 -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSRF Token setup for AJAX -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
