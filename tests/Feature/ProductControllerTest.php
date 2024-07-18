<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;
use App\Services\ProductService;


class ProductControllerTest extends TestCase
{
    /** @var ProductService|\Mockery\MockInterface */
    protected $mockService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the service
        $this->mockService = Mockery::mock(ProductService::class);
        $this->app->instance(ProductService::class, $this->mockService);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /** @test */
    public function it_lists_all_products()
    {
        // Empty the JSON file (if applicable)

        // Send GET request to index route
        $response = $this->get('/');

        // Assert response is successful and contains products data
        $response->assertStatus(200);

    }

    /** @test */
    public function it_stores_a_new_product()
    {

        // Mock data for new product
        $productData = [
            'name' => 'New Product',
            'quantity' => 5,
            'price' => 30.25,
        ];

        // Mock service method
        $this->mockService->shouldReceive('storeProduct')->with($productData)->andReturn($productData);

        // Send POST request to store route with product data
        $response = $this->post('/products', $productData);

        // Assert response is successful
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_an_existing_product()
    {

        // Mock data for existing product
        $productId = 1;
        $updatedData = [
            'name' => 'Updated Product',
            'quantity' => 15,
            'price' => 29.99,
        ];

        // Mock service method
        $this->mockService->shouldReceive('updateProduct')->with($productId, $updatedData)->andReturn($updatedData);

        // Send PUT request to update route with updated data
        $response = $this->put("/products/{$productId}", $updatedData);

        // Assert response is successful
        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_an_existing_product()
    {
        // Empty the JSON file (if applicable)

        // Mock data for existing product
        $productId = 1;

        // Mock service method
        $this->mockService->shouldReceive('deleteProduct')->with($productId)->andReturn(true);

        // Send DELETE request to delete route
        $response = $this->delete("/products/{$productId}");

        // Assert response is successful
        $response->assertStatus(200);
    }
}
