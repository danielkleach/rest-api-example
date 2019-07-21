<?php

namespace Tests\Feature;

use App\User;
use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function unauthenticated_users_cannot_access_product_index_endpoint()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function unauthenticated_users_cannot_access_product_store_endpoint()
    {
        $response = $this->postJson('/api/products');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function unauthenticated_users_cannot_access_product_show_endpoint()
    {
        $response = $this->getJson('/api/products/-1');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function unauthenticated_users_cannot_access_product_update_endpoint()
    {
        $response = $this->patchJson('/api/products/-1');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function unauthenticated_users_cannot_access_product_destroy_endpoint()
    {
        $response = $this->deleteJson('/api/products/-1');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function will_show_a_404_if_product_is_not_found()
    {
        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->getJson("/api/products/-1");
        
        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_get_a_single_product()
    {
        $product = factory(Product::class)->create();

        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->getJson("/api/products/{$product->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'image',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJson([
            'data' => [
                'id' => (int) $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $product->image,
                'created_at' => $product->created_at->format('F j, Y'),
                'updated_at' => $product->updated_at->format('F j, Y')
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_paginated_products()
    {
        $product = factory(Product::class)->create();
        $product2 = factory(Product::class)->create();

        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->getJson("/api/products");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'image',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => [
                'current_page',
                'last_page',
                'from',
                'to',
                'path',
                'per_page',
                'total'
            ]
        ]);
        $response->assertJson([
            'data' => [
                [
                    'id' => (int) $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'image' => $product->image,
                    'created_at' => $product->created_at->format('F j, Y'),
                    'updated_at' => $product->updated_at->format('F j, Y')
                ],
                [
                    'id' => (int) $product2->id,
                    'name' => $product2->name,
                    'description' => $product2->description,
                    'price' => $product2->price,
                    'image' => $product2->image,
                    'created_at' => $product2->created_at->format('F j, Y'),
                    'updated_at' => $product2->updated_at->format('F j, Y')
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_create_a_product()
    {
        $data = [
            'name' => 'Test Product',
            'description' => 'Description of the product.',
            'price' => 100.00
        ];

        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->postJson("/api/products", $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJson([
            'data' => [
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price']
            ]
        ]);

        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function will_show_a_404_if_product_to_update_is_not_found()
    {
        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->patchJson("/api/products/-1");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_update_a_product()
    {
        $product = factory(Product::class)->create();

        $data = [
            'name' => 'New Product Name'
        ];

        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->patchJson("/api/products/{$product->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'image',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJson([
            'data' => [
                'name' => $data['name'],
                'description' => $product->description,
                'price' => $product->price,
                'image' => $product->image
            ]
        ]);

        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function will_show_a_404_if_product_to_delete_is_not_found()
    {
        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->deleteJson("/api/products/-1");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_delete_a_product()
    {
        $product = factory(Product::class)->create();

        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->deleteJson("/api/products/{$product->id}");
        $response->assertStatus(200);
        $response->assertSee(null);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }
}
