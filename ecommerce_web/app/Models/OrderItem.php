<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity'
    ];

    /**
     * Relationship with User model
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship with Product model
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor - Lấy tên sản phẩm
     */
    public function getProductNameAttribute()
    {
        return $this->product->name ?? 'Sản phẩm không tồn tại';
    }

    /**
     * Accessor - Lấy giá sản phẩm từ bảng products
     */
    public function getPriceAttribute()
    {
        return $this->product->price ?? 0;
    }

    /**
     * Accessor - Tính tổng tiền của item này
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    /**
     * Accessor - Lấy giá đã format
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    /**
     * Accessor - Lấy subtotal đã format
     */
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 0, ',', '.');
    }
}
