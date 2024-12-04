<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $userId = Auth::id();

         
        $productResponse = Http::get("http://product-service/api/products/{$productId}");

        if ($productResponse->failed()) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $cartKey = "cart:{$userId}";
        Redis::rpush($cartKey, json_encode($productResponse->json()));

        return response()->json(['message' => 'Product added to cart']);
    }

    public function checkout(Request $request)
    {
        $userId = Auth::id();
        $cartKey = "cart:{$userId}";

        $cartItems = Redis::lrange($cartKey, 0, -1);

        if (empty($cartItems)) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $order = Order::create([
            'user_id' => $userId,
            'products' => json_decode('[' . implode(',', $cartItems) . ']', true),
        ]);

        Redis::del($cartKey);

        return response()->json(['message' => 'Order placed successfully', 'order' => $order]);
    }

    public function getOrders()
    {
        $userId = Auth::id();
        $orders = Order::where('user_id', $userId)->get();

        return response()->json($orders);
    }
}
