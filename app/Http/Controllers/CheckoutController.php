<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\User;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Keranjang Anda kosong!');
        }

        $user = Auth::user();

        // Fetch Provinces for Checkout
        $provinces = [];
        $apiKey = Setting::getValue('rajaongkir_api_key', env('RAJAONGKIR_API_KEY'));
        try {
            $response = Http::withHeaders(['key' => $apiKey])
                ->get('https://rajaongkir.komerce.id/api/v1/destination/province');
            if ($response->successful()) {
                $provinces = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {}

        if (empty($provinces)) {
            $provinces = [
                ['id' => 9, 'name' => 'Jawa Barat'],
                ['id' => 11, 'name' => 'Jawa Timur'],
            ];
        }

        return view('home.checkout', compact('cart', 'user', 'provinces'));
    }

    public function checkCost(Request $request)
    {
        $request->validate([
            'destination' => 'required',
            'weight' => 'required|numeric',
            'courier' => 'required|in:jne,tiki,pos',
        ]);

        $apiKey = Setting::getValue('rajaongkir_api_key', env('RAJAONGKIR_API_KEY'));
        $origin = Setting::getValue('rajaongkir_origin', env('RAJAONGKIR_ORIGIN', '152')); // Default Bandung

        try {
            $response = Http::withHeaders(['key' => $apiKey])
                ->asForm() // <--- TAMBAHKAN INI
                ->post('https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost', [
                    'origin' => $origin,
                    'destination' => $request->destination,
                    'weight' => $request->weight,
                    'courier' => $request->courier,
                ]);


            if ($response->successful()) {
                $costs = $response->json()['data'] ?? [];
                return response()->json([
                    'success' => true,
                    'costs' => $costs
                ]);
            }
        } catch (\Exception $e) {}

        // Mock Cost fallback if API fails
        $mockCosts = [
            'jne' => [
                ['service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => [['value' => 12000, 'etd' => '2-3 hari']]],
                ['service' => 'OKE', 'description' => 'Ongkos Kirim Ekonomis', 'cost' => [['value' => 10000, 'etd' => '3-4 hari']]],
            ],
            'tiki' => [
                ['service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => [['value' => 11000, 'etd' => '2-3 hari']]],
            ],
            'pos' => [
                ['service' => 'Pos Reguler', 'description' => 'Pos Reguler', 'cost' => [['value' => 9000, 'etd' => '3-5 hari']]],
            ],
        ];

        return response()->json([
            'success' => true,
            'costs' => $mockCosts[$request->courier] ?? []
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'province_id' => 'required',
            'city_id' => 'required',
            'province' => 'required',
            'city' => 'required',
            'address_details' => 'required',
            'courier' => 'required',
            'service' => 'required',
            'shipping_cost' => 'required|numeric',
            'notes' => 'nullable',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang Anda kosong!'], 400);
        }

        // Calculate Totals
        $subtotal = 0;
        $totalWeight = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $totalWeight += $item['weight'] * $item['quantity'];
        }

        $shippingCost = $request->shipping_cost;
        $grandTotal = $subtotal + $shippingCost;

        // Create Order
        $invoice = 'INV-' . strtoupper(Str::random(10));

        $order = Order::create([
            'invoice_number' => $invoice,
            'user_id' => Auth::id(),
            'total_weight' => $totalWeight,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'grand_total' => $grandTotal,
            'status' => 'Pending',
            'shipping_courier' => $request->courier,
            'shipping_service' => $request->service,
            'notes' => $request->notes,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'province' => $request->province,
            'city' => $request->city,
            'address_details' => $request->address_details,
            'payment_status' => 'Unpaid',
            'payment_token' => 'TOK-' . strtoupper(Str::random(12)),
        ]);

        // Create Order Items
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // Deduct variant stock
            $variant = ProductVariant::find($item['variant_id']);
            if ($variant) {
                $variant->stock = max(0, $variant->stock - $item['quantity']);
                $variant->save();
            }
        }

        // Clear Cart
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'order_id' => $order->id,
            'payment_url' => route('checkout.payment', $order->id)
        ]);
    }

    public function showPayment($orderId)
    {
        $order = Order::with(['items.product', 'items.variant'])->findOrFail($orderId);
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('home.payment', compact('order'));
    }

    // Payment Callback simulation
    public function simulatePaymentSuccess(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->payment_status = 'Paid';
        $order->status = 'Dikemas'; // Set status to Packed upon payment success
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran Berhasil Disimulasikan!'
        ]);
    }
}
