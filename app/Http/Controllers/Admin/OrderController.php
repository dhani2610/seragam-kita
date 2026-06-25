<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }

    public function orderData()
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $orders]);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'items.variant'])->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:Pending,Dikemas,Dalam Pengiriman,Selesai',
            'tracking_number' => 'required_if:status,Dalam Pengiriman',
        ]);

        $order->status = $request->status;

        if ($request->status === 'Dalam Pengiriman') {
            $order->tracking_number = $request->tracking_number;
        }

        // Auto pay if set to shipping or complete just in case
        if (in_array($request->status, ['Dalam Pengiriman', 'Selesai'])) {
            $order->payment_status = 'Paid';
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui.'
        ]);
    }

    // Reports View
    public function reports()
    {
        return view('admin.reports.index');
    }

    // Reports Data API
    public function reportData(Request $request)
    {
        $query = Order::where('payment_status', 'Paid')->with('user');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('updated_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $orders = $query->orderBy('updated_at', 'desc')->get();
        return response()->json(['data' => $orders]);
    }

    // Export PDF using Barryvdh DomPDF
    public function exportPdf(Request $request)
    {
        $query = Order::where('payment_status', 'Paid')->with(['user', 'items.product']);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('updated_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $orders = $query->get();
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        
        $pdf = Pdf::loadView('admin.reports.pdf', compact('orders', 'start_date', 'end_date'));
        return $pdf->download('Laporan_Penjualan_' . ($start_date ?? 'Semua') . '_sd_' . ($end_date ?? 'Semua') . '.pdf');
    }

    // Export Excel/CSV (Using native stream format for high stability and maximum compatibility)
    public function exportExcel(Request $request)
    {
        $query = Order::where('payment_status', 'Paid')->with(['user', 'items.product']);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('updated_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $orders = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan_Penjualan.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['No', 'Invoice', 'Tanggal', 'Customer', 'Kurir', 'Layanan', 'Total Berat (gr)', 'Subtotal', 'Ongkir', 'Grand Total']);

            foreach ($orders as $index => $order) {
                fputcsv($file, [
                    $index + 1,
                    $order->invoice_number,
                    $order->updated_at->format('Y-m-d H:i'),
                    $order->user->name,
                    strtoupper($order->shipping_courier),
                    $order->shipping_service,
                    $order->total_weight,
                    $order->subtotal,
                    $order->shipping_cost,
                    $order->grand_total
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
