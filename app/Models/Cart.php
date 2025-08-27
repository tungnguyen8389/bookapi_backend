<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    /**
     * Cart thuộc về user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Items trong giỏ
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Tổng tiền tạm tính của cart (không bao gồm phí ship)
     *
     * @return float
     */
    public function getSubtotalAttribute(): float
    {
        $sum = 0.0;
        foreach ($this->items as $item) {
            $price = $item->book?->final_price ?? $item->book?->price ?? 0;
            $sum += $price * $item->quantity;
        }
        return round($sum, 2);
    }
}
