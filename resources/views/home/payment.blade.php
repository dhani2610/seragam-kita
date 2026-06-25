@extends('layouts.app')

@section('content')
<div class="container my-4 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card premium-card border p-4 shadow">
                
                <!-- Status icon -->
                <div class="my-4">
                    <i class="fa-solid fa-file-invoice-dollar fs-1 text-danger"></i>
                </div>

                <h4 class="font-outfit fw-bold text-dark">Simulasi Pembayaran (Duitku Gateway)</h4>
                <p class="text-muted small">Silakan simulasikan pembayaran untuk nomor tagihan berikut</p>
                
                <hr class="border-secondary border-opacity-10 my-4">

                <!-- Order Info details -->
                <div class="bg-light p-4 rounded-3 text-start mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">No. Invoice</span>
                        <span class="fw-bold font-outfit">{{ $order->invoice_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Metode Pembayaran</span>
                        <span class="fw-semibold text-danger">Virtual Account Duitku</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Kurir Pengiriman</span>
                        <span class="fw-semibold">{{ strtoupper($order->shipping_courier) }} ({{ $order->shipping_service }})</span>
                    </div>
                    <hr class="border-secondary border-opacity-10 my-2">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold text-dark">Total Pembayaran</span>
                        <span class="fs-4 fw-bold text-danger font-outfit">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Simulation steps -->
                <div class="text-start mb-4">
                    <h6 class="fw-bold mb-2 small"><i class="fa-solid fa-circle-info text-danger me-1"></i> Cara Pembayaran:</h6>
                    <ol class="small text-muted ps-3">
                        <li>Gunakan nomor tagihan simulator Virtual Account: <strong>883012345678</strong></li>
                        <li>Klik tombol <strong>Simulasikan Bayar Sukses</strong> di bawah.</li>
                        <li>Sistem Duitku API Callback akan mendeteksi status pembayaran secara otomatis.</li>
                    </ol>
                </div>

                <!-- Action Button -->
                <button type="button" class="btn btn-premium w-100 py-3 font-outfit fw-bold mb-2" id="btn-pay-simulate">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    Simulasikan Bayar Sukses <i class="fa-solid fa-circle-check ms-2"></i>
                </button>
                
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 py-2.5 small">
                    Kembali ke Dashboard Saya
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#btn-pay-simulate').on('click', function() {
        const btn = $(this);
        const spinner = btn.find('.spinner-border');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: "/checkout/payment/{{ $order->id }}/simulate",
            type: "POST",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('dashboard') }}";
                    });
                }
            },
            error: function() {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan sistem pengujian.'
                });
            }
        });
    });
</script>
@endsection
