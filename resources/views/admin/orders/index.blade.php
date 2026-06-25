@extends('layouts.admin')

@section('page_title', 'Daftar Pesanan Masuk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="m-0 font-outfit fw-bold text-dark"><i class="fa-solid fa-cart-shopping text-danger me-2"></i> Kelola Pesanan Masuk</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-premium" id="order-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Tanggal</th>
                                <th>Grand Total</th>
                                <th class="text-center">Pembayaran</th>
                                <th class="text-center">Status Kiriman</th>
                                <th>No. Resi</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail & Update Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-outfit fw-bold">Detail Pesanan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <!-- Customer details -->
                    <div class="col-md-6">
                        <h6 class="fw-bold font-outfit text-danger border-bottom pb-1 mb-2">Informasi Customer:</h6>
                        <table class="table table-sm table-borderless small mb-0">
                            <tr>
                                <td class="text-muted" style="width: 100px;">Nama</td>
                                <td class="fw-semibold" id="detail-name">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. HP</td>
                                <td class="fw-semibold" id="detail-phone">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Catatan</td>
                                <td class="text-secondary italic" id="detail-notes">-</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Shipping Address details -->
                    <div class="col-md-6">
                        <h6 class="fw-bold font-outfit text-danger border-bottom pb-1 mb-2">Alamat Pengiriman:</h6>
                        <p class="small mb-1"><strong id="detail-region">-</strong></p>
                        <p class="small text-muted mb-0" id="detail-address">-</p>
                    </div>
                </div>

                <!-- Order items table -->
                <h6 class="fw-bold font-outfit text-danger border-bottom pb-1 mb-3">Item Belanjaan:</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center" style="width: 80px;">Qty</th>
                                <th class="text-end" style="width: 120px;">Harga Satuan</th>
                                <th class="text-end" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody id="detail-items-body">
                            <!-- Loaded via Javascript -->
                        </tbody>
                    </table>
                </div>

                <!-- Shipping Costs Summary -->
                <div class="row mb-4 justify-content-end">
                    <div class="col-md-5">
                        <table class="table table-sm table-borderless small mb-0">
                            <tr>
                                <td class="text-muted">Subtotal</td>
                                <td class="text-end fw-semibold" id="detail-subtotal">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted" id="detail-courier-service">Ongkir</td>
                                <td class="text-end fw-semibold" id="detail-ongkir">-</td>
                            </tr>
                            <tr class="border-top">
                                <td class="fw-bold text-dark">Grand Total</td>
                                <td class="text-end fw-bold text-danger fs-6" id="detail-grandtotal">-</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Update Status Box -->
                <div class="card bg-light border p-3 rounded-3">
                    <h6 class="fw-bold font-outfit text-dark mb-3"><i class="fa-solid fa-file-invoice me-2 text-danger"></i> Update Status & Resi Pesanan</h6>
                    <form id="order-status-form">
                        <input type="hidden" name="order_id" id="order-id-val">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label for="status-select" class="form-label small fw-semibold">Status Pengiriman</label>
                                <select name="status" id="status-select" class="form-select" onchange="toggleTrackingField(this.value)" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Dikemas">Dikemas (Dipacking)</option>
                                    <option value="Dalam Pengiriman">Dalam Pengiriman (Kirim)</option>
                                    <option value="Selesai">Selesai (Sampai)</option>
                                </select>
                            </div>
                            
                            <!-- Nomor Resi (Required only if Dalam Pengiriman) -->
                            <div class="col-md-6 d-none" id="tracking-number-wrapper">
                                <label for="tracking-number" class="form-label small fw-semibold">Nomor Resi / Tracking Number</label>
                                <input type="text" name="tracking_number" id="tracking-number" class="form-control" placeholder="Contoh: JNE1234567890">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm px-4 mt-3 w-100 py-2 font-outfit fw-bold" id="btn-save-status">Update Data Pesanan</button>
                    </form>
                </div>

            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let table;

    $(document).ready(function() {
        table = $('#order-table').DataTable({
            processing: true,
            ajax: "{{ url('/admin/orders/data') }}",
            columns: [
                { data: 'invoice_number', className: 'fw-bold' },
                { data: 'user.name' },
                {
                    data: 'created_at',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        }) + ' WIB';
                    }
                },
                {
                    data: 'grand_total',
                    className: 'text-end fw-semibold text-danger',
                    render: function(price) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
                    }
                },
                {
                    data: 'payment_status',
                    className: 'text-center',
                    render: function(status) {
                        const colorClass = status === 'Paid' ? 'bg-success-subtle text-success border-success' : 'bg-warning-subtle text-warning border-warning';
                        return `<span class="badge ${colorClass} border border-opacity-25 py-1 px-2.5">${status === 'Paid' ? 'Lunas' : 'Belum Bayar'}</span>`;
                    }
                },
                {
                    data: 'status',
                    className: 'text-center',
                    render: function(status) {
                        let colorClass = 'bg-secondary-subtle text-secondary border-secondary';
                        if (status === 'Dikemas') colorClass = 'bg-info-subtle text-info border-info';
                        if (status === 'Dalam Pengiriman') colorClass = 'bg-primary-subtle text-primary border-primary';
                        if (status === 'Selesai') colorClass = 'bg-success-subtle text-success border-success';
                        
                        return `<span class="badge ${colorClass} border border-opacity-25 py-1 px-2.5">${status}</span>`;
                    }
                },
                {
                    data: 'tracking_number',
                    render: function(tracking) {
                        return tracking ? `<code class="text-danger fw-semibold">${tracking}</code>` : '<span class="text-muted small">-</span>';
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        return `<button class="btn btn-sm btn-outline-danger" onclick="viewOrder(${data.id})" title="Detail / Update Status"><i class="fa-solid fa-receipt me-1"></i> Detail</button>`;
                    }
                }
            ],
            language: {
                zeroRecords: "Pesanan masuk masih kosong",
                loadingRecords: "Memuat daftar pesanan...",
            }
        });
    });

    // View Order details in Modal
    function viewOrder(id) {
        $.ajax({
            url: `/admin/orders/show/${id}`,
            type: "GET",
            success: function(order) {
                // Populate fields
                $('#order-id-val').val(order.id);
                $('#detail-name').text(order.user.name);
                $('#detail-phone').text(order.user.phone);
                $('#detail-notes').text(order.notes || 'Tidak ada catatan');
                $('#detail-region').text(`${order.city}, ${order.province}`);
                $('#detail-address').text(`${order.address_details}`);
                
                $('#detail-subtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(order.subtotal));
                $('#detail-courier-service').text(`Ongkir (${order.shipping_courier.toUpperCase()} - ${order.shipping_service})`);
                $('#detail-ongkir').text('Rp ' + new Intl.NumberFormat('id-ID').format(order.shipping_cost));
                $('#detail-grandtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(order.grand_total));

                // Populate Form Status
                $('#status-select').val(order.status);
                $('#tracking-number').val(order.tracking_number || '');
                toggleTrackingField(order.status);

                // Render Items
                let rows = '';
                order.items.forEach(item => {
                    const price = item.price;
                    rows += `
                        <tr class="small">
                            <td>
                                <strong>${item.product.name}</strong>
                                <span class="badge bg-light text-dark border ms-2">Ukuran: ${item.variant.size} | Warna: ${item.variant.color}</span>
                            </td>
                            <td class="text-center">${item.quantity}</td>
                            <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(price)}</td>
                            <td class="text-end fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(price * item.quantity)}</td>
                        </tr>
                    `;
                });
                $('#detail-items-body').html(rows);

                $('#orderModal').modal('show');
            }
        });
    }

    // Toggle tracking resi field display
    function toggleTrackingField(status) {
        if (status === 'Dalam Pengiriman') {
            $('#tracking-number-wrapper').removeClass('d-none');
            $('#tracking-number').prop('required', true);
        } else {
            $('#tracking-number-wrapper').addClass('d-none');
            $('#tracking-number').prop('required', false);
        }
    }

    // Update Status Form Submit
    $('#order-status-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#order-id-val').val();
        const btn = $('#btn-save-status');

        btn.prop('disabled', true);

        $.ajax({
            url: `/admin/orders/status/${id}`,
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                btn.prop('disabled', false);
                $('#orderModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Status Diperbarui',
                    text: response.message,
                    timer: 1000,
                    showConfirmButton: false
                });
                table.ajax.reload();
            },
            error: function() {
                btn.prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memperbarui status pesanan.'
                });
            }
        });
    });
</script>
@endsection
