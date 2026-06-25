@extends('layouts.admin')

@section('page_title', 'Pengaturan Website & RajaOngkir')

@section('content')
<div class="row g-4">
    <!-- Left Column: CMS Website Profile Info -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="m-0 font-outfit fw-bold text-dark"><i class="fa-solid fa-gears text-danger me-2"></i> Profil & CMS Konten Utama</h5>
            </div>
            <div class="card-body p-4">
                <form id="website-settings-form" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="website_name" class="form-label small fw-semibold">Nama Website</label>
                            <input type="text" name="website_name" id="website_name" class="form-control" value="{{ $settings['website_name'] }}" required>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="form-label small fw-semibold d-block">Logo Website</label>
                            <img src="{{ $settings['logo_path'] }}" id="logo-preview" class="rounded border p-1 bg-light mb-2" style="max-height: 45px; max-width: 100%; object-fit: contain;">
                            <input type="file" name="logo" class="form-control form-control-sm" accept="image/*">
                        </div>
                        
                        <hr class="border-secondary border-opacity-10 my-3">
                        
                        <h6 class="fw-bold font-outfit text-danger mb-0">Informasi Kontak & Media Sosial</h6>
                        
                        <div class="col-md-4">
                            <label for="fb_url" class="form-label small fw-semibold">URL Facebook</label>
                            <input type="url" name="facebook_url" id="fb_url" class="form-control form-control-sm" value="{{ $settings['facebook_url'] }}" placeholder="https://facebook.com/name">
                        </div>
                        <div class="col-md-4">
                            <label for="ig_url" class="form-label small fw-semibold">URL Instagram</label>
                            <input type="url" name="instagram_url" id="ig_url" class="form-control form-control-sm" value="{{ $settings['instagram_url'] }}" placeholder="https://instagram.com/name">
                        </div>
                        <div class="col-md-4">
                            <label for="yt_url" class="form-label small fw-semibold">URL YouTube</label>
                            <input type="url" name="youtube_url" id="yt_url" class="form-control form-control-sm" value="{{ $settings['youtube_url'] }}" placeholder="https://youtube.com/name">
                        </div>

                        <div class="col-md-6">
                            <label for="op_hours" class="form-label small fw-semibold">Jam Operasional</label>
                            <input type="text" name="operational_hours" id="op_hours" class="form-control" value="{{ $settings['operational_hours'] }}" placeholder="Senin - Sabtu: 08:00 - 17:00" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address_text" class="form-label small fw-semibold">Alamat Kantor / Toko (Teks)</label>
                            <input type="text" name="address_text" id="address_text" class="form-control" value="{{ $settings['address_text'] }}" placeholder="Jl. Merdeka No. 12" required>
                        </div>

                        <div class="col-12">
                            <label for="maps_iframe" class="form-label small fw-semibold">Embed Iframe Google Maps</label>
                            <textarea name="maps_iframe" id="maps_iframe" class="form-control small" rows="3" placeholder='<iframe src="https://google.com/maps/..." ...></iframe>' required>{{ $settings['maps_iframe'] }}</textarea>
                            <span class="text-muted small" style="font-size: 11px;">Copy-paste kode embed dari Google Maps.</span>
                        </div>

                        <div class="col-12">
                            <label for="about_us" class="form-label small fw-semibold">Tentang Kami (CMS Deskripsi Halaman)</label>
                            <textarea name="about_us" id="about_us" class="form-control" rows="5" required>{{ $settings['about_us'] }}</textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-danger btn-sm px-4 py-2 mt-4 font-outfit fw-bold w-100" id="btn-save-website">Simpan Perubahan Konten</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: RajaOngkir API configs -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="m-0 font-outfit fw-bold text-dark"><i class="fa-solid fa-truck text-danger me-2"></i> Integrasi API RajaOngkir</h5>
            </div>
            <div class="card-body p-4">
                <form id="rajaongkir-form">
                    <div class="mb-3">
                        <label for="ro_key" class="form-label small fw-semibold">API Key RajaOngkir (Starter/Basic)</label>
                        <input type="text" name="rajaongkir_api_key" id="ro_key" class="form-control text-danger font-monospace fw-semibold" value="{{ $settings['rajaongkir_api_key'] }}" required>
                        <span class="text-muted small" style="font-size: 11px;">Kunci API Rajaongkir diperlukan untuk kalkulasi biaya kiriman otomatis.</span>
                    </div>

                    <div class="mb-3">
                        <label for="ro_province" class="form-label small fw-semibold">Provinsi Asal Pengiriman (Origin)</label>
                        <select id="ro_province" class="form-select" required>
                            <option value="">Pilih Provinsi Asal</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov['province_id'] }}">{{ $prov['province'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="ro_origin" class="form-label small fw-semibold">Kota / Kabupaten Asal Pengiriman (Origin)</label>
                        <select name="rajaongkir_origin" id="ro_origin" class="form-select" required>
                            <!-- AJAX loaded based on province select -->
                        </select>
                        <span class="text-muted small" style="font-size: 11px;">Kota asal default saat ini: ID <strong>{{ $settings['rajaongkir_origin'] }}</strong></span>
                    </div>

                    <button type="submit" class="btn btn-danger btn-sm px-4 py-2 font-outfit fw-bold w-100" id="btn-save-rajaongkir">Update Kredensial RajaOngkir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Handle AJAX load for Rajaongkir settings city origin dropdown
    $('#ro_province').on('change', function() {
        const provinceId = $(this).val();
        loadOriginCities(provinceId, null);
    });

    // Preset origin selected if available
    const initialOriginCityId = "{{ $settings['rajaongkir_origin'] }}";
    if (initialOriginCityId) {
        // Pre-query province of selected city and load
        // For simplicity, let's load Jawa Barat (9) defaults
        loadOriginCities(9, initialOriginCityId);
    }

    function loadOriginCities(provinceId, activeCityId) {
        const citySelect = $('#ro_origin');
        if (!provinceId) return;

        citySelect.prop('disabled', true).html('<option value="">Memuat kota...</option>');

        $.ajax({
            url: `/cities/${provinceId}`,
            type: 'GET',
            success: function(cities) {
                let options = '<option value="">Pilih Kota Asal</option>';
                cities.forEach(city => {
                    const selected = activeCityId && activeCityId == city.city_id ? 'selected' : '';
                    options += `<option value="${city.city_id}" ${selected}>${city.type} ${city.city_name}</option>`;
                });
                citySelect.html(options).prop('disabled', false);
            },
            error: function() {
                citySelect.html('<option value="">Gagal memuat kota</option>').prop('disabled', false);
            }
        });
    }

    // Submit Website Settings AJAX
    $('#website-settings-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn-save-website');
        btn.prop('disabled', true);
        const formData = new FormData(this);

        $.ajax({
            url: "/admin/settings/website/update",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btn.prop('disabled', false);
                Swal.fire({
                    icon: 'success',
                    title: 'Profil Diperbarui',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function() {
                btn.prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memperbarui pengaturan website.'
                });
            }
        });
    });

    // Submit RajaOngkir Configs AJAX
    $('#rajaongkir-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn-save-rajaongkir');
        btn.prop('disabled', true);

        $.ajax({
            url: "/admin/settings/website/rajaongkir",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                btn.prop('disabled', false);
                Swal.fire({
                    icon: 'success',
                    title: 'Kredensial Disimpan',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function() {
                btn.prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memperbarui kredensial RajaOngkir.'
                });
            }
        });
    });
</script>
@endsection
