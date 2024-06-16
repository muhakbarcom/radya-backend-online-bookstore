<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShoppingCart;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/orders",
     *      operationId="placeOrder",
     *      tags={"Orders"},
     *      summary="Place a new order",
     *      description="Place a new order, and move all items in the cart to the order",
     *      @OA\Response(
     *          response=201,
     *          description="Order placed successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Order placed successfully"),
     *              @OA\Property(property="data", ref="#/components/schemas/Order")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Internal server error"),
     *              @OA\Property(property="data", example=null)
     *          )
     *      )
     * )
     */
    public function placeOrder(Request $request)
    {
        $user = auth()->user();

        DB::beginTransaction();

        try {
            $cartItems = ShoppingCart::where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'isSuccess' => false,
                    'message' => 'Your cart is empty',
                    'data' => null
                ], 400);
            }

            $totalPrice = 0;

            foreach ($cartItems as $item) {
                $totalPrice += $item->quantity * $item->book->price;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'completed', // Set status to completed (because we don't have payment process yet)
                'total_price' => $totalPrice,
                'order_number' => strtoupper(uniqid('ORDER-'))
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->price
                ]);

                // Kurangi stok buku
                $book = Book::findOrFail($item->book_id);
                $book->quantity -= $item->quantity;
                $book->save();
            }

            // Kosongkan keranjang belanja
            ShoppingCart::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'isSuccess' => true,
                'message' => 'Order placed successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
