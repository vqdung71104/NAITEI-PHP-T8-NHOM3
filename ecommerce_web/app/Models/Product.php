<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'stock',
        'image_url',
        'author',
        // Add other fields as needed
    ];
    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    /**
     * Get the cart items for the product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Accessor: Lấy URL đầy đủ của ảnh (chỉ file local)
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            // Kiểm tra xem có file no_image.png trong public/images không
            if (file_exists(public_path('images/no_image.png'))) {
                return asset('images/no_image.png');
            }
            // Tạo no_image nếu chưa có
            return $this->createNoImageIfNotExists();
        }

        // Kiểm tra file có tồn tại trong storage không
        if (Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        // Fallback nếu file không tồn tại
        return $this->createNoImageIfNotExists();
    }

    /**
     * Tạo ảnh no_image nếu chưa có
     */
    private function createNoImageIfNotExists()
    {
        $publicImagePath = public_path('images/no_image.png');
        
        // Tạo thư mục images nếu chưa có
        if (!is_dir(dirname($publicImagePath))) {
            mkdir(dirname($publicImagePath), 0755, true);
        }
        
        // Nếu chưa có file, tạo mới
        if (!file_exists($publicImagePath)) {
            $this->createNoImageFile($publicImagePath);
        }
        
        return asset('images/no_image.png');
    }

    /**
     * Xóa ảnh khi xóa model
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            $product->deleteOldImage();
        });
    }
}
