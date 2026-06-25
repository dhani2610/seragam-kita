<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('home.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $variant = ProductVariant::findOrFail($request->variant_id);

        $cart = session()->get('cart', []);

        $price = $product->price + $variant->additional_price;
        $image = $variant->image_path ?: ($product->images->first()->image_path ?? '/assets/images/p-sd-kemeja-1.jpg');

        $cartKey = $variant->id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'name' => $product->name,
                'size' => $variant->size,
                'color' => $variant->color,
                'price' => $price,
                'quantity' => (int) $request->quantity,
                'weight' => $product->weight,
                'image' => $image,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang!',
            'cart_count' => count($cart)
        ]);
    }

    public function update(Request $request)
    {
        if ($request->variant_id && $request->quantity) {
            $cart = session()->get('cart');
            if (isset($cart[$request->variant_id])) {
                $cart[$request->variant_id]['quantity'] = (int) $request->quantity;
                session()->put('cart', $cart);
                
                // Recalculate totals
                $subtotal = 0;
                $totalWeight = 0;
                foreach ($cart as $item) {
                    $subtotal += $item['price'] * $item['quantity'];
                    $totalWeight += $item['weight'] * $item['quantity'];
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Keranjang berhasil diperbarui.',
                    'item_total' => number_format($cart[$request->variant_id]['price'] * $cart[$request->variant_id]['quantity'], 0, ',', '.'),
                    'subtotal' => number_format($subtotal, 0, ',', '.'),
                    'total_weight' => $totalWeight
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Gagal memperbarui keranjang.'], 400);
    }

    public function remove(Request $request)
    {
        if ($request->variant_id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->variant_id])) {
                unset($cart[$request->variant_id]);
                session()->put('cart', $cart);
            }

            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang.',
                'subtotal' => number_format($subtotal, 0, ',', '.'),
                'cart_count' => count($cart)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal menghapus produk.'], 400);
    }
}
