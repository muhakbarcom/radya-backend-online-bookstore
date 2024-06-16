<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/users",
     *     operationId="getUsersList",
     *     tags={"User"},
     *     summary="Get list of users",
     *     description="Returns list of users",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json([
                'isSuccess' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
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
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/users",
     *     operationId="createUser",
     *     tags={"User"},
     *     summary="Create a new user",
     *     description="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'isSuccess' => true,
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/users/{id}",
     *     operationId="getUserById",
     *     tags={"User"},
     *     summary="Get user information",
     *     description="Get user information by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'isSuccess' => false,
                    'message' => 'User not found',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'isSuccess' => true,
                'message' => 'User retrieved successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/users/{id}",
     *     operationId="updateUser",
     *     tags={"User"},
     *     summary="Update user information",
     *     description="Update user information",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'isSuccess' => false,
                    'message' => 'User not found',
                    'data' => null,
                ], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'required|string|min:8',
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'isSuccess' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     operationId="deleteUser",
     *     tags={"User"},
     *     summary="Delete user",
     *     description="Delete user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User deleted successfully"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'isSuccess' => false,
                    'message' => 'User not found',
                    'data' => null,
                ], 404);
            }

            $user->delete();

            return response()->json([
                'isSuccess' => true,
                'message' => 'User deleted successfully',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Update user profile.
     *
     * @OA\Put(
     *     path="/api/user/profile",
     *     operationId="updateUserProfile",
     *     tags={"User"},
     *     summary="Update user profile",
     *     description="Update user profile",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="isSuccess", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *             @OA\Property(property="data", example=null)
     *         )
     *     )
     * )
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = User::find(auth()->user()->id);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            return response()->json([
                'isSuccess' => true,
                'message' => 'Profile updated successfully',
                'data' => $user,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Validation error',
                'data' => null,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
