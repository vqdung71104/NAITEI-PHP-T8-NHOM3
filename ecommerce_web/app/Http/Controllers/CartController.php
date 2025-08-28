<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CheckoutRequest;
use App\Services\ShippingFeeService;


class CartController extends Controller
{
    protected $shippingFeeService;

    public function __construct(ShippingFeeService $shippingFeeService)
    {
        $this->shippingFeeService = $shippingFeeService;
    }
    
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem giỏ hàng.');
        }

        // Lấy tất cả sản phẩm trong giỏ hàng của user, kèm thông tin product
        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();

        // Tính subtotal
        $subtotal = $cartItems->sum(function($item) {
            return ($item->product->price ?? 0) * $item->quantity;
        });

        $shipping = 30000; // phí vận chuyển mặc định

        return view('cart.index', compact('cartItems', 'subtotal', 'shipping'));
    }

    public function add(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            // Nếu chưa đăng nhập, chuyển hướng tới login
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thêm vào giỏ hàng.');
        }

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $cartItem = CartItem::where('user_id', $user->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($cartItem) {
            // Nếu đã có, tăng số lượng
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Nếu chưa có, tạo mới
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cartItem->update(['quantity' => $request->quantity]);
        return response()->json(['success' => true]);
    }

    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return response()->json(['success' => true]);
    }
    
    public function checkout()
    {
        $user = Auth::user();
        $addresses = Auth::check()
            ? $user->addresses()->orderByDesc('is_default')->latest()->get()
            : collect();

        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
        $subtotal = $cartItems->sum(fn($item) => ($item->product->price ?? 0) * $item->quantity);
        $shipping = 30000;

        return view('cart.checkout', compact('cartItems', 'subtotal', 'shipping', 'addresses'));
    }

    public function processCheckout(CheckoutRequest $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán.');
        }

        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        \DB::beginTransaction();
        try {
            // Xử lý địa chỉ
            if ($request->address_option === 'existing') {
                $address = Address::where('id', $request->address_id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();
            } else {
                // bỏ default của địa chỉ cũ
                Address::where('user_id', $user->id)->update(['is_default' => 0]);

                $address = Address::create([
                    'user_id' => $user->id,
                    'full_name' => $request->full_name,
                    'phone_number' => $request->phone_number,
                    'details' => $request->details,
                    'ward' => $request->ward,
                    'district' => $request->district,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'country' => $request->country,
                    'is_default' => 1,
                ]);
            }

            // Tính tổng
            $subtotal = $cartItems->sum(fn($item) => ($item->product->price ?? 0) * $item->quantity);
            $shipping = $this->shippingFeeService->calculate($subtotal, $address);
            $totalPrice = $subtotal + $shipping;

            // Tạo order
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'total_price' => $totalPrice,
                'payment_method' => 'COD',
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Tạo order items và giảm stock
            foreach ($cartItems as $item) {
                $product = $item->product;
                if ($product && $product->stock !== null) {
                    if ($product->stock < $item->quantity) {
                        throw new \Exception("Sản phẩm {$product->name} không đủ số lượng.");
                    }
                    $product->decrement('stock', $item->quantity);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $product->price ?? 0,
                ]);
            }

            // Xóa giỏ hàng
            CartItem::where('user_id', $user->id)->delete();

            \DB::commit();

            return redirect()->route('orders.track')
                            ->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi khi xử lý đơn hàng: ' . $e->getMessage());
        }
    }
}
