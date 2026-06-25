<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // ================= CATEGORIES =================
    public function categories()
    {
        return view('admin.products.categories');
    }

    public function categoryData()
    {
        $categories = Category::withCount('products')->get();
        return response()->json(['data' => $categories]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'cover' => 'nullable|image|max:2048',
        ]);

        $coverPath = '/assets/images/category-sd.jpg'; // default cover placeholder
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/categories'), $filename);
            $coverPath = '/uploads/categories/' . $filename;
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'cover' => $coverPath,
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil ditambahkan.']);
    }

    public function showCategory($id)
    {
        return response()->json(Category::findOrFail($id));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $id,
            'cover' => 'nullable|image|max:2048',
        ]);

        $data = ['name' => $request->name, 'slug' => Str::slug($request->name)];

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/categories'), $filename);
            $data['cover'] = '/uploads/categories/' . $filename;
        }

        $category->update($data);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbarui.']);
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }


    // ================= PRODUCTS =================
    public function index()
    {
        $categories = Category::all();
        return view('admin.products.index', compact('categories'));
    }

    public function productData()
    {
        $products = Product::with(['category', 'variants'])->get();
        return response()->json(['data' => $products]);
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|unique:products,name',
            'weight' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'description' => 'required|string',
            'images' => 'required',
            'images.*' => 'image|max:2048',
        ]);

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'weight' => $request->weight,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
        ]);

        // Upload Product Images
        $firstImagePath = null;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . rand(100, 999) . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/products'), $filename);
                $path = '/uploads/products/' . $filename;
                if (!$firstImagePath) {
                    $firstImagePath = $path;
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        // Process Variants
        if ($request->variants) {
            $variants = json_decode($request->variants, true);
            if (is_array($variants)) {
                foreach ($variants as $index => $v) {
                    // Look for variant image file: variant_image_0, variant_image_1...
                    $vImgPath = $firstImagePath; // fallback
                    if ($request->hasFile("variant_image_{$index}")) {
                        $vFile = $request->file("variant_image_{$index}");
                        $vFilename = time() . '_variant_' . $index . '_' . $vFile->getClientOriginalName();
                        $vFile->move(public_path('uploads/variants'), $vFilename);
                        $vImgPath = '/uploads/variants/' . $vFilename;
                    }

                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $v['size'] ?? '',
                        'color' => $v['color'] ?? '',
                        'additional_price' => $v['additional_price'] ?? 0.00,
                        'stock' => $v['stock'] ?? 0,
                        'image_path' => $vImgPath,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan.']);
    }

    public function showProduct($id)
    {
        $product = Product::with(['images', 'variants'])->findOrFail($id);
        return response()->json($product);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|unique:products,name,' . $id,
            'weight' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'weight' => $request->weight,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
        ]);

        // If new images uploaded
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . rand(100, 999) . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/products'), $filename);
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => '/uploads/products/' . $filename,
                ]);
            }
        }

        // Process Variants (replace or update)
        if ($request->variants) {
            $variants = json_decode($request->variants, true);
            if (is_array($variants)) {
                // For simplicity in AJAX edit, we can recreate variants or update them.
                // Let's delete old variants and write new ones
                $product->variants()->delete();
                
                foreach ($variants as $index => $v) {
                    $vImgPath = $v['image_path'] ?? null;
                    
                    if ($request->hasFile("variant_image_{$index}")) {
                        $vFile = $request->file("variant_image_{$index}");
                        $vFilename = time() . '_variant_' . $index . '_' . $vFile->getClientOriginalName();
                        $vFile->move(public_path('uploads/variants'), $vFilename);
                        $vImgPath = '/uploads/variants/' . $vFilename;
                    }

                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $v['size'] ?? '',
                        'color' => $v['color'] ?? '',
                        'additional_price' => $v['additional_price'] ?? 0.00,
                        'stock' => $v['stock'] ?? 0,
                        'image_path' => $vImgPath ?: ($product->images()->first()->image_path ?? '/assets/images/p-sd-kemeja-1.jpg'),
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Produk berhasil diperbarui.']);
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus.']);
    }

    public function deleteProductImage($id)
    {
        $image = ProductImage::findOrFail($id);
        $image->delete();
        return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus.']);
    }
}
