<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Slider;
use App\Models\Setting;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Helpers\ImageGenerator;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate placeholder images
        ImageGenerator::generatePlaceholder('/assets/images/logo.png', 200, 60, 'Ecommerce Seragam', [220, 53, 69]);
        ImageGenerator::generatePlaceholder('/assets/images/avatar.png', 150, 150, 'Avatar BS', [108, 117, 125]);
        
        ImageGenerator::generatePlaceholder('/assets/images/slider1.jpg', 1200, 450, 'Premium Uniforms - 20% OFF', [220, 53, 69]);
        ImageGenerator::generatePlaceholder('/assets/images/slider2.jpg', 1200, 450, 'Back to School Promo', [40, 167, 69]);

        ImageGenerator::generatePlaceholder('/assets/images/category-sd.jpg', 400, 250, 'Seragam SD', [220, 53, 69]);
        ImageGenerator::generatePlaceholder('/assets/images/category-smp.jpg', 400, 250, 'Seragam SMP', [0, 123, 255]);
        ImageGenerator::generatePlaceholder('/assets/images/category-sma.jpg', 400, 250, 'Seragam SMA', [108, 117, 125]);
        ImageGenerator::generatePlaceholder('/assets/images/category-pramuka.jpg', 400, 250, 'Aksesoris & Pramuka', [139, 69, 19]);

        ImageGenerator::generatePlaceholder('/assets/images/p-sd-kemeja-1.jpg', 600, 600, 'Kemeja SD S/M', [240, 240, 240], [0, 0, 0]);
        ImageGenerator::generatePlaceholder('/assets/images/p-sd-kemeja-2.jpg', 600, 600, 'Kemeja SD L/XL', [230, 230, 230], [0, 0, 0]);
        ImageGenerator::generatePlaceholder('/assets/images/p-sd-celana-1.jpg', 600, 600, 'Celana SD Pinggang Karet', [180, 0, 0]);
        ImageGenerator::generatePlaceholder('/assets/images/p-sd-celana-2.jpg', 600, 600, 'Celana SD Ukuran Besar', [160, 0, 0]);
        
        ImageGenerator::generatePlaceholder('/assets/images/p-smp-kemeja-1.jpg', 600, 600, 'Kemeja SMP Lengan Panjang', [240, 240, 240], [0, 0, 0]);
        ImageGenerator::generatePlaceholder('/assets/images/p-smp-rok-1.jpg', 600, 600, 'Rok SMP Plisket Biru', [0, 0, 150]);
        
        ImageGenerator::generatePlaceholder('/assets/images/p-sma-kemeja-1.jpg', 600, 600, 'Kemeja SMA Lengan Pendek', [240, 240, 240], [0, 0, 0]);
        ImageGenerator::generatePlaceholder('/assets/images/p-sma-celana-1.jpg', 600, 600, 'Celana SMA Abu-Abu', [120, 120, 120]);

        ImageGenerator::generatePlaceholder('/assets/images/p-pramuka-baju-1.jpg', 600, 600, 'Baju Pramuka Penggalang', [100, 50, 20]);
        ImageGenerator::generatePlaceholder('/assets/images/p-aksesoris-1.jpg', 600, 600, 'Dasi & Kaos Kaki & Sabuk', [50, 50, 50]);

        // 1. Create Admin & Customer
        User::create([
            'name' => 'Admin Seragam',
            'email' => 'admin@ecommerce.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '081234567890',
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'customer@ecommerce.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
            'phone' => '089876543210',
            'province_id' => 9, // Jawa Barat
            'city_id' => 23, // Bandung
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'address' => 'Jl. Kebon Kawung No. 12, Pasir Kaliki, Cicendo',
            'postal_code' => '40171',
            'avatar' => '/assets/images/avatar.png',
        ]);

        // 2. Categories
        $categories = [
            ['name' => 'Seragam SD', 'cover' => '/assets/images/category-sd.jpg'],
            ['name' => 'Seragam SMP', 'cover' => '/assets/images/category-smp.jpg'],
            ['name' => 'Seragam SMA', 'cover' => '/assets/images/category-sma.jpg'],
            ['name' => 'Aksesoris & Pramuka', 'cover' => '/assets/images/category-pramuka.jpg'],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['name']] = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'cover' => $cat['cover'],
            ]);
        }

        // 3. Products, Images & Variants
        $productsData = [
            [
                'category' => 'Seragam SD',
                'name' => 'Kemeja Putih SD Lengan Pendek',
                'weight' => 150,
                'price' => 45000.00,
                'stock' => 100,
                'description' => 'Kemeja putih seragam sekolah SD terbuat dari bahan katun TC super yang halus, tebal, tidak mudah kusut, dan sangat nyaman dipakai seharian oleh anak Anda.',
                'images' => ['/assets/images/p-sd-kemeja-1.jpg', '/assets/images/p-sd-kemeja-2.jpg'],
                'variants' => [
                    ['size' => 'S', 'color' => 'Putih', 'additional_price' => 0.00, 'stock' => 30, 'image_path' => '/assets/images/p-sd-kemeja-1.jpg'],
                    ['size' => 'M', 'color' => 'Putih', 'additional_price' => 5000.00, 'stock' => 40, 'image_path' => '/assets/images/p-sd-kemeja-1.jpg'],
                    ['size' => 'L', 'color' => 'Putih', 'additional_price' => 10000.00, 'stock' => 30, 'image_path' => '/assets/images/p-sd-kemeja-2.jpg'],
                ]
            ],
            [
                'category' => 'Seragam SD',
                'name' => 'Celana Merah SD Panjang',
                'weight' => 220,
                'price' => 55000.00,
                'stock' => 80,
                'description' => 'Celana panjang seragam SD berwarna merah cabe standar nasional. Dilengkapi karet di bagian pinggang untuk kenyamanan fleksibel serta saku fungsional.',
                'images' => ['/assets/images/p-sd-celana-1.jpg', '/assets/images/p-sd-celana-2.jpg'],
                'variants' => [
                    ['size' => '25', 'color' => 'Merah', 'additional_price' => 0.00, 'stock' => 20, 'image_path' => '/assets/images/p-sd-celana-1.jpg'],
                    ['size' => '26', 'color' => 'Merah', 'additional_price' => 5000.00, 'stock' => 30, 'image_path' => '/assets/images/p-sd-celana-1.jpg'],
                    ['size' => '27', 'color' => 'Merah', 'additional_price' => 10000.00, 'stock' => 30, 'image_path' => '/assets/images/p-sd-celana-2.jpg'],
                ]
            ],
            [
                'category' => 'Seragam SMP',
                'name' => 'Kemeja Putih SMP Lengan Panjang',
                'weight' => 200,
                'price' => 60000.00,
                'stock' => 70,
                'description' => 'Kemeja seragam SMP putih polos lengan panjang dengan bahan Famatex premium. Sangat rapi, tidak menerawang, dan jahitannya kuat.',
                'images' => ['/assets/images/p-smp-kemeja-1.jpg'],
                'variants' => [
                    ['size' => 'M', 'color' => 'Putih', 'additional_price' => 0.00, 'stock' => 35, 'image_path' => '/assets/images/p-smp-kemeja-1.jpg'],
                    ['size' => 'L', 'color' => 'Putih', 'additional_price' => 5000.00, 'stock' => 35, 'image_path' => '/assets/images/p-smp-kemeja-1.jpg'],
                ]
            ],
            [
                'category' => 'Seragam SMP',
                'name' => 'Rok Biru SMP Panjang',
                'weight' => 280,
                'price' => 65000.00,
                'stock' => 90,
                'description' => 'Rok biru panjang untuk SMP dengan model rempel/plisket keliling. Menggunakan bahan Famatex asli yang terkenal tahan lama dan warna tidak mudah pudar.',
                'images' => ['/assets/images/p-smp-rok-1.jpg'],
                'variants' => [
                    ['size' => 'S', 'color' => 'Biru', 'additional_price' => 0.00, 'stock' => 30, 'image_path' => '/assets/images/p-smp-rok-1.jpg'],
                    ['size' => 'M', 'color' => 'Biru', 'additional_price' => 5000.00, 'stock' => 30, 'image_path' => '/assets/images/p-smp-rok-1.jpg'],
                    ['size' => 'L', 'color' => 'Biru', 'additional_price' => 10000.00, 'stock' => 30, 'image_path' => '/assets/images/p-smp-rok-1.jpg'],
                ]
            ],
            [
                'category' => 'Seragam SMA',
                'name' => 'Kemeja Putih SMA Lengan Pendek',
                'weight' => 200,
                'price' => 65000.00,
                'stock' => 120,
                'description' => 'Kemeja putih lengan pendek untuk SMA dengan saku di dada kiri berlogo OSIS sablon premium rapi dan jernih.',
                'images' => ['/assets/images/p-sma-kemeja-1.jpg'],
                'variants' => [
                    ['size' => 'M', 'color' => 'Putih', 'additional_price' => 0.00, 'stock' => 60, 'image_path' => '/assets/images/p-sma-kemeja-1.jpg'],
                    ['size' => 'L', 'color' => 'Putih', 'additional_price' => 5000.00, 'stock' => 60, 'image_path' => '/assets/images/p-sma-kemeja-1.jpg'],
                ]
            ],
            [
                'category' => 'Seragam SMA',
                'name' => 'Celana Abu-Abu SMA Panjang',
                'weight' => 300,
                'price' => 75000.00,
                'stock' => 85,
                'description' => 'Celana panjang abu-abu SMA menggunakan bahan drill tebal berkualitas. Model formal saku samping kanan-kiri dan dua saku belakang.',
                'images' => ['/assets/images/p-sma-celana-1.jpg'],
                'variants' => [
                    ['size' => '28', 'color' => 'Abu-abu', 'additional_price' => 0.00, 'stock' => 40, 'image_path' => '/assets/images/p-sma-celana-1.jpg'],
                    ['size' => '30', 'color' => 'Abu-abu', 'additional_price' => 5000.00, 'stock' => 45, 'image_path' => '/assets/images/p-sma-celana-1.jpg'],
                ]
            ],
            [
                'category' => 'Aksesoris & Pramuka',
                'name' => 'Baju Pramuka Penggalang Lengan Panjang',
                'weight' => 220,
                'price' => 70000.00,
                'stock' => 60,
                'description' => 'Baju Pramuka Penggalang untuk siswa putra/putri, dilengkapi dengan saku dada ganda dengan tutup kancing silang khas pramuka standar nasional.',
                'images' => ['/assets/images/p-pramuka-baju-1.jpg'],
                'variants' => [
                    ['size' => 'M', 'color' => 'Cokelat', 'additional_price' => 0.00, 'stock' => 30, 'image_path' => '/assets/images/p-pramuka-baju-1.jpg'],
                    ['size' => 'L', 'color' => 'Cokelat', 'additional_price' => 5000.00, 'stock' => 30, 'image_path' => '/assets/images/p-pramuka-baju-1.jpg'],
                ]
            ],
            [
                'category' => 'Aksesoris & Pramuka',
                'name' => 'Paket Aksesoris Seragam Sekolah',
                'weight' => 50,
                'price' => 25000.00,
                'stock' => 200,
                'description' => 'Dapatkan paket aksesoris lengkap berupa dasi berlogo sekolah merah/biru/abu, ikat pinggang nilon logo sekolah, dan kaos kaki putih berlogo.',
                'images' => ['/assets/images/p-aksesoris-1.jpg'],
                'variants' => [
                    ['size' => 'All Size', 'color' => 'SD', 'additional_price' => 0.00, 'stock' => 70, 'image_path' => '/assets/images/p-aksesoris-1.jpg'],
                    ['size' => 'All Size', 'color' => 'SMP', 'additional_price' => 2000.00, 'stock' => 70, 'image_path' => '/assets/images/p-aksesoris-1.jpg'],
                    ['size' => 'All Size', 'color' => 'SMA', 'additional_price' => 4000.00, 'stock' => 60, 'image_path' => '/assets/images/p-aksesoris-1.jpg'],
                ]
            ],
        ];

        foreach ($productsData as $data) {
            $catId = $catModels[$data['category']]->id;
            $product = Product::create([
                'category_id' => $catId,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'weight' => $data['weight'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'description' => $data['description'],
            ]);

            // Add Product Images
            foreach ($data['images'] as $img) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $img,
                ]);
            }

            // Add Product Variants
            foreach ($data['variants'] as $v) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $v['size'],
                    'color' => $v['color'],
                    'additional_price' => $v['additional_price'],
                    'stock' => $v['stock'],
                    'image_path' => $v['image_path'],
                ]);
            }

            // Add Reviews
            Review::create([
                'product_id' => $product->id,
                'user_id' => 2, // Budi Santoso
                'rating' => 5,
                'comment' => 'Bahan tebal dan jahitan rapi sekali. Sesuai ukuran untuk anak saya. Recommended seller!',
            ]);
            Review::create([
                'product_id' => $product->id,
                'user_id' => 2,
                'rating' => 4,
                'comment' => 'Pengiriman cepat, packing rapi, barangnya juga bagus lembut bahannya.',
            ]);
        }

        // 4. Sliders
        Slider::create([
            'image_path' => '/assets/images/slider1.jpg',
            'title' => 'Seragam Sekolah Katun Premium',
            'description' => 'Nyaman dipakai seharian, bahan awet tidak gampang kusut.',
            'link' => '/products',
        ]);
        Slider::create([
            'image_path' => '/assets/images/slider2.jpg',
            'title' => 'Promo Awal Tahun Ajaran',
            'description' => 'Diskon bundling seragam lengkap sekolah SD, SMP, SMA.',
            'link' => '/products',
        ]);

        // 5. Settings
        $settings = [
            'website_name' => 'Ecommerce Seragam',
            'logo_path' => '/assets/images/logo.png',
            'facebook_url' => 'https://facebook.com/ecommerceseragam',
            'instagram_url' => 'https://instagram.com/ecommerceseragam',
            'youtube_url' => 'https://youtube.com/ecommerceseragam',
            'address_text' => 'Jl. Kebon Jati No. 40, Kebon Jeruk, Kec. Andir, Kota Bandung, Jawa Barat',
            'maps_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.840915357876!2d107.60195651477283!3d-6.909569794993351!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e63fac56ff77%3A0x6e8e8f81014e7a89!2sPusat%20Seragam%20Sekolah%20Bandung!5e0!3m2!1sid!2sid!4v1655734281729!5m2!1sid!2sid" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
            'operational_hours' => 'Senin - Sabtu: 08:00 - 17:00 WIB',
            'about_us' => 'Ecommerce Seragam adalah toko seragam sekolah online terpercaya yang menyediakan berbagai macam kebutuhan seragam SD, SMP, SMA, dan aksesoris/pramuka. Semua produk kami menggunakan bahan katun premium dan dijahit secara profesional untuk memastikan kenyamanan putra-putri Anda selama kegiatan belajar di sekolah.',
        ];

        foreach ($settings as $key => $val) {
            Setting::setValue($key, $val);
        }
    }
}
