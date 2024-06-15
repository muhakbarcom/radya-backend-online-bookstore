<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/books",
     *      operationId="getBooksList",
     *      tags={"Books"},
     *      summary="Get list of books",
     *      description="Returns list of books",
     *      @OA\Parameter(
     *          name="genre",
     *          description="Genre of the book",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="author",
     *          description="Author of the book",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example="true", description="Status of the request"),
     *             @OA\Property(property="message", type="string", example="Books retrieved successfully", description="Message of the request"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Book"))
     *          )
     * )
     *       ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *            @OA\Property(property="isSuccess", type="boolean", example="false", description="Status of the request"),
     *            @OA\Property(property="message", type="string", example="Internal server error", description="Message of the request"),
     *            @OA\Property(property="data", type="object", example="null"
     * )
     *       )
     *     )
     */
    public function index(Request $request)
    {
        try {
            $query = Book::query();

            // Filtering by genre
            if ($request->has('genre')) {
                $query->where('genre', $request->genre);
            }

            // Filtering by author
            if ($request->has('author')) {
                $query->where('author', $request->author);
            }

            $books = $query->get();

            return response()->json([
                'isSuccess' => true,
                'message' => 'Books retrieved successfully',
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
     *     path="/api/books",
     *     operationId="storeBook",
     *     tags={"Books"},
     *     summary="Store new book",
     *     description="Store new book",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"title","author","genre","price","quantity"},
     *            @OA\Property(property="title", type="string", example="Book Title"),
     *            @OA\Property(property="author", type="string", example="Author Name"),
     *            @OA\Property(property="genre", type="string", example="Genre"),
     *            @OA\Property(property="price", type="number", format="float", example="100.00"),
     *            @OA\Property(property="quantity", type="integer", example="10")
     * )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example="true", description="Status of the request"),
     *             @OA\Property(property="message", type="string", example="Book created successfully", description="Message of the request"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Book")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *           @OA\Property(property="isSuccess", type="boolean", example="false", description="Status of the request"),
     *           @OA\Property(property="message", type="string", example="The given data was invalid.", description="Message of the request"),
     *           @OA\Property(property="data", type="object", example="null")
     * )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *           @OA\Property(property="isSuccess", type="boolean", example="false", description="Status of the request"),
     *           @OA\Property(property="message", type="string", example="Internal server error", description="Message of the request"),
     *           @OA\Property(property="data", type="object", example="null")
     * )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'genre' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $book = Book::create($request->all());

            return response()->json([
                'isSuccess' => true,
                'message' => 'Book created successfully',
                'data' => $book
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }


    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'genre' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        try {


            $book->update($request->all());

            return response()->json([
                'isSuccess' => true,
                'message' => 'Book updated successfully',
                'data' => $book
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }


    public function destroy(Book $book)
    {
        try {
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
