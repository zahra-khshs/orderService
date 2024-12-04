<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_add_to_cart()
    {
        Http::fake([
            'http://product-service/api/products/*' => Http::sequence()
                ->push(['id' => 1, 'name' => 'Test Product', 'price' => 100]),
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/cart/add', ['product_id' => 1]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Product added to cart']);
    }

    public function test_checkout()
    {
        Http::fake([
            'http://product-service/api/products/*' => Http::sequence()
                ->push(['id' => 1, 'name' => 'Test Product', 'price' => 100]),
        ]);

         $this->actingAs($this->user)->postJson('/api/cart/add', ['product_id' => 1]);

         $response = $this->actingAs($this->user)->postJson('/api/cart/checkout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Order placed successfully']);
    }

    public function test_get_orders()
    {
        Http::fake([
            'http://product-service/api/products/*' => Http::sequence()
                ->push(['id' => 1, 'name' => 'Test Product', 'price' => 100]),
        ]);

         $this->actingAs($this->user)->postJson('/api/cart/add', ['product_id' => 1]);
        $this->actingAs($this->user)->postJson('/api/cart/checkout');

         $response = $this->actingAs($this->user)->getJson('/api/orders');

        $response->assertStatus(200)
                 ->assertJsonStructure(['*' => ['id', 'user_id', 'products']]);
    }

}
