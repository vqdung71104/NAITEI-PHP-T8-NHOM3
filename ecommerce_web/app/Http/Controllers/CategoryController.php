<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @group Categories
 *
 * APIs for managing categories
 */
class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories
     *
     * Trả về danh sách tất cả categories.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {"id": 1, "name": "Văn học Nga", "description": "Các tác phẩm văn học từ nước Nga"},
     *     {"id": 2, "name": "Sách giáo khoa", "description": "Bộ sách chính thức sử dụng trong nhà trường từ lớp 1 đến 12"}
     *   ],
     *   "message": "Success get all categories"
     * }
     */
    public function index(): JsonResponse
    {
        try {
            $categories = $this->categoryRepository->all();
            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Success get all categories'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error get all categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new category
     *
     * @authenticated
     * @bodyParam name string required Tên của category. Example: Văn học Nga
     * @bodyParam description string Mô tả category. Example: Các tác phẩm văn học từ nước Nga
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 3,
     *     "name": "Sách self-help",
     *     "description": "Dành cho những tâm hồn cần chữa lành"
     *   },
     *   "message": "Create category success"
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "Invalid data",
     *   "errors": {
     *     "name": ["The name field is required."]
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string'
            ]);

            $category = $this->categoryRepository->store($validated);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Create category success'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category by ID
     *
     * @urlParam id integer required ID của category. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Văn học Nga",
     *     "description": "Các tác phẩm văn học từ nước Nga"
     *   },
     *   "message": "Success get category"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Category not found"
     * }
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = $this->categoryRepository->find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Success get category'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error get category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a category
     *
     * @authenticated
     * @urlParam id integer required ID của category. Example: 1
     * @bodyParam name string required Tên mới của category. Example: Updated Văn học Nga
     * @bodyParam description string Mô tả mới. Example: Updated description
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Updated Văn học Nga",
     *     "description": "Updated description"
     *   },
     *   "message": "Update category success"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Category not found"
     * }
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $category = $this->categoryRepository->find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string'
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'data' => $category->fresh(),
                'message' => 'Update category success'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a category
     *
     * @authenticated
     * @urlParam id integer required ID của category. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Delete category successfully"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Category not found"
     * }
     * @response 400 {
     *   "success": false,
     *   "message": "Cannot delete category because there are products belonging to this category"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $category = $this->categoryRepository->find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            if ($category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category because there are products belonging to this category'
                ], 400);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Delete category successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
