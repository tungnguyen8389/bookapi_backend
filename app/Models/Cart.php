<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // nếu có cột status (active/checked_out) thì thêm vào đây
        // 'status',
    ];

    // Để tự động hiển thị trong JSON trả về
    protected $appends = ['subtotal', 'items_count'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tổng tiền tạm tính của giỏ hàng = sum(item.unit_price * item.quantity)
     * Ưu tiên dùng cart_items.price (nếu bảng có cột này), fallback sang book.final_price hoặc book.price
     */
    public function getSubtotalAttribute(): float
    {
        // đảm bảo đã load book để không bị N+1
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->with('book')->get();

        $sum = 0.0;
        foreach ($items as $item) {
            $unit = $item->unit_price; // accessor từ CartItem (bên dưới)
            $sum += $unit * (int) $item->quantity;
        }

        // làm tròn 2 số thập phân
        return round($sum, 2);
    }

    /**
     * Tổng số lượng (sum quantity)
     */
    public function getItemsCountAttribute(): int
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();
        return (int) $items->sum('quantity');
    }
}
