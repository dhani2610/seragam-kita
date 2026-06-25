@extends('layouts.app')

@section('content')
<div class="container my-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Cara Pemesanan</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card premium-card border p-5 shadow-sm">
                <h2 class="font-outfit fw-bold text-danger mb-4 text-center">Alur Pemesanan & Pembayaran</h2>
                <p class="text-muted text-center mb-5">Ikuti langkah mudah belanja seragam sekolah di platform kami</p>

                <!-- Flow step timeline -->
                <div class="row g-4 position-relative">
                    
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded-4 h-100 border text-center relative-card">
                            <div class="flow-badge bg-danger text-white">1</div>
                            <i class="fa-solid fa-cart-shopping text-danger fs-2 mb-3 mt-2"></i>
                            <h6 class="fw-bold font-outfit text-dark mb-2">Pilih Produk & Variasi</h6>
                            <p class="small text-muted mb-0">Pilih jenis seragam (SD/SMP/SMA), pilih variasi warna/ukuran yang sesuai, tentukan jumlahnya, lalu masukkan keranjang.</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded-4 h-100 border text-center relative-card">
                            <div class="flow-badge bg-danger text-white">2</div>
                            <i class="fa-solid fa-map-location-dot text-danger fs-2 mb-3 mt-2"></i>
                            <h6 class="fw-bold font-outfit text-dark mb-2">Isi Alamat & Hitung Ongkir</h6>
                            <p class="small text-muted mb-0">Isi data alamat pengiriman Anda secara lengkap. Pilih kurir (JNE/TIKI/POS) untuk kalkulasi ongkos kirim real-time.</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded-4 h-100 border text-center relative-card">
                            <div class="flow-badge bg-danger text-white">3</div>
                            <i class="fa-solid fa-credit-card text-danger fs-2 mb-3 mt-2"></i>
                            <h6 class="fw-bold font-outfit text-dark mb-2">Bayar via Payment Gateway</h6>
                            <p class="small text-muted mb-0">Lakukan pembayaran dengan metode yang Anda inginkan (Transfer Bank Virtual Account, E-Wallet QRIS) via Duitku.</p>
                        </div>
                    </div>

                    <div class="col-md-4 offset-md-2 mt-4">
                        <div class="p-4 bg-light rounded-4 h-100 border text-center relative-card">
                            <div class="flow-badge bg-danger text-white">4</div>
                            <i class="fa-solid fa-box-open text-danger fs-2 mb-3 mt-2"></i>
                            <h6 class="fw-bold font-outfit text-dark mb-2">Proses Pengemasan</h6>
                            <p class="small text-muted mb-0">Setelah dana pembayaran terverifikasi, Admin kami akan segera mengemas seragam pesanan Anda dengan rapi.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mt-4">
                        <div class="p-4 bg-light rounded-4 h-100 border text-center relative-card">
                            <div class="flow-badge bg-danger text-white">5</div>
                            <i class="fa-solid fa-truck-ramp-box text-danger fs-2 mb-3 mt-2"></i>
                            <h6 class="fw-bold font-outfit text-dark mb-2">Pengiriman & Resi</h6>
                            <p class="small text-muted mb-0">Kurir mengambil paket, lalu admin akan memasukkan nomor resi tracking agar Anda dapat memantau status pesanan.</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .relative-card {
        position: relative;
    }
    
    .flow-badge {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        border: 2px solid white;
    }
</style>
@endsection
