@extends('layouts.admin')

@section('page_title', 'Manajemen Kategori Produk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="m-0 font-outfit fw-bold text-dark"><i class="fa-solid fa-tags text-danger me-2"></i> Daftar Kategori Seragam</h5>
                <button type="button" class="btn btn-danger btn-sm px-4 py-2" onclick="openAddModal()">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-premium" id="category-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Cover</th>
                                <th>Nama Kategori</th>
                                <th>Slug</th>
                                <th class="text-center">Jumlah Produk</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-outfit fw-bold" id="modal-title">Tambah Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="category-form" enctype="multipart/form-data">
                <input type="hidden" name="id" id="category-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="category-name" class="form-label small fw-semibold">Nama Kategori</label>
                        <input type="text" name="name" id="category-name" class="form-control" placeholder="Contoh: Seragam SD" required>
                    </div>
                    <div class="mb-3">
                        <label for="category-cover" class="form-label small fw-semibold">Gambar Cover</label>
                        <input type="file" name="cover" id="category-cover" class="form-control" accept="image/*">
                        <div class="mt-2 text-center d-none" id="cover-preview-wrapper">
                            <img src="" id="cover-preview" class="rounded border" style="max-height: 120px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4" id="btn-save">Simpan Kategori</button>
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
        table = $('#category-table').DataTable({
            processing: true,
            ajax: "{{ url('/admin/categories/data') }}",
            columns: [
                {
                    data: 'cover',
                    orderable: false,
                    render: function(data) {
                        return `<img src="${data || '/assets/images/category-sd.jpg'}" class="rounded border" style="width: 80px; height: 50px; object-fit: cover;">`;
                    }
                },
                { data: 'name', className: 'fw-bold' },
                { data: 'slug', className: 'text-muted small' },
                {
                    data: 'products_count',
                    className: 'text-center fw-bold text-danger'
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editCategory(${data.id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-outline-danger" onclick="deleteCategory(${data.id})" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                zeroRecords: "Kategori belum ditambahkan",
                loadingRecords: "Memuat data kategori...",
            }
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modal-title').text('Tambah Kategori');
        $('#category-id').val('');
        $('#category-form')[0].reset();
        $('#cover-preview-wrapper').addClass('d-none');
        $('#categoryModal').modal('show');
    }

    function editCategory(id) {
        isEditMode = true;
        $('#modal-title').text('Edit Kategori');
        $('#category-form')[0].reset();
        
        $.ajax({
            url: `/admin/categories/show/${id}`,
            type: "GET",
            success: function(category) {
                $('#category-id').val(category.id);
                $('#category-name').val(category.name);
                
                if (category.cover) {
                    $('#cover-preview').attr('src', category.cover);
                    $('#cover-preview-wrapper').removeClass('d-none');
                } else {
                    $('#cover-preview-wrapper').addClass('d-none');
                }

                $('#categoryModal').modal('show');
            }
        });
    }

    // Submit AJAX Kategori (Add & Edit)
    $('#category-form').on('submit', function(e) {
        e.preventDefault();

        const id = $('#category-id').val();
        const url = isEditMode ? `/admin/categories/update/${id}` : "/admin/categories/store";
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
                $('#categoryModal').modal('hide');
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
                let errMsg = 'Gagal menyimpan kategori.';
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
    function deleteCategory(id) {
        Swal.fire({
            title: 'Hapus Kategori?',
            text: "Semua produk dalam kategori ini juga akan terpengaruh. Lanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/categories/delete/${id}`,
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
                            text: 'Kategori gagal dihapus.'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection
