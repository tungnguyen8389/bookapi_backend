<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    // Nếu bảng cart_items CÓ cột price -> để trong fillable; nếu KHÔNG có thì giữ nguyên 3 field
    protected $fillable = [
        'cart_id',
        'book_id',
        'quantity',
        // 'price', // bật dòng này nếu migration có cột price
    ];

    protected $appends = ['total', 'unit_price'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Đơn giá của item (unit price)
     * - Nếu cart_items có cột price và đã lưu -> ưu tiên dùng
     * - Nếu không có price -> dùng book.final_price (nếu có) hoặc book.price
     */
    public function getUnitPriceAttribute(): float
    {
        // Nếu model có thuộc tính price và khác null -> dùng
        if (array_key_exists('price', $this->attributes) && !is_null($this->attributes['price'])) {
            return (float) $this->attributes['price'];
        }

        // Fallback sang giá từ book
        $book = $this->relationLoaded('book') ? $this->book : $this->book()->first();
        if ($book) {
            // Book của bạn đã có accessor final_price
            return (float) ($book->final_price ?? $book->price ?? 0);
        }

        return 0.0;
        }

    /**
     * Tổng tiền của 1 item = unit_price * quantity
     */
    public function getTotalAttribute(): float
    {
        return round($this->unit_price * (int) $this->quantity, 2);
    }
}
