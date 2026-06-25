@extends('layouts.app')

@section('content')
<!-- Hero Slider -->
<div class="container mb-5">
    <div id="heroCarousel" class="carousel slide rounded-4 overflow-hidden shadow-sm" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach($sliders as $index => $slider)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($sliders as $index => $slider)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}" data-bs-interval="4000">
                    <img src="{{ $slider->image_path }}" class="d-block w-100" alt="Slider" style="height: 450px; object-fit: cover; filter: brightness(0.95);">
                    <div class="carousel-caption d-none d-md-block text-start bg-dark bg-opacity-50 p-4 rounded-3" style="max-width: 500px; bottom: 50px; left: 80px;">
                        <h2 class="font-outfit text-white mb-2">{{ $slider->title }}</h2>
                        <p class="text-white-50 mb-3">{{ $slider->description }}</p>
                        @if($slider->link)
                            <a href="{{ $slider->link }}" class="btn btn-danger btn-sm px-4 py-2">Belanja Sekarang <i class="fa-solid fa-arrow-right ms-1"></i></a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<!-- Category Categories Grid -->
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-outfit fw-bold text-danger m-0">Kategori Seragam</h3>
            <p class="text-muted small mb-0">Cari seragam sekolah anak berdasarkan tingkat pendidikan</p>
        </div>
    </div>
    <div class="row row-cols-2 row-cols-md-4 g-3">
        @foreach($categories as $category)
            <div class="col">
                <a href="{{ route('home', ['category_id' => $category->id]) }}" class="text-decoration-none">
                    <div class="premium-card text-center p-3 h-100 border">
                        <div class="rounded-3 overflow-hidden mb-3 border" style="height: 140px;">
                            <img src="{{ $category->cover ?: '/assets/images/category-sd.jpg' }}" alt="{{ $category->name }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;">
                        </div>
                        <h6 class="font-outfit text-dark fw-bold m-0" style="font-size: 15px;">{{ $category->name }}</h6>
                        <span class="badge bg-light text-danger border border-danger border-opacity-25 mt-2" style="font-size: 11px;">Pilihan Lengkap</span>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

<!-- Search Results & Grid -->
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            @if($search)
                <h3 class="font-outfit fw-bold text-danger m-0">Hasil Pencarian: "{{ $search }}"</h3>
            @elseif($categoryId)
                @php $activeCat = $categories->firstWhere('id', $categoryId) @endphp
                <h3 class="font-outfit fw-bold text-danger m-0">Kategori: {{ $activeCat ? $activeCat->name : 'Produk' }}</h3>
            @else
                <h3 class="font-outfit fw-bold text-danger m-0">Koleksi Seragam Terbaru</h3>
            @endif
            <p class="text-muted small mb-0">Seragam sekolah standar kualitas jahitan premium</p>
        </div>
        
        <!-- Filter Actions -->
        @if($search || $categoryId)
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-danger px-3"><i class="fa-solid fa-rotate-left me-1"></i> Reset Filter</a>
        @endif
    </div>

    <!-- Product Grid -->
    <div class="row row-cols-2 row-cols-md-4 g-4">
        @forelse($products as $product)
            @php 
                $price = $product->price;
                $lowestVariant = $product->variants->sortBy('additional_price')->first();
                $highestVariant = $product->variants->sortByDesc('additional_price')->first();
                
                $priceRange = number_format($price, 0, ',', '.');
                if ($lowestVariant && $highestVariant && $lowestVariant->id !== $highestVariant->id) {
                    $priceRange = number_format($price + $lowestVariant->additional_price, 0, ',', '.') . ' - ' . number_format($price + $highestVariant->additional_price, 0, ',', '.');
                } elseif ($lowestVariant) {
                    $priceRange = number_format($price + $lowestVariant->additional_price, 0, ',', '.');
                }
            @endphp
            <div class="col">
                <div class="premium-card h-100 d-flex flex-column border">
                    <!-- Image Wrapper -->
                    <div class="position-relative overflow-hidden bg-light" style="height: 240px;">
                        <a href="{{ route('product.detail', $product->slug) }}">
                            <img src="{{ $product->images->first()->image_path ?? '/assets/images/p-sd-kemeja-1.jpg' }}" alt="{{ $product->name }}" class="w-100 h-100" style="object-fit: cover;">
                        </a>
                        <span class="position-absolute top-2 start-2 badge bg-danger text-uppercase font-outfit" style="font-size: 10px; padding: 5px 8px; letter-spacing: 0.5px;">Bestseller</span>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <a href="{{ route('product.detail', $product->slug) }}" class="text-decoration-none text-dark hover-text-danger">
                            <h6 class="font-outfit fw-bold text-truncate-2 mb-2" style="font-size: 15px; height: 38px; line-height: 1.3;">{{ $product->name }}</h6>
                        </a>
                        <div class="mt-auto">
                            <!-- Ratings and Sold count -->
                            <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 12px;">
                                <div class="text-warning">
                                    @php $ratingVal = round($product->average_rating) @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $ratingVal)
                                            <i class="fa-solid fa-star"></i>
                                        @else
                                            <i class="fa-regular fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-muted">| Terjual {{ $product->total_sold }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-danger fw-bold font-outfit" style="font-size: 16px;">
                                    Rp {{ $priceRange }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 pb-3">
                        <a href="{{ route('product.detail', $product->slug) }}" class="btn btn-premium btn-sm w-100">Beli Sekarang <i class="fa-solid fa-chevron-right ms-1" style="font-size: 10px;"></i></a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center my-5 py-5">
                <i class="fa-solid fa-magnifying-glass fs-1 text-muted mb-3 d-block"></i>
                <h5 class="fw-semibold text-muted">Produk Tidak Ditemukan</h5>
                <p class="text-muted small">Coba cari kata kunci seragam lainnya.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
        {{ $products->links() }}
    </div>
</div>
@endsection

@section('styles')
<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .hover-text-danger:hover {
        color: var(--primary-color) !important;
    }
    
    .carousel-item img {
        border-radius: 16px;
    }
    
    .position-absolute.top-2.start-2 {
        top: 10px !important;
        left: 10px !important;
    }
</style>
@endsection
