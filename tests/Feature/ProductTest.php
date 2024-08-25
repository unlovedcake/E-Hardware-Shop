<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Auth\Authenticatable;



class ProductTest extends TestCase
{
    use RefreshDatabase;


    public function test_only_admin_can_add_product(): void
    {

        // Arrange: Create a brand and category
        $user = User::factory()->create(['user_type' => 'admin']);
        $userGuest = User::factory()->create(['user_type' => 'user']);
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();




        // Define product data with brand and category
        $data = [
            'name' => 'Test Product',
            'description' => 'Description Test Product',
            'price' => 100,
            'quantity' => 100,
            'image' => [],
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ];


        // Act: Attempt to add a product as a regular user
        $responseAsUser = $this->actingAs($userGuest)->post('/product/store', $data);
        // Assert: Regular user should be forbidden
        $responseAsUser->assertStatus(302);
        $this->assertDatabaseMissing('products', $data);


        // Act: Attempt to add a product as a admin user
        $response = $this->actingAs($user)->post('/product/store', $data);


        // Assert: Check if the product was created successfully with the correct brand and category
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', $data);
    }


    public function test_can_read_product(): void
    {
        // Arrange: Create a product with brand and category

        $userAdmin = User::factory()->create(['user_type' => 'admin']);

        $product = Product::factory()->create([
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
        ]);


        // Act: Fetch the product by ID
        $response = $this->actingAs($userAdmin)->get("/product/{$product->id}");

        // Assert: Check if the product data is returned with brand and category
        $response->assertStatus(200);

        $response->assertJson($product->toArray());
    }


    public function test_can_update_a_product(): void
    {
        // Arrange: Create a product
        $user = User::factory()->create(['user_type' => 'admin']);
        $product = Product::factory()->create([
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
        ]);

        // Define new data for the product
        $newBrand = Brand::factory()->create();
        $newCategory = Category::factory()->create();
        $newData = [
            'name' => 'Test Product',
            'description' => 'Description Test Product',
            'price' => 200,
            'quantity' => 200,
            'image' => [],
            'brand_id' => $newBrand->id,
            'category_id' => $newCategory->id,
        ];

        // Act: Send a PUT request to update the product
        $response = $this->actingAs($user)->post("/product/update/{$product->id}", $newData);

        // Assert: Check if the product was updated successfully with the correct brand and category
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $newData));
    }


    public function test_can_delete_a_product(): void
    {
        // Arrange: Create a product
        $user = User::factory()->create(['user_type' => 'admin']);
        $product = Product::factory()->create([
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
        ]);

        // Act: Send a DELETE request to delete the product
        $response = $this->actingAs($user)->delete("/product/delete/{$product->id}");

        // Assert: Check if the product was deleted successfully
        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
