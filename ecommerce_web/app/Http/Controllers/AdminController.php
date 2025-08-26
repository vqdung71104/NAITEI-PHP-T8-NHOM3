<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        try {
            // Get data for dashboard
            $categories = Category::all();
            $products = Product::with('category')->get();
            $orders = Order::with(['user', 'orderItems', 'orderItems.product'])->get();
            $users = User::all();
            
            // Calculate statistics
            $totalOrders = $orders->count();
            $totalRevenue = $orders->sum('total_price');
            $processingOrders = $orders->where('status', 'processing')->count();
            $completedOrders = $orders->where('status', 'completed')->count();
            $cancelledOrders = $orders->where('status', 'cancelled')->count();
            $pendingOrders = $orders->where('status', 'pending')->count();
            $returnOrders = $orders->where('status', 'return')->count();
            
            // Low stock products (stock <= 10)
            $lowStockProducts = $products->where('stock', '<=', 10)->count();
            
            // Recent orders (last 7 days)
            $recentOrders = $orders->where('created_at', '>=', now()->subDays(7))->count();
            
            // Top selling categories (simplified approach)
            $categoryStats = $categories->map(function($category) {
                $categoryProducts = Product::where('category_id', $category->id)->pluck('id');
                $categoryOrderItems = OrderItem::whereIn('product_id', $categoryProducts)->get();
                $uniqueOrders = $categoryOrderItems->pluck('order_id')->unique();
                
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'orders_count' => $uniqueOrders->count()
                ];
            })->sortByDesc('orders_count')->take(5);

            // Monthly revenue (last 12 months)
            $monthlyRevenue = collect();
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthRevenue = $orders->filter(function($order) use ($month) {
                    return $order->created_at >= $month->startOfMonth() && 
                           $order->created_at <= $month->endOfMonth();
                })->sum('total_price');
                
                $monthlyRevenue->push([
                    'month' => $month->format('M Y'),
                    'revenue' => $monthRevenue
                ]);
            }
            
            // Pagination data
            $pagination = [
                'current_page' => 1,
                'per_page' => 10,
                'total' => $products->count()
            ];

            $statistics = [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'processing_orders' => $processingOrders,
                'completed_orders' => $completedOrders,
                'cancelled_orders' => $cancelledOrders,
                'pending_orders' => $pendingOrders,
                'return_orders' => $returnOrders,
                'low_stock_products' => $lowStockProducts,
                'recent_orders' => $recentOrders,
                'total_products' => $products->count(),
                'total_categories' => $categories->count(),
                'category_stats' => $categoryStats->values(),
                'monthly_revenue' => $monthlyRevenue
            ];

            return inertia('AdminDashboard', [
                'categories' => $categories,
                'products' => $products,
                'orders' => $orders,
                'users' => $users,
                'pagination' => $pagination,
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            // Return empty data if there's an error (e.g., tables don't exist yet)
            return inertia('AdminDashboard', [
                'categories' => [],
                'products' => [],
                'orders' => [],
                'users' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 0
                ],
                'statistics' => [
                    'total_orders' => 0,
                    'total_revenue' => 0,
                    'processing_orders' => 0,
                    'completed_orders' => 0,
                    'cancelled_orders' => 0,
                    'pending_orders' => 0,
                    'return_orders' => 0,
                    'low_stock_products' => 0,
                    'recent_orders' => 0,
                    'total_products' => 0,
                    'total_categories' => 0,
                    'category_stats' => [],
                    'monthly_revenue' => []
                ],
            ]);
        }
    }

    public function index()
    {
        return redirect()->route('admin.dashboard');
    }


    /**
     * Display admin statistics or overview
     */
    public function categories()
    {
        $categories = Category::all();
        return redirect()->back()->with('categories', $categories);
    }
    
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ], [
            'name.unique' => 'Tên danh mục này đã tồn tại. Vui lòng chọn tên khác.',
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
        ]);
        
        $category = Category::create($validated);
        
        return redirect()->back()->with('success', 'Category created successfully.');
    }
    
    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ], [
            'name.unique' => 'Tên danh mục này đã tồn tại. Vui lòng chọn tên khác.',
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
        ]);
        
        $category->update($validated);
        
        return redirect()->back()->with('success', 'Category updated successfully.');
    }
    
    public function destroyCategory(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    // Product CRUD methods
    public function products()
    {
        $products = Product::all();
        return redirect()->back()->with('products', $products);
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
        ], [
            'name.unique' => 'Tên sản phẩm này đã tồn tại. Vui lòng chọn tên khác.',
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là một số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.',
            'stock.required' => 'Số lượng tồn kho là bắt buộc.',
            'stock.integer' => 'Số lượng tồn kho phải là một số nguyên.',
            'stock.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'category_id.required' => 'Danh mục sản phẩm là bắt buộc.',
            'category_id.exists' => 'Danh mục đã chọn không tồn tại.',
        ]);
        
        $product = Product::create($validated);
        
        return redirect()->back()->with('success', 'Product created successfully.');
    }
    
    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
        ], [
            'name.unique' => 'Tên sản phẩm này đã tồn tại. Vui lòng chọn tên khác.',
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là một số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.',
            'stock.required' => 'Số lượng tồn kho là bắt buộc.',
            'stock.integer' => 'Số lượng tồn kho phải là một số nguyên.',
            'stock.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'category_id.required' => 'Danh mục sản phẩm là bắt buộc.',
            'category_id.exists' => 'Danh mục đã chọn không tồn tại.',
        ]);
        
        $product->update($validated);
        
        return redirect()->back()->with('success', 'Product updated successfully.');
    }
    
    public function destroyProduct(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully.');
    }

    // Order management
    public function orders()
    {
        $orders = Order::all();
        return redirect()->back()->with('orders', $orders);
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total' => 'required|numeric|min:0',
            'status' => 'required|string|max:255',
            'address_id' => 'required|exists:addresses,id',
        ]);

        $order = Order::create($validated);

        return redirect()->back()->with('success', 'Order created successfully.');
    }

    public function updateOrder(Request $request, Order $order)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total' => 'required|numeric|min:0',
            'status' => 'required|string|max:255',
            'address_id' => 'required|exists:addresses,id',
        ]);

        $order->update($validated);

        return redirect()->back()->with('success', 'Order updated successfully.');
    }

    public function destroyOrder(Order $order)
    {
        $order->delete();
        return redirect()->back()->with('success', 'Order deleted successfully.');
    }

    public function users()
    {
        $users = User::all();
        return redirect()->back()->with('users', $users);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|string|max:255'
        ]);

        $user = User::create($validated);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|string|max:255'
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function showUser(User $user)
    {
        $user->load(['cartItems.product', 'Orders.orderItems.product', 'Addresses']);
        return redirect()->back()->with('user', $user);
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

}
