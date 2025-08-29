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

/**
 * @group Users
 * APIs for managing users. All endpoints require admin privileges and authentication via Bearer Token.
 * @authenticated
 */
class UserController extends Controller
{
    /**
     * Display a listing of users
     *
     * Retrieves a paginated list of users with optional filters for role, status, and search by name or email.
     *
     * @queryParam role string Filter users by role (e.g., 'admin', 'customer'). Example: admin
     * @queryParam status string Filter users by status (e.g., 'active', 'inactive'). Example: active
     * @queryParam search string Search users by name or email. Example: john
     * @queryParam per_page integer Number of users per page. Default: 15. Example: 10
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "data": [
     *       {
     *         "id": 1,
     *         "name": "John Doe",
     *         "email": "john@example.com",
     *         "role": "admin",
     *         "status": "active",
     *         "created_at": "2025-08-29T13:00:00.000000Z",
     *         "updated_at": "2025-08-29T13:00:00.000000Z"
     *       }
     *     ],
     *     "current_page": 1,
     *     "last_page": 1,
     *     "per_page": 15,
     *     "total": 1
     *   },
     *   "message": "Danh sách người dùng được tải thành công."
     * }
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
     *
     * Creates a new user with the provided details. Only accessible by admins.
     *
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Example: john@example.com
     * @bodyParam password string required The password for the user. Example: Password123!
     * @bodyParam role string required The role of the user (e.g., 'admin', 'customer'). Example: admin
     * @bodyParam status string required The status of the user (e.g., 'active', 'inactive'). Example: active
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "role": "admin",
     *     "status": "active",
     *     "created_at": "2025-08-29T13:00:00.000000Z",
     *     "updated_at": "2025-08-29T13:00:00.000000Z"
     *   },
     *   "message": "Người dùng được tạo thành công."
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
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
     *
     * Retrieves detailed information about a specific user, including their cart items, orders, and addresses.
     *
     * @urlParam user integer required The ID of the user. Example: 1
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "role": "admin",
     *     "status": "active",
     *     "cartItems": [],
     *     "Orders": [],
     *     "Addresses": [],
     *     "created_at": "2025-08-29T13:00:00.000000Z",
     *     "updated_at": "2025-08-29T13:00:00.000000Z"
     *   },
     *   "message": "Thông tin người dùng được tải thành công."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Người dùng không tồn tại."
     * }
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
     *
     * Updates the details of a specific user. Only accessible by admins.
     *
     * @urlParam user integer required The ID of the user. Example: 1
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Example: john@example.com
     * @bodyParam password string optional The new password for the user. Example: NewPassword123!
     * @bodyParam role string required The role of the user (e.g., 'admin', 'customer'). Example: admin
     * @bodyParam status string required The status of the user (e.g., 'active', 'inactive'). Example: active
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "role": "admin",
     *     "status": "active",
     *     "created_at": "2025-08-29T13:00:00.000000Z",
     *     "updated_at": "2025-08-29T13:00:00.000000Z"
     *   },
     *   "message": "Thông tin người dùng được cập nhật thành công."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Người dùng không tồn tại."
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
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
     *
     * Deletes a specific user. Cannot delete users with existing orders or the current admin user.
     *
     * @urlParam user integer required The ID of the user. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Người dùng được xóa thành công."
     * }
     * @response 400 {
     *   "success": false,
     *   "message": "Không thể xóa người dùng có đơn hàng. Hãy thay đổi trạng thái thành inactive thay vì xóa."
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Bạn không thể xóa tài khoản của chính mình."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Người dùng không tồn tại."
     * }
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
     *
     * Updates the status of a specific user to 'active' or 'inactive'. Cannot deactivate the current admin user.
     *
     * @urlParam user integer required The ID of the user. Example: 1
     * @bodyParam status string required The new status of the user (active or inactive). Example: active
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "role": "admin",
     *     "status": "active",
     *     "created_at": "2025-08-29T13:00:00.000000Z",
     *     "updated_at": "2025-08-29T13:00:00.000000Z"
     *   },
     *   "message": "Trạng thái người dùng được cập nhật thành công."
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Bạn không thể vô hiệu hóa tài khoản của chính mình."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Người dùng không tồn tại."
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "status": ["Trạng thái phải là active hoặc inactive."]
     *   }
     * }
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
     *
     * Retrieves statistics about users, including total users, active users, inactive users, admin users, customer users, and recent users (created within the last 30 days).
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "total_users": 100,
     *     "active_users": 80,
     *     "inactive_users": 20,
     *     "admin_users": 5,
     *     "customer_users": 95,
     *     "recent_users": 10
     *   },
     *   "message": "Thống kê người dùng được tải thành công."
     * }
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