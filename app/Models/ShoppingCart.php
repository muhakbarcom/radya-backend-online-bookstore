<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ShoppingCart",
 *     title="ShoppingCart",
 *     description="ShoppingCart model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the shopping cart item",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user who owns the shopping cart",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="book_id",
 *         type="integer",
 *         description="ID of the book in the shopping cart",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         description="Quantity of the book",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Shopping cart item created at",
 *         example="2021-08-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Shopping cart item updated at",
 *         example="2021-08-01T00:00:00Z"
 *     )
 * )
 */
class ShoppingCart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'book_id',
        'quantity',
    ];

    /**
     * Get the user that owns the shopping cart.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that is in the shopping cart.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
