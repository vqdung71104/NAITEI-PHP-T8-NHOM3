<?php

namespace App\Http\Controllers;

use App\Events\OrderConfirmed;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use App\Notifications\NewOrderNotification;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);
        
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $query = Order::with(['user', 'address', 'orderItems.product']);
            
            if ($request->has('status') && $request->status !== '') {
                $query->status($request->status);
            }
            
            if ($request->has('user_id') && $request->user_id !== '') {
                $query->forUser($request->user_id);
            }
        } else {
            $query = Order::with(['address', 'orderItems.product'])
                          ->forUser($user->id);
                          
            if ($request->has('status') && $request->status !== '') {
                $query->status($request->status);
            }
        }
        
        $orders = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('per_page', 15));
        
        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Danh sách đơn hàng được tải thành công.'
        ]);
    }

    /**
     * Display the specified order
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);
        
        $order->load(['user', 'address', 'orderItems.product']);
        
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Chi tiết đơn hàng được tải thành công.'
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);
        
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'address_id' => $validated['address_id'],
                'status' => $validated['status'],
                'total_price' => 0,
            ]);
            
            $totalPrice = 0;
            
            foreach ($validated['order_items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
                
                $totalPrice += $product->price * $item['quantity'];
            }
            
            $order->update(['total_price' => $totalPrice]);

            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewOrderNotification($order));
            }
            
            DB::commit();
            
            $order->load(['user', 'address', 'orderItems.product']);
            
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Đơn hàng được tạo thành công.'
            ], 201);
            
        } catch (\Throwable $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled,return', 
        ]);
    
        $order->update([
            'status' => $request->status,
        ]);
    
        return redirect()->back()->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified order from storage
     */
    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);
        
        try {
            DB::beginTransaction();
            
            $order->orderItems()->delete();
            
            $order->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng được xóa thành công.'
            ]);
            
        } catch (\Throwable $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);
        
        try {
            $order->update(['status' => Order::STATUS_CANCELLED]);
            
            $order->load(['user', 'address', 'orderItems.product']);
            
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Đơn hàng được hủy thành công.'
            ]);
            
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status (Admin only)
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorize('updateStatus', $order);
        
        $request->validate([
            'status' => 'required|in:' . implode(',', Order::getStatuses())
        ]);
        
        try {
            $order->update(['status' => $request->status]);
            
            $order->load(['user', 'address', 'orderItems.product']);
            
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Trạng thái đơn hàng được cập nhật thành công.'
            ]);
            
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics (Admin only)
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('viewStatistics', Order::class);
        
        try {
            $stats = [
                'total_orders' => Order::count(),
                'pending_orders' => Order::status(Order::STATUS_PENDING)->count(),
                'processing_orders' => Order::status(Order::STATUS_PROCESSING)->count(),
                'completed_orders' => Order::status(Order::STATUS_COMPLETED)->count(),
                'cancelled_orders' => Order::status(Order::STATUS_CANCELLED)->count(),
                'return_orders' => Order::status(Order::STATUS_RETURN)->count(),
                'total_revenue' => Order::whereIn('status', [
                    Order::STATUS_COMPLETED,
                    Order::STATUS_PROCESSING
                ])->sum('total_price'),
                'orders_by_status' => Order::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Thống kê đơn hàng được tải thành công.'
            ]);
            
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thống kê: ' . $e->getMessage()
            ], 500);
        }
    }

    // Backward compatibility method
    public function trackOrders()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $orders = Order::with(['address', 'orderItems.product'])
                    ->forUser($user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('orders.track', compact('orders'));
}

    /**
     * Lấy đơn hàng với eager loading để tránh N+1 query
     */
    private function getOrderWithRelations($orderId)
    {
        return Order::with([
            'user',                          // Thông tin khách hàng
            'address',                       // Địa chỉ giao hàng
            'orderItems',                    // Order items
            'orderItems.product',            // Chi tiết sản phẩm trong đơn hàng
            'confirmedBy',                   // Admin xác nhận'
        ])->findOrFail($orderId);
    }

    public function confirmOrder(Request $request, $orderId)
    {
        $order = $this->getOrderWithRelations($orderId);
        
        // Kiểm tra đơn hàng đã được xác nhận chưa
        // if ($order->isConfirmed()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Đơn hàng này đã được xác nhận trước đó!'
        //     ]);
        // }

        // Kiểm tra có email khách hàng không
        if (!$order->user || !$order->user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy email khách hàng!'
            ]);
        }

        try {
            // Kích hoạt event - Email sẽ được gửi tự động qua listener
            event(new OrderConfirmed($order));

            // Cập nhật trạng thái đơn hàng
            $order->update([
                'status' => 'processing',
            ]);
        
            
        
            return ;
        
        } catch (\Exception $e) {
            \Log::error('Error confirming order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận đơn hàng!'
        ], 500);
        }
    }
}