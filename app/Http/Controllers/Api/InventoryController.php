<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/inventory",
     *      operationId="getInventoryList",
     *      tags={"Inventory"},
     *      summary="Get list of inventory items",
     *      description="Returns list of inventory items",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Inventory retrieved successfully"),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Book"))
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Internal server error"),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Book"))
     *          )
     *      )
     * )
     */
    public function index()
    {
        try {
            $books = Book::all();
            return response()->json([
                'isSuccess' => true,
                'message' => 'Inventory retrieved successfully',
                'data' => $books
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
     * @OA\Post(
     *      path="/api/inventory/{id}/add-stock",
     *      operationId="addInventoryStock",
     *      tags={"Inventory"},
     *      summary="Add stock to inventory",
     *      description="Add stock to inventory",
     *      @OA\Parameter(
     *          name="id",
     *          description="Book ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="quantity", type="integer", example=5)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Stock added successfully"),
     *              @OA\Property(property="data", ref="#/components/schemas/Book")
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
    public function addStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $book = Book::findOrFail($id);
            $book->quantity += $request->quantity;
            $book->save();

            return response()->json([
                'isSuccess' => true,
                'message' => 'Stock added successfully',
                'data' => $book
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
     * @OA\Post(
     *      path="/api/inventory/{id}/reduce-stock",
     *      operationId="reduceInventoryStock",
     *      tags={"Inventory"},
     *      summary="Reduce stock from inventory",
     *      description="Reduce stock from inventory",
     *      @OA\Parameter(
     *          name="id",
     *          description="Book ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="quantity", type="integer", example=5)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Stock reduced successfully"),
     *              @OA\Property(property="data", ref="#/components/schemas/Book")
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
    public function reduceStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $book = Book::findOrFail($id);

            if ($book->quantity < $request->quantity) {
                return response()->json([
                    'isSuccess' => false,
                    'message' => 'Not enough stock available',
                    'data' => null
                ], 400);
            }

            $book->quantity -= $request->quantity;
            $book->save();

            return response()->json([
                'isSuccess' => true,
                'message' => 'Stock reduced successfully',
                'data' => $book
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
     *      path="/api/inventory/{id}",
     *      operationId="deleteInventoryItem",
     *      tags={"Inventory"},
     *      summary="Delete a book from inventory",
     *      description="Delete a book from inventory",
     *      @OA\Parameter(
     *          name="id",
     *          description="Book ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="isSuccess", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Book deleted successfully"),
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
    public function deleteBook($id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();

            return response()->json([
                'isSuccess' => true,
                'message' => 'Book deleted successfully',
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
}
