@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-lg-5 col-md-7">
            <div class="card premium-card border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="{{ \App\Models\Setting::getValue('logo_path', '/assets/images/logo.png') }}" alt="Logo" class="img-fluid mb-3" style="max-height: 60px;">
                        <h4 class="font-outfit fw-bold text-danger">Selamat Datang Kembali</h4>
                        <p class="text-muted small">Silakan masuk ke akun Ecommerce Seragam Anda</p>
                    </div>

                    <form id="login-form">
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                                <input type="email" name="email" id="email" class="form-control bg-light border-0 py-2.5" placeholder="contoh@email.com" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label small fw-semibold">Password</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                <input type="password" name="password" id="password" class="form-control bg-light border-0 py-2.5" placeholder="******" required>
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                        </div>

                        <button type="submit" class="btn btn-premium w-100 py-2.5" id="btn-login">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                            Masuk Ke Akun
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="small text-muted">Belum punya akun? <a href="{{ route('register') }}" class="text-danger fw-semibold text-decoration-none">Daftar Sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $('#btn-login');
        const spinner = btn.find('.spinner-border');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: "{{ route('login') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
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

                let errMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: errMsg
                });
            }
        });
    });
</script>
@endsection
