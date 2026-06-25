<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('cover')->nullable();
            $table->timestamps();
        });

        // Products table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('weight'); // in grams
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamps();
        });

        // Product Images table
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });

        // Product Variants table
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->decimal('additional_price', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // Sliders table
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });

        // Settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Orders table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_weight'); // in grams
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('grand_total', 12, 2);
            $table->string('status')->default('Pending'); // Pending, Dikemas, Dalam Pengiriman, Selesai
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->integer('province_id');
            $table->integer('city_id');
            $table->string('province');
            $table->string('city');
            $table->text('address_details');
            $table->string('payment_status')->default('Unpaid'); // Unpaid, Paid, Expired, Cancelled
            $table->string('payment_token')->nullable();
            $table->timestamps();
        });

        // Order Items table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });

        // Reviews table
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
