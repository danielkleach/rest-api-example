<?php

namespace Tests\Feature;

use App\User;
use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function unauthenticated_users_cannot_access_user_products_index_endpoint()
    {
        $response = $this->getJson('/api/user-products');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function unauthenticated_users_cannot_access_user_products_store_endpoint()
    {
        $response = $this->postJson('/api/user-products');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function unauthenticated_users_cannot_access_user_products_destroy_endpoint()
    {
        $response = $this->deleteJson('/api/user-products/-1');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_paginated_products_for_the_requesting_user()
    {
        $user = factory(User::class)->create();
        $product = factory(Product::class)->create();
        $product2 = factory(Product::class)->create();

        $user->products()->attach($product);
        $user->products()->attach($product2);

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/user-products");
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
    public function can_attach_a_product_to_a_user()
    {
        $user = factory(User::class)->create();
        $product = factory(Product::class)->create();

        $response = $this->actingAs($user, 'api')
            ->postJson("/api/user-products", [
                'product_id' => $product->id
            ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('user_products', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    /**
     * @test
     */
    public function can_detach_a_product_from_a_user()
    {
        $user = factory(User::class)->create();
        $product = factory(Product::class)->create();

        $user->products()->attach($product);

        $response = $this->actingAs($user, 'api')
            ->deleteJson("/api/user-products/{$product->id}");
        $response->assertStatus(200);
        $response->assertSee(null);

        $this->assertDatabaseMissing('user_products', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }
}
