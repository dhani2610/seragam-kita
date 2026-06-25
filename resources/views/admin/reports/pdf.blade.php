<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Ecommerce Seragam</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
            margin: 0 0 5px;
        }
        .subtitle {
            font-size: 11px;
            color: #666;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #555;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .total-box {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="title">LAPORAN PENJUALAN SUKSES</h1>
        <p class="subtitle">Platform Ecommerce Seragam Sekolah Terpercaya</p>
        <p class="subtitle">
            Periode: 
            {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d M Y') : 'Awal' }}
            s/d
            {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d M Y') : 'Akhir' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Invoice</th>
                <th>Tanggal Sukses</th>
                <th>Customer</th>
                <th>Kurir</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Ongkir</th>
                <th class="text-right">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @php $totalClean = 0; @endphp
            @forelse($orders as $index => $order)
                @php $totalClean += $order->grand_total; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $order->invoice_number }}</strong></td>
                    <td>{{ $order->updated_at->format('d M Y, H:i') }} WIB</td>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ strtoupper($order->shipping_courier) }} ({{ $order->shipping_service }})</td>
                    <td class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                    <td class="text-right fw-bold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada riwayat transaksi ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        TOTAL PEMASUKAN BERSIH: Rp {{ number_format($totalClean, 0, ',', '.') }}
    </div>

</body>
</html>
