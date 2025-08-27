<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status', // pending, paid, shipped, completed, cancelled
        'shipping_address',
        'payment_method',
        'shipping_status',
    ];

    protected $casts = [
        'total_price' => 'float',
    ];

    /**
     * Order thuộc về user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Items của order
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Các giao dịch (có thể 1 hoặc nhiều)
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Lấy transaction gần nhất nếu muốn
     *
     * @return Transaction|null
     */
    public function latestTransaction(): ?Transaction
    {
        return $this->transactions()->latest()->first();
    }
}
