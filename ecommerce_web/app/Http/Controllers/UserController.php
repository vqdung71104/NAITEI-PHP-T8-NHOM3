<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filter by role if provided
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $users,
            'message' => 'Danh sách người dùng được tải thành công.'
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(UserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Người dùng được tạo thành công.'
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['cartItems.product', 'Orders.orderItems.product', 'Addresses']);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Thông tin người dùng được tải thành công.'
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'data' => $user->fresh(),
            'message' => 'Thông tin người dùng được cập nhật thành công.'
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deleting the current admin user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể xóa tài khoản của chính mình.'
            ], 403);
        }

        // Check if user has orders
        if ($user->Orders()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa người dùng có đơn hàng. Hãy thay đổi trạng thái thành inactive thay vì xóa.'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Người dùng được xóa thành công.'
        ]);
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ], [
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái phải là active hoặc inactive.',
        ]);

        // Prevent deactivating the current admin user
        if ($user->id === auth()->id() && $validated['status'] === 'inactive') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể vô hiệu hóa tài khoản của chính mình.'
            ], 403);
        }

        $user->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'data' => $user->fresh(),
            'message' => 'Trạng thái người dùng được cập nhật thành công.'
        ]);
    }

    /**
     * Get user statistics
     */
    public function statistics(): JsonResponse
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        $adminUsers = User::where('role', 'admin')->count();
        $customerUsers = User::where('role', 'customer')->count();
        $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();

        $statistics = [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'admin_users' => $adminUsers,
            'customer_users' => $customerUsers,
            'recent_users' => $recentUsers,
        ];

        return response()->json([
            'success' => true,
            'data' => $statistics,
            'message' => 'Thống kê người dùng được tải thành công.'
        ]);
    }
}