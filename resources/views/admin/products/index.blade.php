@extends('layouts.admin')

@section('page_title', 'Manajemen Produk & Variasi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="m-0 font-outfit fw-bold text-dark"><i class="fa-solid fa-shirt text-danger me-2"></i> Daftar Produk & Variasi</h5>
                <button type="button" class="btn btn-danger btn-sm px-4 py-2" onclick="openAddModal()">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Produk
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-premium" id="product-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Berat (gr)</th>
                                <th>Harga Dasar</th>
                                <th>Total Stok</th>
                                <th>Variasi Tersedia</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-outfit fw-bold" id="modal-title">Tambah Produk Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="product-form" enctype="multipart/form-data">
                <input type="hidden" name="id" id="product-id">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Left Column: Base Info -->
                        <div class="col-lg-5">
                            <h5 class="font-outfit text-danger mb-3 pb-2 border-bottom">Informasi Dasar</h5>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label small fw-semibold">Kategori</label>
                                <select name="category_id" id="product-category" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="product-name" class="form-label small fw-semibold">Nama Produk</label>
                                <input type="text" name="name" id="product-name" class="form-control" placeholder="Contoh: Kemeja SD Putih" required>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="product-weight" class="form-label small fw-semibold">Berat (gram)</label>
                                    <input type="number" name="weight" id="product-weight" class="form-control" placeholder="150" min="1" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="product-stock" class="form-label small fw-semibold">Stok Dasar</label>
                                    <input type="number" name="stock" id="product-stock" class="form-control" placeholder="100" min="0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="product-price" class="form-label small fw-semibold">Harga Dasar (Rp)</label>
                                <input type="number" name="price" id="product-price" class="form-control" placeholder="50000" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="product-description" class="form-label small fw-semibold">Deskripsi</label>
                                <textarea name="description" id="product-description" class="form-control" rows="4" placeholder="Detail produk..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="product-images" class="form-label small fw-semibold">Upload Gambar (Bisa Multi-Upload)</label>
                                <input type="file" name="images[]" id="product-images" class="form-control" multiple accept="image/*">
                                <div class="mt-2 d-flex flex-wrap gap-2" id="existing-images-wrapper">
                                    <!-- AJAX loaded sub-images here -->
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Variants configuration -->
                        <div class="col-lg-7">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h5 class="font-outfit text-danger m-0">Variasi Produk (Ukuran & Warna)</h5>
                                <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="addVariantRow()">
                                    <i class="fa-solid fa-plus me-1"></i> Tambah Variasi
                                </button>
                            </div>

                            <div class="table-responsive" style="max-height: 400px;">
                                <table class="table align-middle table-sm border">
                                    <thead class="table-light">
                                        <tr class="small">
                                            <th>Ukuran</th>
                                            <th>Warna</th>
                                            <th>Harga Tambahan (Rp)</th>
                                            <th>Stok</th>
                                            <th>Foto</th>
                                            <th class="text-center" style="width: 50px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variants-tbody">
                                        <!-- Dynamic Variant rows loaded by Javascript -->
                                    </tbody>
                                </table>
                            </div>
                            <span class="text-muted small d-block mt-2">Setiap variasi dapat diunggah foto tersendiri (Shopee click switch feature). Jika kosong, akan memakai foto produk utama.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4" id="btn-save-product">Simpan Produk</button>
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
    let variantRowIndex = 0;

    $(document).ready(function() {
        table = $('#product-table').DataTable({
            processing: true,
            ajax: "{{ url('/admin/products/data') }}",
            columns: [
                {
                    data: null,
                    render: function(data) {
                        return `<span class="fw-bold d-block">${data.name}</span><span class="text-muted small">${data.slug}</span>`;
                    }
                },
                { data: 'category.name' },
                { data: 'weight', className: 'text-end' },
                {
                    data: 'price',
                    className: 'text-end fw-semibold text-danger',
                    render: function(price) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
                    }
                },
                { data: 'stock', className: 'text-center' },
                {
                    data: 'variants',
                    render: function(variants) {
                        if (!variants || variants.length === 0) return '<span class="text-muted small">-</span>';
                        let list = '';
                        variants.forEach(v => {
                            list += `<span class="badge bg-light text-dark border me-1 my-1">${v.size} (${v.color}) - Rp ${new Intl.NumberFormat('id-ID').format(v.additional_price)}</span>`;
                        });
                        return list;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editProduct(${data.id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-outline-danger" onclick="deleteProduct(${data.id})" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                zeroRecords: "Produk belum ditambahkan",
                loadingRecords: "Memuat data produk...",
            }
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modal-title').text('Tambah Produk Baru');
        $('#product-id').val('');
        $('#product-form')[0].reset();
        $('#existing-images-wrapper').html('');
        $('#variants-tbody').html('');
        variantRowIndex = 0;
        
        // Add 1 default variant row automatically
        addVariantRow();
        
        $('#productModal').modal('show');
    }

    // Dynamic row constructor for variants
    function addVariantRow(data = null) {
        const sizeVal = data ? data.size : '';
        const colorVal = data ? data.color : '';
        const addPrice = data ? data.additional_price : 0;
        const stockVal = data ? data.stock : 10;
        const imgPath = data ? data.image_path : '';

        const row = `
            <tr id="variant-row-${variantRowIndex}">
                <td><input type="text" class="form-control form-control-sm var-size" value="${sizeVal}" placeholder="S/M/L/25" required></td>
                <td><input type="text" class="form-control form-control-sm var-color" value="${colorVal}" placeholder="Putih/Merah" required></td>
                <td><input type="number" class="form-control form-control-sm var-price text-end" value="${parseInt(addPrice)}" placeholder="0" required></td>
                <td><input type="number" class="form-control form-control-sm var-stock text-center" value="${stockVal}" placeholder="10" required></td>
                <td>
                    <input type="file" name="variant_image_${variantRowIndex}" class="form-control form-control-sm mb-1" accept="image/*">
                    ${imgPath ? `<img src="${imgPath}" class="rounded border d-block" style="width: 40px; height: 40px; object-fit: cover;">` : ''}
                    <input type="hidden" class="var-existing-img" value="${imgPath}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeVariantRow(${variantRowIndex})"><i class="fa-solid fa-trash-can"></i></button>
                </td>
            </tr>
        `;

        $('#variants-tbody').append(row);
        variantRowIndex++;
    }

    function removeVariantRow(index) {
        $(`#variant-row-${index}`).remove();
    }

    // Load product for editing
    function editProduct(id) {
        isEditMode = true;
        $('#modal-title').text('Edit Produk');
        $('#product-form')[0].reset();
        $('#existing-images-wrapper').html('');
        $('#variants-tbody').html('');
        variantRowIndex = 0;

        $.ajax({
            url: `/admin/products/show/${id}`,
            type: "GET",
            success: function(product) {
                $('#product-id').val(product.id);
                $('#product-category').val(product.category_id);
                $('#product-name').val(product.name);
                $('#product-weight').val(product.weight);
                $('#product-price').val(parseInt(product.price));
                $('#product-stock').val(product.stock);
                $('#product-description').val(product.description);

                // Render Sub images
                if (product.images && product.images.length > 0) {
                    product.images.forEach(img => {
                        $('#existing-images-wrapper').append(`
                            <div class="position-relative border rounded p-1" id="p-img-box-${img.id}">
                                <img src="${img.image_path}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-xs position-absolute top-0 start-100 translate-middle rounded-circle" onclick="deleteProductImage(${img.id})" style="padding: 1px 5px; font-size: 8px;"><i class="fa-solid fa-x"></i></button>
                            </div>
                        `);
                    });
                }

                // Render Variants
                if (product.variants && product.variants.length > 0) {
                    product.variants.forEach(v => {
                        addVariantRow(v);
                    });
                } else {
                    addVariantRow();
                }

                $('#productModal').modal('show');
            }
        });
    }

    // Delete sub-image inside edit
    function deleteProductImage(id) {
        $.ajax({
            url: `/admin/products/image/delete/${id}`,
            type: "DELETE",
            success: function() {
                $(`#p-img-box-${id}`).remove();
            }
        });
    }

    // Submit product via AJAX (Add & Edit)
    $('#product-form').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#product-id').val();
        const url = isEditMode ? `/admin/products/update/${id}` : "/admin/products/store";
        
        const btn = $('#btn-save-product');
        btn.prop('disabled', true);

        // Compile variant rows data into json input
        const variants = [];
        $('#variants-tbody tr').each(function(i, row) {
            const size = $(row).find('.var-size').val();
            const color = $(row).find('.var-color').val();
            const price = $(row).find('.var-price').val();
            const stock = $(row).find('.var-stock').val();
            const existingImg = $(row).find('.var-existing-img').val();

            if (size || color) {
                variants.push({
                    size: size,
                    color: color,
                    additional_price: price,
                    stock: stock,
                    image_path: existingImg
                });
            }
        });

        const formData = new FormData(this);
        formData.append('variants', JSON.stringify(variants));

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btn.prop('disabled', false);
                $('#productModal').modal('hide');
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
                let errMsg = 'Gagal menyimpan produk.';
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

    // Delete Product AJAX
    function deleteProduct(id) {
        Swal.fire({
            title: 'Hapus Produk?',
            text: "Apakah Anda yakin ingin menghapus produk ini beserta variasi dan reviewnya secara permanen?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/products/delete/${id}`,
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
                            text: 'Produk gagal dihapus.'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection
