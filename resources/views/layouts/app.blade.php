<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::getValue('website_name', 'Ecommerce Seragam') }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Custom Premium Styles (Merah Putih / Shopee style) -->
    <style>
        :root {
            --primary-color: #dc3545; /* Merah */
            --primary-hover: #b02a37;
            --secondary-color: #ffffff; /* Putih */
            --dark-color: #1e293b;
            --light-bg: #f8fafc;
            --shopee-orange: #ee4d2d;
            --accent-color: #ffc107;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-color);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }

        /* Navbar Styling */
        .navbar-main {
            background-color: var(--secondary-color);
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border-bottom: 2px solid var(--primary-color);
            padding: 15px 0;
            transition: var(--transition-smooth);
        }

        .navbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 24px;
            color: var(--primary-color) !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            max-height: 40px;
            object-fit: contain;
        }

        .nav-link {
            font-weight: 500;
            color: var(--dark-color);
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: var(--transition-smooth);
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            background-color: rgba(220, 53, 69, 0.05);
        }

        .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: 600;
        }

        /* Cart Badge Icon */
        .cart-icon-wrapper {
            position: relative;
            padding: 8px;
            border-radius: 50%;
            background-color: rgba(220, 53, 69, 0.05);
            color: var(--primary-color);
            transition: var(--transition-smooth);
        }

        .cart-icon-wrapper:hover {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--accent-color);
            color: var(--dark-color);
            font-weight: 700;
            font-size: 11px;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--secondary-color);
        }

        /* Premium Buttons */
        .btn-premium {
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff4d5a 100%);
            color: var(--secondary-color);
            border: none;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
            transition: var(--transition-smooth);
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
            color: var(--secondary-color);
        }

        .btn-premium-outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            font-weight: 600;
            padding: 8px 22px;
            border-radius: 8px;
            transition: var(--transition-smooth);
        }

        .btn-premium-outline:hover {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            transform: translateY(-2px);
        }

        /* Cards */
        .premium-card {
            background-color: var(--secondary-color);
            border: none;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: var(--transition-smooth);
            overflow: hidden;
        }

        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
        }

        /* Footer */
        .footer-main {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 70px 0 30px;
            margin-top: 80px;
            border-top: 5px solid var(--primary-color);
        }

        .footer-main h5 {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 18px;
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            transition: var(--transition-smooth);
            display: inline-block;
            margin-bottom: 10px;
        }

        .footer-link:hover {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.05);
            color: var(--secondary-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
            margin-right: 10px;
            text-decoration: none;
        }

        .social-btn:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
            color: var(--secondary-color);
        }

        /* Top Alert Bar */
        .top-bar {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            font-size: 13px;
            font-weight: 600;
            padding: 8px 0;
            text-align: center;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <i class="fa-solid fa-gift me-2"></i> Belanja Seragam Sekolah Murah & Berkualitas di Sini! Pengiriman Cepat Seluruh Indonesia.
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-main sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ \App\Models\Setting::getValue('logo_path', '/assets/images/logo.png') }}" alt="Logo">
                <span>{{ \App\Models\Setting::getValue('website_name', 'Ecommerce Seragam') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Search bar in Navbar -->
                <form class="d-flex mx-auto col-lg-5 my-2 my-lg-0" action="{{ route('home') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control rounded-start-pill border-danger" placeholder="Cari seragam sekolah..." value="{{ request('search') }}">
                        <button class="btn btn-danger rounded-end-pill px-4" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <ul class="navbar-nav align-items-lg-center ms-auto gap-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('flow') ? 'active' : '' }}" href="{{ route('flow') }}">Cara Order</a>
                    </li>

                    <!-- Shopping Cart Icon -->
                    <li class="nav-item me-2">
                        <a href="{{ route('cart.index') }}" class="nav-link d-inline-block">
                            <div class="cart-icon-wrapper">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span class="cart-badge" id="cart-badge-count">{{ count(session('cart', [])) }}</span>
                            </div>
                        </a>
                    </li>

                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="{{ Auth::user()->avatar ?: '/assets/images/avatar.png' }}" alt="Avatar" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-line me-2 text-danger"></i> Panel Admin</a></li>
                                @else
                                    <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="fa-solid fa-user me-2 text-danger"></i> Dashboard Saya</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-premium-outline py-2">Masuk</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-premium py-2">Daftar</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content Page -->
    <main class="min-vh-100 py-4">
        @yield('content')
    </main>

    <!-- Main Footer -->
    <footer class="footer-main">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="navbar-brand text-white mb-4">
                        <img src="{{ \App\Models\Setting::getValue('logo_path', '/assets/images/logo.png') }}" alt="Logo" class="me-2" style="max-height: 35px; background: white; padding: 3px; border-radius: 4px;">
                        {{ \App\Models\Setting::getValue('website_name', 'Ecommerce Seragam') }}
                    </h5>
                    <p class="small text-white">{{ \App\Models\Setting::getValue('about_us', '') }}</p>
                    <div class="mt-4">
                        <a href="{{ \App\Models\Setting::getValue('facebook_url', '#') }}" class="social-btn"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="{{ \App\Models\Setting::getValue('instagram_url', '#') }}" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
                        <a href="{{ \App\Models\Setting::getValue('youtube_url', '#') }}" class="social-btn"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5>Navigasi</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="footer-link">Beranda</a></li>
                        <li><a href="{{ route('about') }}" class="footer-link">Tentang Kami</a></li>
                        <li><a href="{{ route('flow') }}" class="footer-link">Cara Order</a></li>
                        <li><a href="{{ route('cart.index') }}" class="footer-link">Keranjang Belanja</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Info Kontak</h5>
                    <p class="small text-white mb-2"><i class="fa-solid fa-location-dot me-2 text-danger"></i> {{ \App\Models\Setting::getValue('address_text', '') }}</p>
                    <p class="small text-white mb-2"><i class="fa-solid fa-clock me-2 text-danger"></i> {{ \App\Models\Setting::getValue('operational_hours', '') }}</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Peta Lokasi</h5>
                    <div class="rounded overflow-hidden shadow-sm border border-secondary border-opacity-25">
                        {!! \App\Models\Setting::getValue('maps_iframe', '') !!}
                    </div>
                </div>
            </div>
            <hr class="border-secondary mt-5 mb-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small text-white mb-0">&copy; {{ date('Y') }} {{ \App\Models\Setting::getValue('website_name', 'Ecommerce Seragam') }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3 text-white">
                        <i class="fa-brands fa-cc-visa fs-4"></i>
                        <i class="fa-brands fa-cc-mastercard fs-4"></i>
                        <span class="small fw-semibold align-self-center text-uppercase">Duitku Integrated</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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
