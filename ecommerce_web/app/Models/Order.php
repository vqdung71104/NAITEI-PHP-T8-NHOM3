<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'address_id', 
        'total_price',
        'status',
        'confirmed_at',
        'confirmed_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Order status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_RETURN = 'return';

    /**
     * Get all available statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_RETURN,
        ];
    }

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship với User (admin xác nhận)
     */
    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Relationship with Address model
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Relationship with OrderItem model
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get products through order items
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Scope for filtering orders by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering orders by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Calculate total price from order items
     */
    public function calculateTotalPrice()
    {
        return $this->orderItems()->with('product')->get()->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Check if order belongs to user
     */
    public function belongsToUser($userId)
    {
        return $this->user_id == $userId;
    }

    /**
     * Accessor - Lấy tên khách hàng
     */
    public function getCustomerNameAttribute()
    {
        return $this->user->name ?? 'N/A';
    }

    /**
     * Accessor - Lấy email khách hàng
     */
    public function getCustomerEmailAttribute()
    {
        return $this->user->email ?? null;
    }

    /**
     * Accessor - Lấy số điện thoại khách hàng
     */
    public function getCustomerPhoneAttribute()
    {
        return $this->address->phone_number ?? null;
    }

    /**
     * Accessor - Lấy địa chỉ giao hàng
     */
    public function getShippingAddressAttribute()
    {
        if ($this->address) {
            return $this->address->full_address ?? 
                   ($this->address->ward . ', ' .$this->address->district . ', ' . $this->address->city) ??
                   $this->address->address;
        }
        return 'N/A';
    }

    /**
     * Accessor - Tính tổng tiền đơn hàng từ order items
     */
    public function getCalculatedTotalAttribute()
    {
        return $this->orderItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });
    }

    /**
     * Method - Cập nhật tổng tiền đơn hàng
     */
    public function updateTotalPrice()
    {
        $this->total_price = $this->calculated_total;
        $this->save();
        return $this;
    }
}
