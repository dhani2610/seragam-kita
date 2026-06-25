<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helper to get average rating
    public function getAverageRatingAttribute()
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : 5.0;
    }

    // Helper to get total sold
    public function getTotalSoldAttribute()
    {
        // Count from completed orders
        return $this->orderItems()
            ->whereHas('order', function ($query) {
                $query->where('payment_status', 'Paid');
            })
            ->sum('quantity');
    }
}
