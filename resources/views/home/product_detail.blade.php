@extends('layouts.app')

@section('content')
<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('home', ['category_id' => $product->category->id]) }}" class="text-decoration-none text-muted">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Left Column: Image Gallery -->
        <div class="col-md-6">
            <div class="premium-card p-3 border">
                <!-- Large Display Image -->
                <div class="rounded-3 overflow-hidden border mb-3 Display-img-container" style="height: 480px; background-color: #f8fafc;">
                    <img src="{{ $product->images->first()->image_path ?? '/assets/images/p-sd-kemeja-1.jpg' }}" id="main-product-image" alt="{{ $product->name }}" class="w-100 h-100" style="object-fit: contain;">
                </div>

                <!-- Sub-images Row -->
                <div class="row g-2">
                    @foreach($product->images as $img)
                        <div class="col-3">
                            <div class="rounded-2 overflow-hidden border thumbnail-img-item" style="height: 80px; cursor: pointer;">
                                <img src="{{ $img->image_path }}" class="w-100 h-100 product-thumbnail" style="object-fit: cover;" onclick="switchMainImage('{{ $img->image_path }}')">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Product Configurations -->
        <div class="col-md-6">
            <h2 class="font-outfit fw-bold text-dark mb-2">{{ $product->name }}</h2>
            
            <!-- Ratings and Sold count -->
            <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-3">
                <div class="text-warning fs-5">
                    @php $ratingVal = round($product->average_rating) @endphp
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $ratingVal)
                            <i class="fa-solid fa-star"></i>
                        @else
                            <i class="fa-regular fa-star"></i>
                        @endif
                    @endfor
                </div>
                <span class="fs-6 fw-semibold text-danger">{{ $product->average_rating }} / 5.0</span>
                <span class="text-muted">|</span>
                <span class="text-muted fw-semibold">Terjual {{ $product->total_sold }} pcs</span>
            </div>

            <!-- Prices Box -->
            <div class="p-4 rounded-3 mb-4 bg-light" style="border-left: 5px solid var(--primary-color);">
                <div class="text-muted small mb-1">Harga Barang</div>
                <div class="fs-2 fw-bold text-danger font-outfit" id="display-price">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </div>
            </div>

            <!-- Details Attributes -->
            <table class="table table-borderless small mb-4">
                <tr>
                    <td class="text-muted py-1" style="width: 120px;">Kategori</td>
                    <td class="fw-semibold py-1">{{ $product->category->name }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-1">Berat Barang</td>
                    <td class="fw-semibold py-1">{{ $product->weight }} gram</td>
                </tr>
                <tr>
                    <td class="text-muted py-1">Total Stok</td>
                    <td class="fw-semibold py-1" id="display-stock">{{ $product->stock }} pcs</td>
                </tr>
            </table>

            <!-- Variant Selector Forms -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3 font-outfit">Pilih Variasi Seragam:</h6>
                <div class="d-flex flex-wrap gap-2 mb-3" id="variant-list">
                    @foreach($product->variants as $variant)
                        <button type="button" 
                                class="btn btn-outline-secondary variant-btn py-2 px-3 rounded-3 text-start d-flex align-items-center gap-2 border-opacity-75"
                                data-id="{{ $variant->id }}"
                                data-price="{{ $product->price + $variant->additional_price }}"
                                data-stock="{{ $variant->stock }}"
                                data-image="{{ $variant->image_path ?: ($product->images->first()->image_path ?? '/assets/images/p-sd-kemeja-1.jpg') }}"
                                onclick="selectVariant(this)">
                            @if($variant->image_path)
                                <img src="{{ $variant->image_path }}" class="rounded-circle border" style="width: 24px; height: 24px; object-fit: cover;">
                            @endif
                            <div class="small">
                                <strong class="d-block">{{ $variant->size }} ({{ $variant->color }})</strong>
                                <span class="text-muted text-nowrap" style="font-size: 11px;">+ Rp {{ number_format($variant->additional_price, 0, ',', '.') }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Quantity & Add to Cart form -->
            <form id="add-to-cart-form">
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="variant_id" id="selected-variant-id" value="">

                <div class="row align-items-center g-3 mb-4">
                    <div class="col-4 col-md-3">
                        <label for="quantity" class="form-label small fw-semibold mb-0">Jumlah</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary py-1" onclick="adjustQty(-1)"><i class="fa-solid fa-minus" style="font-size: 10px;"></i></button>
                            <input type="number" name="quantity" id="quantity" class="form-control text-center py-1 fw-bold" value="1" min="1" required>
                            <button type="button" class="btn btn-outline-secondary py-1" onclick="adjustQty(1)"><i class="fa-solid fa-plus" style="font-size: 10px;"></i></button>
                        </div>
                    </div>
                    <div class="col-8 col-md-9 align-self-end">
                        <button type="submit" class="btn btn-premium w-100 py-2.5" id="btn-add-cart">
                            <i class="fa-solid fa-cart-plus me-2"></i> Masukkan Keranjang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Description & Reviews tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card premium-card border">
                <div class="card-header bg-white border-bottom p-0">
                    <nav class="nav nav-tabs border-bottom-0 px-4" id="nav-tab" role="tablist">
                        <button class="nav-link py-3 active border-0 text-danger" id="nav-desc-tab" data-bs-toggle="tab" data-bs-target="#nav-desc" type="button">Deskripsi Produk</button>
                        <button class="nav-link py-3 border-0 text-danger" id="nav-reviews-tab" data-bs-toggle="tab" data-bs-target="#nav-reviews" type="button">Ulasan Pembeli ({{ $product->reviews->count() }})</button>
                    </nav>
                </div>
                <div class="card-body p-4 tab-content" id="nav-tabContent">
                    <!-- Description -->
                    <div class="tab-pane fade show active" id="nav-desc" role="tabpanel">
                        <p style="line-height: 1.7; text-align: justify;">{!! nl2br(e($product->description)) !!}</p>
                    </div>
                    <!-- Reviews -->
                    <div class="tab-pane fade" id="nav-reviews" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-4 mb-4 border-end">
                                <div class="text-center py-4">
                                    <h1 class="display-3 fw-bold text-danger m-0">{{ $product->average_rating }}</h1>
                                    <div class="text-warning fs-4 my-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $ratingVal)
                                                <i class="fa-solid fa-star"></i>
                                            @else
                                                <i class="fa-regular fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="text-muted small m-0">Rata-rata rating dari pembeli</p>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <h5 class="fw-bold mb-4 font-outfit">Semua Ulasan</h5>
                                @forelse($product->reviews as $review)
                                    <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
                                        <img src="{{ $review->user->avatar ?: '/assets/images/avatar.png' }}" class="rounded-circle border align-self-start" style="width: 48px; height: 48px; object-fit: cover;">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h6 class="m-0 fw-semibold">{{ $review->user->name }}</h6>
                                                <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                                            </div>
                                            <div class="text-warning small mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fa-solid fa-star"></i>
                                                    @else
                                                        <i class="fa-regular fa-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <p class="text-muted small mb-0">{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fa-regular fa-comment-dots fs-1 text-muted mb-3 d-block"></i>
                                        <p class="text-muted m-0">Belum ada ulasan untuk produk ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Click sub-images to switch main image
    function switchMainImage(path) {
        $('#main-product-image').attr('src', path);
    }

    // Select variant options
    function selectVariant(element) {
        // Remove active class from all variant buttons
        $('.variant-btn').removeClass('active border-danger').addClass('border-secondary');
        
        // Add active classes to selected button
        $(element).addClass('active border-danger').removeClass('border-secondary');

        // Extract data properties
        const variantId = $(element).data('id');
        const price = $(element).data('price');
        const stock = $(element).data('stock');
        const image = $(element).data('image');

        // Set inputs & fields
        $('#selected-variant-id').val(variantId);
        
        // Format price and update displays
        const formattedPrice = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(price).replace('IDR', 'Rp');

        $('#display-price').text(formattedPrice);
        $('#display-stock').text(stock + ' pcs');

        // Switch main display image to variant image (Shopee feature)
        if (image) {
            switchMainImage(image);
        }
    }

    // Adjust quantities
    function adjustQty(amount) {
        const qtyInput = $('#quantity');
        let currentVal = parseInt(qtyInput.val());
        if (!isNaN(currentVal)) {
            let newVal = currentVal + amount;
            if (newVal >= 1) {
                qtyInput.val(newVal);
            }
        }
    }

    // Handle AJAX Add to Cart Submission
    $('#add-to-cart-form').on('submit', function(e) {
        e.preventDefault();

        // Ensure variant is selected
        const selectedVariant = $('#selected-variant-id').val();
        if (!selectedVariant) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Variasi',
                text: 'Silakan pilih ukuran dan warna seragam terlebih dahulu!'
            });
            return;
        }

        const btn = $('#btn-add-cart');
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('cart.add') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                btn.prop('disabled', false);
                
                // Update navigation badge count
                $('#cart-badge-count').text(response.cart_count);

                Swal.fire({
                    icon: 'success',
                    title: 'Ditambahkan ke Keranjang',
                    text: response.message,
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Lihat Keranjang',
                    cancelButtonText: 'Lanjut Belanja'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('cart.index') }}";
                    }
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal menambahkan ke keranjang. Silakan coba lagi.'
                });
            }
        });
    });
</script>
@endsection
