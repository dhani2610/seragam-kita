@extends('layouts.admin')

@section('page_title', 'Manajemen Customer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="m-0 font-outfit fw-bold text-dark"><i class="fa-solid fa-users text-danger me-2"></i> Daftar Customer Terdaftar</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-premium" id="customer-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Foto</th>
                                <th>Nama</th>
                                <th>Kontak</th>
                                <th>Alamat Pengiriman</th>
                                <th class="text-center">Total Order</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-outfit fw-bold">Edit Data Customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="customer-form">
                <input type="hidden" name="id" id="customer-id">
                <div class="modal-body p-4">
                    <div class="mb-3 text-center">
                        <img src="" id="customer-avatar" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                    </div>
                    <div class="mb-3">
                        <label for="edit-name" class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-phone" class="form-label small fw-semibold">Nomor Telepon</label>
                        <input type="text" name="phone" id="edit-phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-address" class="form-label small fw-semibold">Alamat Lengkap</label>
                        <textarea name="address" id="edit-address" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label small fw-semibold">Izin Login / Status Akun</label>
                        <select name="status" id="edit-status" class="form-select" required>
                            <option value="active">Active (Bisa Login)</option>
                            <option value="inactive">Inactive (Blokir Akses Login)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let table;

    $(document).ready(function() {
        table = $('#customer-table').DataTable({
            processing: true,
            ajax: "{{ url('/admin/customers/data') }}",
            columns: [
                {
                    data: 'avatar',
                    orderable: false,
                    render: function(data) {
                        return `<img src="${data || '/assets/images/avatar.png'}" class="rounded-circle border" style="width: 45px; height: 45px; object-fit: cover;">`;
                    }
                },
                { data: 'name' },
                {
                    data: null,
                    render: function(data) {
                        return `<span class="small d-block fw-bold">${data.phone}</span><span class="small text-muted">${data.email}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return `<span class="small d-block fw-semibold">${data.city}, ${data.province}</span><span class="small text-muted d-inline-block text-truncate" style="max-width: 250px;">${data.address || '-'}</span>`;
                    }
                },
                {
                    data: 'orders_count',
                    className: 'text-center fw-bold text-danger'
                },
                {
                    data: 'status',
                    className: 'text-center',
                    render: function(status, type, row) {
                        const colorClass = status === 'active' ? 'bg-success-subtle text-success border-success' : 'bg-danger-subtle text-danger border-danger';
                        const labelText = status === 'active' ? 'Active' : 'Non-Active';
                        return `<span class="badge ${colorClass} border border-opacity-25 py-1.5 px-3">${labelText}</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        const toggleBtnClass = data.status === 'active' ? 'btn-outline-warning' : 'btn-outline-success';
                        const toggleBtnText = data.status === 'active' ? '<i class="fa-solid fa-ban"></i>' : '<i class="fa-solid fa-check"></i>';
                        
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editCustomer(${data.id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn ${toggleBtnClass}" onclick="toggleStatus(${data.id})" title="Blokir/Buka Login">${toggleBtnText}</button>
                                <button class="btn btn-outline-danger" onclick="deleteCustomer(${data.id})" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                zeroRecords: "Tidak ada customer terdaftar",
                loadingRecords: "Memuat data customer...",
            }
        });
    });

    // Toggle Customer Status Flag AJAX
    function toggleStatus(id) {
        $.ajax({
            url: `/admin/customers/toggle-status/${id}`,
            type: "POST",
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
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
                    text: 'Gagal merubah status customer.'
                });
            }
        });
    }

    // Load customer data for edit modal
    function editCustomer(id) {
        $.ajax({
            url: `/admin/customers/show/${id}`,
            type: "GET",
            success: function(customer) {
                $('#customer-id').val(customer.id);
                $('#customer-avatar').attr('src', customer.avatar || '/assets/images/avatar.png');
                $('#edit-name').val(customer.name);
                $('#edit-phone').val(customer.phone);
                $('#edit-address').val(customer.address);
                $('#edit-status').val(customer.status);
                
                $('#customerModal').modal('show');
            }
        });
    }

    // Edit Submit AJAX
    $('#customer-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#customer-id').val();
        
        $.ajax({
            url: `/admin/customers/update/${id}`,
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                $('#customerModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
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
                    text: 'Gagal memperbarui data customer.'
                });
            }
        });
    });

    // Delete Customer AJAX
    function deleteCustomer(id) {
        Swal.fire({
            title: 'Hapus Customer?',
            text: "Apakah Anda yakin ingin menghapus data customer ini secara permanen?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/customers/delete/${id}`,
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
                            text: 'Gagal menghapus customer.'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection
