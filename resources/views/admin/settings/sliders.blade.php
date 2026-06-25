@extends('layouts.admin')

@section('page_title', 'Pengaturan Slider Halaman Depan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="m-0 font-outfit fw-bold text-dark"><i class="fa-solid fa-images text-danger me-2"></i> Daftar Slider Banner</h5>
                <button type="button" class="btn btn-danger btn-sm px-4 py-2" onclick="openAddModal()">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Banner Slider
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-premium" id="slider-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 250px;">Gambar Slider</th>
                                <th>Judul Banner</th>
                                <th>Subdeskripsi</th>
                                <th>Link Halaman</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Slider Modal -->
<div class="modal fade" id="sliderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-outfit fw-bold" id="modal-title">Tambah Slider Banner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="slider-form" enctype="multipart/form-data">
                <input type="hidden" name="id" id="slider-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="slider-image" class="form-label small fw-semibold">Pilih File Banner (Rasio 1200 x 450)</label>
                        <input type="file" name="image" id="slider-image" class="form-control" accept="image/*">
                        <div class="mt-2 text-center d-none" id="preview-wrapper">
                            <img src="" id="slider-preview" class="rounded border w-100" style="max-height: 150px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="slider-headline" class="form-label small fw-semibold">Judul Banner (Headline)</label>
                        <input type="text" name="title" id="slider-headline" class="form-control" placeholder="Contoh: Diskon Bundling 20%">
                    </div>
                    <div class="mb-3">
                        <label for="slider-desc" class="form-label small fw-semibold">Subdeskripsi / Promo Detail</label>
                        <input type="text" name="description" id="slider-desc" class="form-control" placeholder="Contoh: Dapatkan seragam sekolah lengkap berkualitas">
                    </div>
                    <div class="mb-3">
                        <label for="slider-link" class="form-label small fw-semibold">Link Tombol (URL Target)</label>
                        <input type="text" name="link" id="slider-link" class="form-control" placeholder="Contoh: /products atau link khusus">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4" id="btn-save">Simpan Slider</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let table;
    let isEditMode = false;

    $(document).ready(function() {
        table = $('#slider-table').DataTable({
            processing: true,
            ajax: "{{ url('/admin/settings/sliders/data') }}",
            columns: [
                {
                    data: 'image_path',
                    orderable: false,
                    render: function(data) {
                        return `<img src="${data}" class="rounded border" style="width: 220px; height: 80px; object-fit: cover;">`;
                    }
                },
                { data: 'title', className: 'fw-bold' },
                { data: 'description', className: 'text-muted small' },
                {
                    data: 'link',
                    render: function(link) {
                        return link ? `<code class="text-danger">${link}</code>` : '<span class="text-muted small">-</span>';
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editSlider(${data.id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-outline-danger" onclick="deleteSlider(${data.id})" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                zeroRecords: "Slider belum ditambahkan",
                loadingRecords: "Memuat data banner...",
            }
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modal-title').text('Tambah Slider Banner');
        $('#slider-id').val('');
        $('#slider-form')[0].reset();
        $('#preview-wrapper').addClass('d-none');
        $('#slider-image').prop('required', true); // image required on create
        $('#sliderModal').modal('show');
    }

    function editSlider(id) {
        isEditMode = true;
        $('#modal-title').text('Edit Slider Banner');
        $('#slider-form')[0].reset();
        $('#slider-image').prop('required', false); // image optional on edit
        
        $.ajax({
            url: `/admin/settings/sliders/show/${id}`,
            type: "GET",
            success: function(slider) {
                $('#slider-id').val(slider.id);
                $('#slider-headline').val(slider.title);
                $('#slider-desc').val(slider.description);
                $('#slider-link').val(slider.link);
                
                if (slider.image_path) {
                    $('#slider-preview').attr('src', slider.image_path);
                    $('#preview-wrapper').removeClass('d-none');
                } else {
                    $('#preview-wrapper').addClass('d-none');
                }

                $('#sliderModal').modal('show');
            }
        });
    }

    // Submit AJAX Kategori (Add & Edit)
    $('#slider-form').on('submit', function(e) {
        e.preventDefault();

        const id = $('#slider-id').val();
        const url = isEditMode ? `/admin/settings/sliders/update/${id}` : "/admin/settings/sliders/store";
        const formData = new FormData(this);

        const btn = $('#btn-save');
        btn.prop('disabled', true);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btn.prop('disabled', false);
                $('#sliderModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 1000,
                    showConfirmButton: false
                });
                table.ajax.reload();
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                let errMsg = 'Gagal menyimpan banner.';
                if (xhr.status === 422) {
                    errMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: errMsg
                });
            }
        });
    });

    // Delete Kategori AJAX
    function deleteSlider(id) {
        Swal.fire({
            title: 'Hapus Slider?',
            text: "Apakah Anda yakin ingin menghapus banner slider ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/settings/sliders/delete/${id}`,
                    type: "DELETE",
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus',
                            text: response.message,
                            timer: 1000,
                            showConfirmButton: false
                        });
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Slider gagal dihapus.'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection
