<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\ShoppingCart;

class ShoppingCartController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/cart",
     *      operationId="addToCart",
     *      tags={"Shopping Cart"},
     *      summary="Add book to cart",
     *      description="Add a book to the shopping cart",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="book_id", type="integer", example=1),
     *              @OA\Property(property="quantity", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Book added to cart successfully"),
     *              @OA\Property(property="data", ref="#/components/schemas/ShoppingCart")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Validation error"),
     *              @OA\Property(property="data", example=null)
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
    public function addToCart(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = auth()->user();
        $book = Book::findOrFail($request->book_id);

        $cartItem = ShoppingCart::where('user_id', $user->id)->where('book_id', $request->book_id)->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cartItem = ShoppingCart::create([
                'user_id' => $user->id,
                'book_id' => $request->book_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json([
            'isSuccess' => true,
            'message' => 'Book added to cart successfully',
            'data' => $cartItem
        ], 200);
    }

    /**
     * @OA\Put(
     *      path="/api/cart/{id}",
     *      operationId="updateCart",
     *      tags={"Shopping Cart"},
     *      summary="Update book quantity in cart",
     *      description="Update the quantity of a book in the shopping cart",
     *      @OA\Parameter(
     *          name="id",
     *          description="Cart Item ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="quantity", type="integer", example=2)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Cart updated successfully"),
     *              @OA\Property(property="data", ref="#/components/schemas/ShoppingCart")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Validation error"),
     *              @OA\Property(property="data", example=null)
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
    public function updateCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $cartItem = ShoppingCart::findOrFail($id);
            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            return response()->json([
                'isSuccess' => true,
                'message' => 'Cart updated successfully',
                'data' => $cartItem
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/cart/{id}",
     *      operationId="removeFromCart",
     *      tags={"Shopping Cart"},
     *      summary="Remove book from cart",
     *      description="Remove a book from the shopping cart",
     *      @OA\Parameter(
     *          name="id",
     *          description="Cart Item ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Book removed from cart successfully"),
     *              @OA\Property(property="data", example=null)
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
    public function removeFromCart($id)
    {
        try {
            $cartItem = ShoppingCart::findOrFail($id);
            $cartItem->delete();

            return response()->json([
                'isSuccess' => true,
                'message' => 'Book removed from cart successfully',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/cart",
     *      operationId="viewCart",
     *      tags={"Shopping Cart"},
     *      summary="View cart",
     *      description="View all items in the shopping cart",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Cart retrieved successfully"),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ShoppingCart"))
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
    public function viewCart()
    {
        try {
            $user = auth()->user();
            $cartItems = ShoppingCart::where('user_id', $user->id)->get();

            return response()->json([
                'isSuccess' => true,
                'message' => 'Cart retrieved successfully',
                'data' => $cartItems
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
