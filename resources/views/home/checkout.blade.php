@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h3 class="font-outfit fw-bold text-danger mb-4">Checkout Pesanan</h3>

    <form id="checkout-form">
        @csrf
        <div class="row g-4">
            <!-- Left: Shipping Address & Courier -->
            <div class="col-lg-7">
                <!-- Address Section -->
                <div class="card premium-card border p-4 mb-4">
                    <h5 class="font-outfit fw-bold text-danger mb-4 border-bottom pb-2"><i class="fa-solid fa-location-dot me-2"></i> Alamat Pengiriman</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="province_select" class="form-label small fw-semibold">Provinsi</label>
                            <select name="province_id" id="province_select" class="form-select" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov['province_id'] }}" {{ $user->province_id == $prov['province_id'] ? 'selected' : '' }}>{{ $prov['province'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="province" id="province_name" value="{{ $user->province }}">
                        </div>

                        <div class="col-md-6">
                            <label for="city_select" class="form-label small fw-semibold">Kota / Kabupaten</label>
                            <select name="city_id" id="city_select" class="form-select" required>
                                <option value="{{ $user->city_id }}">{{ $user->city }}</option>
                            </select>
                            <input type="hidden" name="city" id="city_name" value="{{ $user->city }}">
                        </div>

                        <div class="col-12">
                            <label for="address_details" class="form-label small fw-semibold">Alamat Lengkap</label>
                            <textarea name="address_details" id="address_details" class="form-control" rows="3" placeholder="Nama Jalan, Blok, No. Rumah, RT/RW, Kecamatan/Kelurahan" required>{{ $user->address }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Courier Section -->
                <div class="card premium-card border p-4 mb-4">
                    <h5 class="font-outfit fw-bold text-danger mb-4 border-bottom pb-2"><i class="fa-solid fa-truck me-2"></i> Opsi Pengiriman (RajaOngkir)</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="courier_select" class="form-label small fw-semibold">Pilih Kurir Ekspedisi</label>
                            <select name="courier" id="courier_select" class="form-select" required>
                                <option value="">-- Pilih Kurir --</option>
                                <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                                <option value="tiki">TIKI (Titipan Kilat)</option>
                                <option value="pos">POS Indonesia</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="service_select" class="form-label small fw-semibold">Layanan Pengiriman</label>
                            <select name="service" id="service_select" class="form-select" disabled required>
                                <option value="">-- Pilih Layanan --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Loader for shipping rates -->
                    <div id="shipping-loader" class="text-center py-4 d-none">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted small mt-2">Menghitung ongkos kirim RajaOngkir...</p>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card premium-card border p-4">
                    <h5 class="font-outfit fw-bold text-danger mb-3 border-bottom pb-2"><i class="fa-solid fa-pen me-2"></i> Catatan Pesanan</h5>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan untuk pengemasan atau penjual (opsional)"></textarea>
                </div>
            </div>

            <!-- Right: Order Summary & Review -->
            <div class="col-lg-5">
                <div class="card premium-card border p-4 shadow-sm">
                    <h5 class="font-outfit fw-bold border-bottom pb-3 mb-3">Ringkasan Pembayaran</h5>

                    <!-- Cart Review list -->
                    <div class="mb-4">
                        <h6 class="small fw-bold text-muted mb-3">Detail Barang Belanjaan:</h6>
                        @php
                            $subtotal = 0;
                            $totalWeight = 0;
                        @endphp
                        @foreach($cart as $item)
                            @php
                                $itemSub = $item['price'] * $item['quantity'];
                                $subtotal += $itemSub;
                                $totalWeight += $item['weight'] * $item['quantity'];
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $item['image'] }}" alt="Product" class="rounded border" style="width: 45px; height: 45px; object-fit: cover;">
                                    <div>
                                        <div class="small fw-bold text-truncate" style="max-width: 180px;">{{ $item['name'] }}</div>
                                        <span class="text-muted" style="font-size: 11px;">Ukuran: {{ $item['size'] }} | {{ $item['quantity'] }}x</span>
                                    </div>
                                </div>
                                <span class="small fw-semibold text-dark">Rp {{ number_format($itemSub, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Computations summary -->
                    <div class="border-top pt-3 mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Total Subtotal</span>
                            <span class="fw-semibold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Total Berat Paket</span>
                            <span class="fw-semibold text-dark">{{ $totalWeight }} gram</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted small">Ongkos Kirim</span>
                            <span class="fw-bold text-danger" id="summary-ongkir">Rp 0</span>
                        </div>
                        <hr class="border-secondary border-opacity-10 my-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold font-outfit m-0">Grand Total</h6>
                            <h4 class="text-danger fw-bold font-outfit m-0" id="summary-grand-total">Rp {{ number_format($subtotal, 0, ',', '.') }}</h4>
                        </div>
                    </div>

                    <!-- Hidden Inputs for logic -->
                    <input type="hidden" name="shipping_cost" id="shipping_cost_val" value="0">
                    <input type="hidden" id="weight_val" value="{{ $totalWeight }}">
                    <input type="hidden" id="subtotal_val" value="{{ $subtotal }}">

                    <button type="submit" class="btn btn-premium w-100 py-3 font-outfit fw-bold" id="btn-submit-order" disabled>
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        Bayar Sekarang <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const subtotal = parseInt($('#subtotal_val').val());

    // Load cities initially based on user's province selection
    const initialProvId = "{{ $user->province_id }}";
    if (initialProvId) {
        loadCities(initialProvId, "{{ $user->city_id }}");
    }

    // Handle dynamic Province change
    $('#province_select').on('change', function() {
        const provinceId = $(this).val();
        const provinceText = $(this).find('option:selected').text();
        $('#province_name').val(provinceText);
        
        loadCities(provinceId, null);
        resetShipping();
    });

    // Handle city change
    $('#city_select').on('change', function() {
        const cityText = $(this).find('option:selected').text();
        $('#city_name').val(cityText);
        resetShipping();
    });

    // Trigger API call when courier is selected
    $('#courier_select').on('change', function() {
        const courier = $(this).val();
        const destination = $('#city_select').val();
        const weight = $('#weight_val').val();
        const serviceSelect = $('#service_select');

        if (!destination) {
            Swal.fire({
                icon: 'warning',
                title: 'Alamat Belum Lengkap',
                text: 'Silakan pilih Provinsi dan Kota tujuan terlebih dahulu.'
            });
            $(this).val('');
            return;
        }

        if (courier) {
            serviceSelect.prop('disabled', true).html('<option value="">-- Pilih Layanan --</option>');
            $('#shipping-loader').removeClass('d-none');
            
            $.ajax({
                url: '/checkout/cost',
                type: 'POST',
                data: {
                    destination: destination,
                    weight: weight,
                    courier: courier
                },
                success: function(response) {
                    $('#shipping-loader').addClass('d-none');
                    if (response.success && response.costs.length > 0) {
                        let options = '<option value="">-- Pilih Layanan --</option>';
                        response.costs.forEach(cost => {
                            const val = cost.cost[0].value;
                            const etd = cost.cost[0].etd;
                            options += `<option value="${cost.service}" data-cost="${val}">${cost.service} (${cost.description}) - Rp ${new Intl.NumberFormat('id-ID').format(val)} [${etd}]</option>`;
                        });
                        serviceSelect.html(options).prop('disabled', false);
                    } else {
                        serviceSelect.html('<option value="">Layanan tidak tersedia</option>').prop('disabled', false);
                    }
                },
                error: function() {
                    $('#shipping-loader').addClass('d-none');
                    serviceSelect.html('<option value="">Gagal mengambil ongkir</option>').prop('disabled', false);
                }
            });
        } else {
            resetShipping();
        }
    });

    // When specific service is selected
    $('#service_select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const cost = parseInt(selectedOption.data('cost'));

        if (!isNaN(cost)) {
            // Update Summary cost & grand total
            $('#shipping_cost_val').val(cost);
            $('#summary-ongkir').text('Rp ' + new Intl.NumberFormat('id-ID').format(cost));
            
            const grandTotal = subtotal + cost;
            $('#summary-grand-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal));

            // Enable Order button
            $('#btn-submit-order').prop('disabled', false);
        } else {
            resetShipping();
        }
    });

    // Helper functions
    function loadCities(provinceId, activeCityId) {
        const citySelect = $('#city_select');
        if (!provinceId) return;

        citySelect.prop('disabled', true).html('<option value="">Memuat kota...</option>');

        $.ajax({
            url: `/cities/${provinceId}`,
            type: 'GET',
            success: function(cities) {
                let options = '<option value="">Pilih Kota / Kabupaten</option>';
                cities.forEach(city => {
                    const selected = activeCityId && activeCityId == city.city_id ? 'selected' : '';
                    options += `<option value="${city.city_id}" data-postal="${city.postal_code}" ${selected}>${city.type} ${city.city_name}</option>`;
                });
                citySelect.html(options).prop('disabled', false);
            },
            error: function() {
                citySelect.html('<option value="">Gagal memuat kota</option>').prop('disabled', false);
            }
        });
    }

    function resetShipping() {
        $('#courier_select').val('');
        $('#service_select').html('<option value="">-- Pilih Layanan --</option>').prop('disabled', true);
        $('#shipping_cost_val').val(0);
        $('#summary-ongkir').text('Rp 0');
        $('#summary-grand-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
        $('#btn-submit-order').prop('disabled', true);
    }

    // Process Checkout AJAX
    $('#checkout-form').on('submit', function(e) {
        e.preventDefault();

        const btn = $('#btn-submit-order');
        const spinner = btn.find('.spinner-border');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: "{{ route('checkout.process') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pesanan Dibuat',
                        text: 'Silakan lanjutkan ke halaman pembayaran.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.payment_url;
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                
                let errMsg = 'Terjadi kesalahan saat memproses pesanan.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
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
