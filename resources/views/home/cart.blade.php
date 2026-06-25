@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h3 class="font-outfit fw-bold text-danger mb-4">Keranjang Belanja</h3>

    <div class="row g-4">
        <!-- Left: Cart Items List -->
        <div class="col-lg-8">
            <div class="card premium-card border p-3">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th>Produk</th>
                                <th class="text-center" style="width: 150px;">Jumlah</th>
                                <th class="text-end" style="width: 150px;">Subtotal</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cart as $key => $item)
                                <tr id="cart-row-{{ $key }}" class="cart-item-row">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $item['image'] }}" alt="Product" class="rounded border" style="width: 70px; height: 70px; object-fit: cover;">
                                            <div>
                                                <h6 class="m-0 fw-bold small text-truncate" style="max-width: 250px;">{{ $item['name'] }}</h6>
                                                <span class="badge bg-light text-secondary border mt-1">Ukuran: {{ $item['size'] }} | Warna: {{ $item['color'] }}</span>
                                                <div class="text-danger fw-semibold small mt-1">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity('{{ $key }}', -1)"><i class="fa-solid fa-minus" style="font-size: 9px;"></i></button>
                                            <input type="number" id="qty-input-{{ $key }}" class="form-control text-center fw-bold qty-input" value="{{ $item['quantity'] }}" min="1" readonly>
                                            <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity('{{ $key }}', 1)"><i class="fa-solid fa-plus" style="font-size: 9px;"></i></button>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-dark font-outfit">
                                        Rp <span id="item-total-{{ $key }}">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none" onclick="removeItem('{{ $key }}')">
                                            <i class="fa-solid fa-trash-can fs-5"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fa-solid fa-cart-shopping display-3 text-muted mb-3 d-block"></i>
                                        <h5 class="fw-semibold text-muted">Keranjang Belanja Kosong</h5>
                                        <p class="text-muted small mb-4">Anda belum memasukkan produk seragam apapun ke keranjang.</p>
                                        <a href="{{ route('home') }}" class="btn btn-premium btn-sm px-4">Mulai Belanja</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Summary Card -->
        @if(count($cart) > 0)
            <div class="col-lg-4">
                <div class="card premium-card border p-4">
                    <h5 class="font-outfit fw-bold border-bottom pb-3 mb-3">Ringkasan Belanja</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Total Barang</span>
                        <span class="fw-semibold small" id="summary-count">{{ count($cart) }} macam</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 border-bottom pb-2">
                        <span class="text-muted small">Total Berat</span>
                        <span class="fw-semibold small"><span id="summary-weight">
                            @php
                                $totalWeight = 0;
                                $subtotal = 0;
                                foreach($cart as $item) {
                                    $totalWeight += $item['weight'] * $item['quantity'];
                                    $subtotal += $item['price'] * $item['quantity'];
                                }
                                echo $totalWeight;
                            @endphp
                        </span> gram</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="m-0 fw-bold font-outfit">Total Harga</h6>
                        <h4 class="text-danger fw-bold font-outfit m-0">Rp <span id="summary-subtotal">{{ number_format($subtotal, 0, ',', '.') }}</span></h4>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn btn-premium w-100 py-2.5">
                        Lanjut ke Checkout <i class="fa-solid fa-credit-card ms-2"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // AJAX Quantity Updates
    function updateQuantity(key, amount) {
        const input = $(`#qty-input-${key}`);
        let currentQty = parseInt(input.val());
        let newQty = currentQty + amount;

        if (newQty < 1) return;

        input.val(newQty);

        $.ajax({
            url: "{{ route('cart.update') }}",
            type: "POST",
            data: {
                variant_id: key,
                quantity: newQty
            },
            success: function(response) {
                // Update item total price text
                $(`#item-total-${key}`).text(response.item_total);
                // Update checkout summary
                $('#summary-subtotal').text(response.subtotal);
                $('#summary-weight').text(response.total_weight);
            },
            error: function() {
                input.val(currentQty); // revert if failed
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memperbarui jumlah barang.'
                });
            }
        });
    }

    // AJAX Item Removal
    function removeItem(key) {
        Swal.fire({
            title: 'Hapus Barang?',
            text: "Apakah Anda yakin ingin menghapus produk ini dari keranjang belanja?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('cart.remove') }}",
                    type: "POST",
                    data: {
                        variant_id: key
                    },
                    success: function(response) {
                        // Remove element with slideUp transition
                        $(`#cart-row-${key}`).slideUp(300, function() {
                            $(this).remove();
                            // If cart becomes empty, reload to show empty state
                            if (response.cart_count === 0) {
                                window.location.reload();
                            }
                        });

                        // Update header count badge
                        $('#cart-badge-count').text(response.cart_count);
                        // Update summary values
                        $('#summary-count').text(response.cart_count + ' macam');
                        $('#summary-subtotal').text(response.subtotal);
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menghapus produk.'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection
