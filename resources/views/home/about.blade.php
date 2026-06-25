@extends('layouts.app')

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Tentang Kami</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card premium-card border p-5 shadow-sm">
                <h2 class="font-outfit fw-bold text-danger mb-4 text-center">Tentang Kami</h2>
                <div class="text-center mb-5">
                    <img src="{{ \App\Models\Setting::getValue('logo_path', '/assets/images/logo.png') }}" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                    <p class="text-muted font-outfit fw-semibold fs-5 mb-0">Platform E-Commerce Seragam Sekolah Terlengkap & Terpercaya</p>
                </div>

                <div class="lh-lg text-secondary" style="font-size: 15px; text-align: justify;">
                    {!! nl2br(e($about)) !!}
                </div>

                <hr class="border-secondary border-opacity-10 my-5">

                <div class="row g-4 text-center mt-2">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-4 h-100 border">
                            <i class="fa-solid fa-shirt text-danger fs-3 mb-3"></i>
                            <h6 class="fw-bold font-outfit text-dark">Kualitas Premium</h6>
                            <p class="small text-muted mb-0">Bahan katun premium berstandar nasional, dingin dan awet dipakai.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-4 h-100 border">
                            <i class="fa-solid fa-money-bill-wave text-danger fs-3 mb-3"></i>
                            <h6 class="fw-bold font-outfit text-dark">Harga Terjangkau</h6>
                            <p class="small text-muted mb-0">Kami memberikan penawaran harga terbaik langsung dari produsen konveksi.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-4 h-100 border">
                            <i class="fa-solid fa-truck-fast text-danger fs-3 mb-3"></i>
                            <h6 class="fw-bold font-outfit text-dark">Pengiriman Cepat</h6>
                            <p class="small text-muted mb-0">Integrasi pengiriman dengan RajaOngkir untuk memantau paket kiriman Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
