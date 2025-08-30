<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'price',
        'discount',
        'stock',
        'status',
        'description',
        'image_url',
        'category_id',
        'author_id',
        'published_year',
        'publisher',
    ];

    protected $casts = [
        'price' => 'float',
        'discount' => 'float',
        'stock' => 'integer',
        'published_year' => 'integer',
    ];

    /**
     * Tác giả của sách
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Danh mục của sách
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Review cho sách
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Item đơn hàng chứa sách này
     *
     * @return HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Item giỏ hàng chứa sách này
     *
     * @return HasMany
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Giá sau khi áp dụng discount
     *
     * @return float
     */
    public function getFinalPriceAttribute(): float
    {
        $price = $this->price ?? 0.0;
        $discount = $this->discount ?? 0.0;
        $final = $price - $discount;
        return $final > 0 ? round($final, 2) : 0.0;
    }
    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => [
                'title' => $value,
                'slug' => Str::slug($value),
            ],
        );
    }
}
