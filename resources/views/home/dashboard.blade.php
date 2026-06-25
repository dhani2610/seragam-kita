@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="row g-4">
        <!-- Left Sidebar: Customer Profile Summary -->
        <div class="col-lg-4">
            <div class="card premium-card border p-4 text-center">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <img src="{{ Auth::user()->avatar ?: '/assets/images/avatar.png' }}" alt="Avatar" class="rounded-circle border shadow-sm" style="width: 110px; height: 110px; object-fit: cover;">
                </div>
                <h5 class="font-outfit fw-bold text-dark mb-1">{{ Auth::user()->name }}</h5>
                <span class="badge bg-danger text-uppercase px-3 py-1.5 font-outfit" style="font-size: 11px;">Customer</span>

                <hr class="border-secondary border-opacity-10 my-4">

                <!-- Profile fields info -->
                <div class="text-start">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Email</small>
                        <strong class="small text-dark">{{ Auth::user()->email }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Nomor HP</small>
                        <strong class="small text-dark">{{ Auth::user()->phone }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Alamat Pengiriman</small>
                        <strong class="small text-dark d-block mb-1">{{ Auth::user()->city }}, {{ Auth::user()->province }}</strong>
                        <p class="small text-muted mb-0">{{ Auth::user()->address }} ({{ Auth::user()->postal_code }})</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Transaction History list -->
        <div class="col-lg-8">
            <div class="card premium-card border p-4">
                <h4 class="font-outfit fw-bold text-danger mb-4 border-bottom pb-2">Riwayat Transaksi</h4>

                <div class="accordion accordion-flush" id="ordersAccordion">
                    @forelse($orders as $index => $order)
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden">
                            <h2 class="accordion-header" id="heading-{{ $order->id }}">
                                <button class="accordion-button collapsed px-4 py-3 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $order->id }}">
                                    <div class="w-100 d-flex flex-wrap align-items-center justify-content-between pe-3">
                                        <div class="me-3">
                                            <span class="fw-bold text-dark d-block font-outfit" style="font-size: 15px;">{{ $order->invoice_number }}</span>
                                            <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }} WIB</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 my-2 my-md-0">
                                            <!-- Payment Badge -->
                                            @if($order->payment_status === 'Paid')
                                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25" style="font-size: 12px; padding: 6px 12px;">Lunas</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25" style="font-size: 12px; padding: 6px 12px;">Belum Bayar</span>
                                            @endif

                                            <!-- Shipping Status Badge -->
                                            @php
                                                $statusColor = [
                                                    'Pending' => 'bg-secondary-subtle text-secondary border-secondary',
                                                    'Dikemas' => 'bg-info-subtle text-info border-info',
                                                    'Dalam Pengiriman' => 'bg-primary-subtle text-primary border-primary',
                                                    'Selesai' => 'bg-success-subtle text-success border-success'
                                                ][$order->status] ?? 'bg-secondary-subtle text-secondary';
                                            @endphp
                                            <span class="badge {{ $statusColor }} border border-opacity-25" style="font-size: 12px; padding: 6px 12px;">{{ $order->status }}</span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse-{{ $order->id }}" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                                <div class="accordion-body p-4 bg-white">
                                    
                                    <!-- Items list -->
                                    <h6 class="fw-bold mb-3 font-outfit small text-muted">Barang Belanjaan:</h6>
                                    <div class="mb-4">
                                        @foreach($order->items as $item)
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $item->variant->image_path ?: ($item->product->images->first()->image_path ?? '/assets/images/p-sd-kemeja-1.jpg') }}" alt="Product" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="m-0 fw-bold small">{{ $item->product->name }}</h6>
                                                        <small class="text-muted">Ukuran: {{ $item->variant->size }} | Warna: {{ $item->variant->color }} | {{ $item->quantity }} pcs</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="small fw-bold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                                    
                                                    <!-- Review Button if order is paid -->
                                                    @if($order->payment_status === 'Paid')
                                                        <button type="button" class="btn btn-sm btn-outline-danger py-1" onclick="openReviewModal({{ $item->product_id }}, '{{ $item->product->name }}')">
                                                            <i class="fa-regular fa-star me-1"></i> Ulas
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Shipping Resi Details -->
                                    <div class="bg-light p-3 rounded-3 mb-4 text-start small">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <span class="text-muted d-block mb-1">Kurir Pengiriman</span>
                                                <strong class="text-dark">{{ strtoupper($order->shipping_courier) }} - {{ $order->shipping_service }}</strong>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <span class="text-muted d-block mb-1">Nomor Resi (Tracking Number)</span>
                                                <strong class="text-danger">{{ $order->tracking_number ?: 'Sedang diproses admin' }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total billing details -->
                                    <div class="border-top pt-3 text-end">
                                        <p class="small text-muted mb-1">Subtotal: Rp {{ number_format($order->subtotal, 0, ',', '.') }} | Ongkir: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</p>
                                        <h5 class="fw-bold font-outfit text-danger mb-3">Grand Total: Rp {{ number_format($order->grand_total, 0, ',', '.') }}</h5>
                                        
                                        @if($order->payment_status === 'Unpaid')
                                            <a href="{{ route('checkout.payment', $order->id) }}" class="btn btn-danger btn-sm px-4 py-2 font-outfit fw-bold">Bayar Tagihan Sekarang</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fa-solid fa-cart-flatbed-suitcases display-4 text-muted mb-3 d-block"></i>
                            <h6 class="text-muted m-0">Anda belum memiliki transaksi pesanan.</h6>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-outfit fw-bold">Kirim Ulasan Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="review-form">
                <input type="hidden" name="product_id" id="review-product-id">
                <div class="modal-body p-4">
                    <div class="mb-3 text-center bg-light p-3 rounded border">
                        <span class="text-muted small d-block mb-1">Mengulas Produk:</span>
                        <strong class="text-dark fs-6" id="review-product-name">Product Name</strong>
                    </div>

                    <!-- Star Selector -->
                    <div class="mb-4 text-center">
                        <label class="form-label small fw-semibold d-block mb-2">Pilih Rating Bintang</label>
                        <div class="fs-2 text-warning d-inline-flex gap-2" style="cursor: pointer;">
                            <i class="fa-regular fa-star star-select-btn" data-rating="1" onclick="rateStars(1)"></i>
                            <i class="fa-regular fa-star star-select-btn" data-rating="2" onclick="rateStars(2)"></i>
                            <i class="fa-regular fa-star star-select-btn" data-rating="3" onclick="rateStars(3)"></i>
                            <i class="fa-regular fa-star star-select-btn" data-rating="4" onclick="rateStars(4)"></i>
                            <i class="fa-regular fa-star star-select-btn" data-rating="5" onclick="rateStars(5)"></i>
                        </div>
                        <input type="hidden" name="rating" id="review-rating-val" value="" required>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label small fw-semibold">Ulasan / Komentar Anda</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Bagikan tanggapan Anda mengenai kualitas bahan, jahitan, atau ukuran seragam ini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">Kirim Ulasan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Star rating selector function
    function rateStars(rating) {
        $('#review-rating-val').val(rating);
        $('.star-select-btn').each(function() {
            const currentStarRating = parseInt($(this).data('rating'));
            if (currentStarRating <= rating) {
                $(this).removeClass('fa-regular').addClass('fa-solid');
            } else {
                $(this).removeClass('fa-solid').addClass('fa-regular');
            }
        });
    }

    // Open review modal
    function openReviewModal(productId, productName) {
        $('#review-product-id').val(productId);
        $('#review-product-name').text(productName);
        $('#review-rating-val').val('');
        $('.star-select-btn').removeClass('fa-solid').addClass('fa-regular');
        
        // Reset comment text
        $('#review-form')[0].reset();
        
        $('#reviewModal').modal('show');
    }

    // Submit Review AJAX
    $('#review-form').on('submit', function(e) {
        e.preventDefault();

        const ratingVal = $('#review-rating-val').val();
        if (!ratingVal) {
            Swal.fire({
                icon: 'warning',
                title: 'Beri Bintang',
                text: 'Silakan pilih rating bintang terlebih dahulu!'
            });
            return;
        }

        $.ajax({
            url: "{{ route('review.submit') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                $('#reviewModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Terima Kasih!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let errMsg = 'Gagal mengirimkan ulasan.';
                if (xhr.status === 403) {
                    errMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errMsg
                });
            }
        });
    });
</script>
@endsection
