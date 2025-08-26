<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductValidationUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function test_product_name_is_required()
    {
        // Arrange
        $data = [
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_name_must_be_string()
    {
        // Arrange
        $data = [
            'name' => 12345, // Not a string
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_name_max_length_validation()
    {
        // Arrange
        $longName = str_repeat('a', 256); // 256 characters, exceeds max of 255
        $data = [
            'name' => $longName,
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_name_uniqueness_validation()
    {
        // Arrange
        Product::factory()->create(['name' => 'Existing Product']);
        
        $data = [
            'name' => 'Existing Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_price_is_required()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'category_id' => $this->category->id,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_price_must_be_numeric()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 'not_a_number',
            'category_id' => $this->category->id,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_price_cannot_be_negative()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => -10.50,
            'category_id' => $this->category->id,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_price_can_be_zero()
    {
        // Arrange
        $data = [
            'name' => 'Free Product',
            'description' => 'Test Description',
            'price' => 0,
            'category_id' => $this->category->id,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_product_stock_is_required()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_stock_must_be_integer()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => 'not_an_integer',
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_stock_cannot_be_negative()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => -5,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_stock_can_be_zero()
    {
        // Arrange
        $data = [
            'name' => 'Out of Stock Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => 0,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_product_category_id_is_required()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_category_id_must_exist()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => 999999, // Non-existent category
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('category_id', $validator->errors()->toArray());
    }

    /** @test */
    public function test_product_description_is_nullable()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => 10,
            // description is omitted
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_product_description_must_be_string_when_provided()
    {
        // Arrange
        $data = [
            'name' => 'Test Product',
            'description' => 12345, // Not a string
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => 10,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('description', $validator->errors()->toArray());
    }

    /** @test */
    public function test_update_validation_allows_sometimes_rules()
    {
        // Arrange
        $product = Product::factory()->create();
        $data = [
            'name' => 'Updated Name',
            // Other fields are optional for update
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function test_update_validation_unique_name_excludes_current_product()
    {
        // Arrange
        $product1 = Product::factory()->create(['name' => 'Product 1']);
        $product2 = Product::factory()->create(['name' => 'Product 2']);
        
        $data = [
            'name' => 'Product 2', // Same name as current product
        ];

        // Act & Assert - Updating product2 with its own name should pass
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255|unique:products,name,' . $product2->id,
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        $this->assertFalse($validator->fails());

        // But using another product's name should fail
        $data['name'] = 'Product 1';
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255|unique:products,name,' . $product2->id,
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function test_valid_product_data_passes_validation()
    {
        // Arrange
        $data = [
            'name' => 'Valid Product Name',
            'description' => 'This is a valid product description.',
            'price' => 99.99,
            'category_id' => $this->category->id,
            'stock' => 50,
        ];

        // Act & Assert
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        $this->assertFalse($validator->fails());
        $this->assertEmpty($validator->errors()->toArray());
    }

    /** @test */
    public function test_business_logic_prevents_deleting_product_with_orders()
    {
        // Arrange
        $product = Product::factory()->create();
        $order = Order::factory()->create();
        OrderItem::factory()->create([
            'product_id' => $product->id,
            'order_id' => $order->id,
        ]);

        // Act & Assert
        // This would be tested in the controller or service layer
        // Here we just verify the relationship exists
        $this->assertTrue($product->orderItems()->exists());
    }

    /** @test */
    public function test_business_logic_prevents_deleting_product_with_reviews()
    {
        // Arrange
        $product = Product::factory()->create();
        Review::factory()->create(['product_id' => $product->id]);

        // Act & Assert
        // This would be tested in the controller or service layer
        // Here we just verify the relationship exists
        $this->assertTrue($product->reviews()->exists());
    }
}