@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-lg-8 col-md-10">
            <div class="card premium-card border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <img src="{{ \App\Models\Setting::getValue('logo_path', '/assets/images/logo.png') }}" alt="Logo" class="img-fluid mb-3" style="max-height: 55px;">
                        <h4 class="font-outfit fw-bold text-danger">Buat Akun Customer</h4>
                        <p class="text-muted small">Lengkapi formulir di bawah ini untuk mulai berbelanja seragam</p>
                    </div>

                    <form id="register-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <h5 class="font-outfit text-danger mb-3 pb-2 border-bottom">Informasi Dasar</h5>

                                <div class="mb-3">
                                    <label for="name" class="form-label small fw-semibold">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" placeholder="Nama Lengkap Anda" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label small fw-semibold">Alamat Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="contoh@email.com" required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label small fw-semibold">Nomor Telepon/WA</label>
                                    <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx" required>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label for="password" class="form-label small fw-semibold">Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="******" required>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="password_confirmation" class="form-label small fw-semibold">Konfirmasi</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="******" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="avatar" class="form-label small fw-semibold">Foto Profil</label>
                                    <input type="file" name="avatar" class="form-control" accept="image/*">
                                    <span class="text-muted small" style="font-size: 11px;">Maksimal ukuran file gambar 2MB.</span>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <h5 class="font-outfit text-danger mb-3 pb-2 border-bottom">Alamat Pengiriman</h5>

                                <div class="mb-3">
                                    <label for="province_id" class="form-label small fw-semibold">Provinsi</label>
                                    <select name="province_id" id="province_select" class="form-select" required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinces as $prov)
                                            <option value="{{ $prov['id'] }}">{{ $prov['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="province_name" id="province_name">
                                </div>

                                <div class="mb-3">
                                    <label for="city_id" class="form-label small fw-semibold">Kota / Kabupaten</label>
                                    <select name="city_id" id="city_select" class="form-select" disabled required>
                                        <option value="">Pilih Kota / Kabupaten</option>
                                    </select>
                                    <input type="hidden" name="city_name" id="city_name">
                                </div>

                                <div class="mb-3">
                                    <label for="postal_code" class="form-label small fw-semibold">Kode Pos</label>
                                    <input type="text" name="postal_code" id="postal_code" class="form-control" placeholder="Kode Pos" required>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label small fw-semibold">Alamat Lengkap</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Nama Jalan, No. Rumah, RT/RW, Kecamatan/Kelurahan" required></textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-premium w-100 py-2.5 mt-4" id="btn-register">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                            Daftar Sekarang
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="small text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="text-danger fw-semibold text-decoration-none">Masuk di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Handle dynamic Province -> City loading
    $('#province_select').on('change', function() {
        const provinceId = $(this).val();
        const provinceText = $(this).find('option:selected').text();
        $('#province_name').val(provinceText);

        const citySelect = $('#city_select');

        if (provinceId) {
            citySelect.prop('disabled', true).html('<option value="">Memuat kota...</option>');

            $.ajax({
                url: `/cities/${provinceId}`,
                type: 'GET',
                success: function(cities) {
                    let options = '<option value="">Pilih Kota / Kabupaten</option>';
                    cities.forEach(city => {
                        options += `<option value="${city.id}" data-postal="">${city.name}</option>`;
                    });
                    citySelect.html(options).prop('disabled', false);
                },
                error: function() {
                    citySelect.html('<option value="">Gagal memuat kota</option>').prop('disabled', false);
                }
            });
        } else {
            citySelect.html('<option value="">Pilih Kota / Kabupaten</option>').prop('disabled', true);
            $('#province_name').val('');
        }
    });

    // Auto set postal code and city name on city select
    $('#city_select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const cityName = selectedOption.text();
        const postalCode = selectedOption.data('postal');

        $('#city_name').val(cityName);
        if (postalCode) {
            $('#postal_code').val(postalCode);
        }
    });

    // Handle AJAX Register Submit
    $('#register-form').on('submit', function(e) {
        e.preventDefault();

        const btn = $('#btn-register');
        const spinner = btn.find('.spinner-border');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('register') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Daftar Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = response.redirect;
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');

                let errMsg = 'Gagal mendaftar. Periksa inputan Anda.';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errMsg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi Gagal',
                    html: errMsg
                });
            }
        });
    });
</script>
@endsection
