<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Lưu review mới.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'content'    => 'required|string|max:1000',
        ]);

        // Check đăng nhập
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để viết đánh giá.');
        }

        // Tạo review
        Review::create([
            'user_id'    => $user->id,
            'product_id' => $validated['product_id'],
            'rating'     => $validated['rating'],
            'content'    => $validated['content'],
            'image_url'  => null,
        ]);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm.');
    }
}
