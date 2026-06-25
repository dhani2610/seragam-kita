@extends('layouts.admin')

@section('page_title', 'Laporan Penjualan Sukses')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Date filter card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 font-outfit text-danger"><i class="fa-solid fa-filter me-1"></i> Filter Berdasarkan Tanggal</h6>
                <form id="filter-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label small fw-semibold">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label small fw-semibold">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>
                        <div class="col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4 font-outfit fw-bold py-2 flex-grow-1">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Cari Laporan
                            </button>
                            <button type="button" class="btn btn-outline-danger font-outfit fw-bold py-2 px-3" onclick="resetFilter()" title="Reset Filter Tanggal">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sales history card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="m-0 font-outfit fw-bold text-dark"><i class="fa-solid fa-chart-line text-danger me-2"></i> Riwayat Penjualan</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm px-3" onclick="exportData('excel')">
                        <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm px-3" onclick="exportData('pdf')">
                        <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-premium" id="report-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Tanggal Sukses</th>
                                <th>Kurir</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-end">Ongkos Kirim</th>
                                <th class="text-end">Grand Total</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="table-light fw-bold font-outfit fs-6">
                                <td colspan="6" class="text-end">TOTAL PEMASUKAN BERSIH:</td>
                                <td class="text-end text-danger" id="report-total-sum">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let table;

    $(document).ready(function() {
        loadReportTable();
    });

    function loadReportTable(startDate = '', endDate = '') {
        if (table) {
            table.destroy();
        }

        table = $('#report-table').DataTable({
            processing: true,
            ajax: {
                url: "{{ url('/admin/reports/data') }}",
                data: {
                    start_date: startDate,
                    end_date: endDate
                }
            },
            columns: [
                { data: 'invoice_number', className: 'fw-bold' },
                { data: 'user.name' },
                {
                    data: 'updated_at',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        }) + ' WIB';
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return `${data.shipping_courier.toUpperCase()} - ${data.shipping_service}`;
                    }
                },
                {
                    data: 'subtotal',
                    className: 'text-end',
                    render: function(price) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
                    }
                },
                {
                    data: 'shipping_cost',
                    className: 'text-end',
                    render: function(price) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
                    }
                },
                {
                    data: 'grand_total',
                    className: 'text-end fw-bold text-danger',
                    render: function(price) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
                    }
                }
            ],
            drawCallback: function(settings) {
                // Calculate and update overall total sum in footer
                const api = this.api();
                const total = api.column(6).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                
                $('#report-total-sum').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
            },
            language: {
                zeroRecords: "Belum ada riwayat penjualan pada tanggal yang dipilih",
                loadingRecords: "Memuat riwayat penjualan...",
            }
        });
    }

    // Submit filter form
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        const start = $('#start_date').val();
        const end = $('#end_date').val();
        loadReportTable(start, end);
    });

    function resetFilter() {
        $('#start_date').val('');
        $('#end_date').val('');
        loadReportTable();
    }

    // Handle export request parameters redirection
    function exportData(type) {
        const start = $('#start_date').val();
        const end = $('#end_date').val();
        
        let url = `/admin/reports/export/${type}?start_date=${start}&end_date=${end}`;
        window.open(url, '_blank');
    }
</script>
@endsection
