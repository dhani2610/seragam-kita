@extends('layouts.admin')

@section('page_title', 'Dashboard Ringkasan Penjualan')

@section('content')
<!-- Row of widgets: Pemasukan -->
<div class="row g-4 mb-4">
    <!-- Pemasukan Hari Ini -->
    <div class="col-md-4">
        <div class="stat-widget d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold uppercase tracking-wider d-block">Pemasukan Hari Ini</span>
                <h3 class="font-outfit fw-bold mt-1 mb-0 text-danger">Rp {{ number_format($incomeToday, 0, ',', '.') }}</h3>
            </div>
            <div class="stat-icon bg-danger-subtle text-danger">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
        </div>
    </div>
    
    <!-- Pemasukan Bulan Ini -->
    <div class="col-md-4">
        <div class="stat-widget d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold uppercase tracking-wider d-block">Pemasukan Bulan Ini</span>
                <h3 class="font-outfit fw-bold mt-1 mb-0 text-danger">Rp {{ number_format($incomeMonth, 0, ',', '.') }}</h3>
            </div>
            <div class="stat-icon bg-danger-subtle text-danger">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Pemasukan Total -->
    <div class="col-md-4">
        <div class="stat-widget d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold uppercase tracking-wider d-block">Pemasukan Total</span>
                <h3 class="font-outfit fw-bold mt-1 mb-0 text-danger">Rp {{ number_format($incomeTotal, 0, ',', '.') }}</h3>
            </div>
            <div class="stat-icon bg-danger-subtle text-danger">
                <i class="fa-solid fa-vault"></i>
            </div>
        </div>
    </div>
</div>

<!-- Row of widgets: Counts -->
<div class="row g-4 mb-4">
    <!-- Customers count -->
    <div class="col-md-4">
        <div class="stat-widget d-flex align-items-center justify-content-between bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <h5 class="m-0 fw-bold font-outfit">{{ $totalCustomers }}</h5>
                    <span class="text-muted small">Total Customer Terdaftar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders count -->
    <div class="col-md-4">
        <div class="stat-widget d-flex align-items-center justify-content-between bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div>
                    <h5 class="m-0 fw-bold font-outfit">{{ $totalOrders }}</h5>
                    <span class="text-muted small">Total Transaksi Order</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending orders -->
    <div class="col-md-4">
        <div class="stat-widget d-flex align-items-center justify-content-between bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h5 class="m-0 fw-bold font-outfit">{{ $pendingOrders }}</h5>
                    <span class="text-muted small">Pesanan Pending</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Latest transactions log -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="m-0 font-outfit fw-bold text-dark"><i class="fa-solid fa-receipt me-2 text-danger"></i> Transaksi Terakhir Masuk</h5>
                <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-danger px-3 font-outfit small">Semua Pesanan</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Tanggal</th>
                                <th>Grand Total</th>
                                <th>Pembayaran</th>
                                <th>Status Pesanan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestOrders as $order)
                                <tr>
                                    <td><strong>{{ $order->invoice_number }}</strong></td>
                                    <td>{{ $order->user->name }}</td>
                                    <td class="small">{{ $order->created_at->format('d M Y, H:i') }} WIB</td>
                                    <td class="fw-semibold text-danger">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($order->payment_status === 'Paid')
                                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Lunas</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $order->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <span class="text-muted small">Belum ada transaksi masuk</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
